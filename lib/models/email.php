<?php
class Email extends ActiveRecordBase {
    const STATUS_SUBSCRIBED = 1;
    const STATUS_UNSUBSCRIBED = 0;

    public $id, $email_id, $website_id, $email, $name, $phone, $status, $date_created, $date_unsubscribed, $date_synced, $timestamp;

    // Fields available from other tables
    public $mc_list_id;

    // Artificial field
    public $interests, $date;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'emails' );

        // We want to make sure they match
        if ( isset( $this->email_id ) )
            $this->id = $this->email_id;
    }

    /**
     * Get
     *
     * @param int $email_id
     * @param int $account_id
     * @return Email
     */
    public function get( $email_id, $account_id ) {
		$this->prepare( 'SELECT * FROM `emails` WHERE `email_id` = :email_id AND `website_id` = :account_id'
            , 'is'
            , array(
                ':account_id' => $account_id
                , ':email_id' => $email_id
            )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->email_id;
    }

    /**
     * Get Email by Email
     *
     * @param int $account_id
     * @param string $email
     */
    public function get_by_email( $account_id, $email ) {
		$this->prepare( 'SELECT `email_id`, `email`, `status` FROM `emails` WHERE `website_id` = :account_id AND `email` = :email'
            , 'is'
            , array(
                ':account_id' => $account_id
                , ':email' => $email
            )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->email_id;
    }

    /**
     * Get by email list
     *
     * @param int $email_list_id
     * @return Email[]
     */
    public function get_by_email_list( $email_list_id ) {
		return $this->prepare( 'SELECT e.`email` FROM `emails` AS e LEFT JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) WHERE e.`status` = :status AND ea.`email_list_id` = :email_list_id'
            , 'ii'
            , array(
                ':status' => self::STATUS_SUBSCRIBED
                , ':email_list_id' => $email_list_id
            )
        )->get_col();
    }

    /**
     * Get Dashboard Subscribers By Account
     *
     * @param int $account_id
     * @return Email[]
     */
    public function get_dashboard_subscribers_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_id`, `email` FROM `emails` WHERE `website_id` = :account_id AND `status` = :status ORDER BY `date_created` DESC LIMIT 5'
            , 'ii'
            , array( ':account_id' => $account_id, ':status' => self::STATUS_SUBSCRIBED )
        )->get_results( PDO::FETCH_CLASS, 'Email' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert(
            array(
                'website_id' => $this->website_id
                , 'email' => strip_tags($this->email)
                , 'name' => strip_tags($this->name)
                , 'status' => $this->status
                , 'date_created' => $this->date_created
            ), 'issis'
        );

        $this->email_id = $this->id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        parent::update(
            array(
                'name' =>strip_tags( $this->name)
                , 'email' => strip_tags($this->email)
                , 'phone' => strip_tags($this->phone)
                , 'status' => $this->status
            )
            , array( 'email_id' => $this->id )
            , 'sssi', 'i'
        );
    }

    /**
     * Remove Associations
     */
    public function remove_associations() {
        $this->prepare(
            'DELETE FROM `email_associations` WHERE `email_id` = :email_id'
            , 'i'
            , array( ':email_id' => $this->id )
        )->query();
    }

    /**
     * Remove All
     *
     * @param Account $account
     */
    public function remove_all( Account $account ) {
        if ( !$this->id )
            return;

        // Setup AC
        $settings = $account->get_settings( 'sendgrid-username', 'sendgrid-password' );
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
        $sendgrid->setup_email();
        $sendgrid->setup_unsubscribes();

        // Add to Global Unsubscribe List
        $sendgrid->unsubscribes->add( $this->email );

        // Remove email from all Lists
        $email_list = new EmailList();
        $email_lists = $email_list->get_by_email( $this->id, $account->id );

        foreach ( $email_lists as $email_list ) {
            $sendgrid->email->delete( $email_list->name, $this->email );
        }

        // Assuming the above is successful, delete everything about this email
        $this->remove_associations();

        $this->status = 0;
        $this->save();
    }

    /**
     * List Subscribers
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return Email[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT DISTINCT e.`email_id`, e.`name`, e.`email`, IF( 1 = e.`status`, e.`date_created`, e.`timestamp` ) AS date FROM `emails` AS e LEFT JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Email' );
    }

    /**
     * Aggregate Subscribers by Date
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return Email[]
     */
    public function aggregate_by_date( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT COUNT(e.`email_id`) AS total , DATE(e.`date_created`) AS date FROM `emails` AS e WHERE 1 $where $order_by GROUP BY date LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Count all
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT e.`email_id` ) FROM `emails` AS e LEFT JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }

    /**
     * Import
     *
     * @param int $account_id
     * @param array $emails
     */
    public function import_all( $account_id, array $emails ) {
        // Type Juggling
        $account_id = (int) $account_id;

        // Delete already imported emails
        $this->delete_imported( $account_id );

        // Select all the unsubscribed emails they already have
        $unsubscribed_emails = $this->list_all( array(
            ' AND e.`status` = 0 AND e.`website_id` = ' . (int) $account_id
            , array()
            , 'ORDER BY e.`date_created` ASC'
            , 100000
        ) );

        $unsubscribed = $values = array();

        foreach ( $unsubscribed_emails as $unsubscriber ) {
            $unsubscribed[] = $unsubscriber->email;
        }

        // Create string to insert new emails
        foreach ( $emails as $email ) {
            // Make sure they haven't been unsubscribed
            if ( in_array( $email['email'], $unsubscribed ) )
                continue;

            $values[$email['email']] = $email['name'];
        }

        $this->import( $account_id, $values );
    }

    /**
     * Import
     *
     * @param int $account_id
     * @param array $values
     */
    protected function import( $account_id, array $values ) {
        // Type Juggling
        $account_id = (int) $account_id;

        // Get chunks
        $chunks = array_chunk( $values, 500, true );

        foreach ( $chunks as $chunk ) {
            $chunk_count = count( $chunk );
            $value_string = substr( str_repeat( ",( $account_id, ?, ?, NOW() )", $chunk_count ), 1 );
            $values = array();

            foreach ( $chunk as $email => $name ) {
                $values[] = $email;
                $values[] = $name;
            }

            $this->prepare(
                "INSERT INTO `email_import_emails` ( `website_id`, `email`, `name`, `date_created` ) VALUES $value_string"
                , str_repeat( 'ss', $chunk_count )
                , $values
            )->query();
        }
    }


    /**
     * Complete import
     *
     * @param int $account_id
     * @param SendGridAPI
     * @param array $email_list_ids
     */
    public function complete_import( $account_id, SendGridAPI $sendgrid, array $email_list_ids ) {
        // Import the emails
        $this->import_emails( $account_id );

        // Add associations
        $this->add_associations_to_imported_emails( $account_id, $email_list_ids );

        $email_list = new EmailList();
        $email_lists = $email_list->get_by_ids( $email_list_ids, $account_id );
        $emails = $this->get_import_emails( $account_id );

        foreach ( $email_lists as $email_list ) {
            $email_chunks = array_chunk( $emails, 1000 );

            foreach ( $email_chunks as $email_set ) {
                if ( is_array( $email_set ) ) {
                    $sendgrid->email->add( $email_list->name, $email_set );
                } else {
                    $sendgrid->email->add( $email_list->name, array( $email_set ) );
                }
            }
        }

        // Delete the imported emails
        $this->delete_imported( $account_id );
    }

    /**
     * Complete Import
     *
     * @param int $account_id
     */
    protected function import_emails( $account_id ) {
        // Type Juggling
        $account_id = (int) $account_id;

    	// @Fix remove the subquery
		// @Fix need a way to remove these subscribers
		// Transfer new emails to emails table
		$this->query("INSERT INTO `emails` ( `website_id`, `email`, `name`, `date_created` ) SELECT `website_id`, `email`, `name`, NOW() FROM `email_import_emails` WHERE `website_id` = $account_id AND `email` NOT IN ( SELECT `email` FROM `emails` WHERE `website_id` = $account_id )" );
    }

    /**
     * Complete Import
     *
     * @param int $account_id
     * @return array
     */
    protected function get_import_emails( $account_id ) {
        // Type Juggling
        $account_id = (int) $account_id;

//		return $this->get_col("SELECT `email` FROM `email_import_emails` WHERE `website_id` = $account_id AND `email` NOT IN ( SELECT `email` FROM `emails` WHERE `website_id` = $account_id )" );
        return $this->get_col("SELECT `email` FROM `email_import_emails` WHERE `website_id` = $account_id" );
    }

    /**
     * Add associations to imported emails
     *
     * @param int $account_id
     * @param array $email_list_ids
     */
    protected function add_associations_to_imported_emails( $account_id, array $email_list_ids ) {
        // Add the associations for each list
        foreach ( $email_list_ids as $email_list_id ) {
            $this->prepare(
                'INSERT INTO `email_associations` ( `email_id`, `email_list_id` ) SELECT e.`email_id`, :email_list_id FROM `emails` AS e INNER JOIN `email_import_emails` AS eie ON ( eie.`email` = e.`email` ) WHERE e.`website_id` = :account_id ON DUPLICATE KEY UPDATE `email_id` = VALUES( `email_id` )'
                , 'ii'
                , array( ':email_list_id' => $email_list_id, ':account_id' => $account_id )
            )->query();
        }
    }

    /**
     * Delete imported
     *
     * @param int $account_id
     */
    public function delete_imported( $account_id ) {
        $this->prepare(
            'DELETE FROM `email_import_emails` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->query();
    }

    /***** ASSOCIATIONS *****/

    /**
     * Get Associations
     *
     * @return array
     */
    public function get_associations( ) {
        return $this->prepare(
            'SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = :email_id'
            , 'i'
            , array( ':email_id' => $this->id )
        )->get_col();
    }

    /**
     * Add Associations
     *
     * @param array $email_list_ids
     */
    public function add_associations( array $email_list_ids ) {
        if ( empty( $email_list_ids ) )
            return;

        $email_id = (int) $this->id;
        $values = array();

        foreach ( $email_list_ids as $elid ) {
            $elid = (int) $elid;

            $values[] = "( $email_id, $elid )";
        }

        $this->query( "INSERT INTO `email_associations` VALUES " . implode( ',', $values ) . ' ON DUPLICATE KEY UPDATE `email_list_id` = VALUES( `email_list_id` )' );
    }
}
