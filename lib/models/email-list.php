<?php
class EmailList extends ActiveRecordBase {
    public $id, $email_list_id, $category_id, $website_id, $ac_list_id, $name, $description, $date_created;

    // Artifical fields
    public $count;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_lists' );

        // We want to make sure they match
        if ( isset( $this->email_list_id ) )
            $this->id = $this->email_list_id;
    }

    /**
     * Get Email List
     *
     * @param int $email_list_id
     * @param int $account_id
     */
    public function get( $email_list_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `email_lists` WHERE `email_list_id` = :email_list_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':email_list_id' => $email_list_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
		
		$this->id = $this->email_list_id;
    }

    /**
     * Get Default Email List
     *
     * @param int $account_id
     */
    public function get_default_email_list( $account_id ) {
        $this->prepare(
            'SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = :account_id AND `category_id` = 0'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

		$this->id = $this->email_list_id;
    }

    /**
     * Get Email lists by account
     *
     * @param int $account_id
     * @return EmailList[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_list_id`, `category_id`, `ac_list_id`, `name` FROM `email_lists` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Get Email lists by account
     *
     * @param int $account_id
     * @return EmailList[]
     */
    public function get_count_by_account( $account_id ) {
        return $this->prepare(
            'SELECT el.`email_list_id`, el.`category_id`, el.`name`, COUNT( DISTINCT ea.`email_id` ) AS count FROM `email_lists` AS el LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` ) WHERE el.`website_id` = :account_id AND e.`status` = 1 GROUP BY el.`email_list_id` ORDER BY el.`name`'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Get Email lists by account
     *
     * @param int $email_message_id
     * @param int $account_id
     * @return EmailList[]
     */
    public function get_by_message( $email_message_id, $account_id ) {
        return $this->prepare(
            'SELECT el.`email_list_id`, el.`name` FROM `email_lists` AS el LEFT JOIN `email_message_associations` AS ema ON ( ema.`email_list_id` = el.`email_list_id` ) LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` ) WHERE el.`website_id` = :account_id AND ema.`email_message_id` = :email_message_id GROUP BY el.`email_list_id`'
            , 'ii'
            , array( ':email_message_id' => $email_message_id, ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
    }

    /**
     * Create Email List
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => $this->name
            , 'description' => $this->description
            , 'date_created' => $this->date_created
        ), 'isss' );

        $this->id = $this->email_list_id = $this->get_insert_id();
    }

    /**
     * Save Email List
     */
    public function save() {
        $this->update( array(
            'ac_list_id' => $this->ac_list_id
            , 'name' => $this->name
            , 'description' => $this->description
        ), array(
            'email_list_id' => $this->id
        ), 'iss', 'i' );
    }

    /**
     * Synchronize email lists
     *
     * @param Account $account
     * @return bool
     */
    public function synchronize( $account ) {
        $this->remove_bad_emails( $account );
        $this->update_bulk( $account );
    }

    /**
     * Removes unsubscribes
     *
     * @throws ModelException
     *
     * @param Account $account
     */
    protected function remove_bad_emails( Account $account ) {
        // Make sure they have a mailchimp list id to work off
        if ( !$account->mc_list_id )
            return;

        // Get mailchimp
        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );

        $date = new DateTime();
        $date->sub( new DateInterval('P1D') );

        // Get the unsubscribers
        $unsubscribers = $mc->listMembers( $account->mc_list_id, 'unsubscribed', $date->format('Y-m-d H:i:s'), 0, 15000 );

        // Error Handling
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        $emails = array();

        if ( is_array( $unsubscribers ) )
        foreach ( $unsubscribers as $unsub ) {
            $emails[] = $unsub['email'];
        }

        $email = new Email();
        $email->unsubscribe_bulk( $emails, $account->id );

        // Get the cleaned
        $cleaned = $mc->listMembers( $account->mc_list_id, 'cleaned', $date->format('Y-m-d H:i:s'), 0, 10000 );

        // Error Handling
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        $emails = array();

        if ( is_array( $cleaned ) )
        foreach ( $cleaned as $clean ) {
            $emails[] = $clean['email'];
        }

        $email->clean_bulk( $emails, $account->id );
    }

    /**
     * Updates email lists
     *
     * @throws ModelException
     *
     * @param Account $account
     */
    protected function update_bulk( Account $account ) {
        $email = new Email();
        $email_results = $email->get_unsynced_by_account( $account->id );

        // If there isn't anything, continue
        if ( empty( $email_results ) )
            return;

        // Create array
        $email_lists = $email_interests = $emails = array();

        // We know an array exists or we would have aborted above
        foreach ( $email_results as $er ) {
            $emails[$er->id] = array(
                'EMAIL' => $er->email,
                'EMAIL_TYPE' => 'html',
                'FNAME' => $er->name,
                'INTERESTS' => $er->interests
            );

            $email_interests = ( isset( $email_interests ) ) ? array_merge( $email_interests, explode( ',', $er->interests ) ) : explode( ',', $er->interests );
        }

        // Create array to hold email ids
        $synced_email_ids = array();

        // Get the unique interests
        $interests = array_unique( $email_interests );

        // Get mailchimp
        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );

        $groups_result = $mc->listInterestGroups( $account->mc_list_id );

        // Error Handling
        if ( $mc->errorCode ) {
			switch ( $mc->errorCode ) {
				case 211: // "This list does not have interest groups enabled" (they have no email groups"
					// Don't care
				break;
				
				default:
					throw new ModelException( $mc->errorMessage, $mc->errorCode );
				break;
			}
		}

        if ( !isset( $groups_result['groups'] ) )
            $groups_result['groups'] = array();

        foreach ( $interests as $i ) {
            if ( !in_array( $i, $groups_result['groups'] ) ) {
                $mc->listInterestGroupAdd( $account->mc_list_id, $i );

                // Error Handling
                if ( $mc->errorCode )
                    throw new ModelException( $mc->errorMessage, $mc->errorCode );
            }
        }

        // list_id, batch of emails, require double optin, update existing users, replace interests
        $vals = $mc->listBatchSubscribe( $account->mc_list_id, $emails, false, true, true );

        if ( $mc->errorCode ) {
            throw new ModelException( $mc->errorMessage, $mc->errorCode );
        } else {
            // Handle errors if there were any
            if ( $vals['error_count'] > 0 ) {
                $errors = '';
                $unsubscribe_emails = array();

                foreach ( $vals['errors'] as $val ) {
                    switch( $val['code'] ) {
						case 212: // Unsubscribed email
                        case 213: // Bounced email
						case 220: // Banned
                            $unsubscribe_emails[] = $val['row']['EMAIL'];
                        break;

                        default:
                            $errors .= "Email: " . $val['row']['EMAIL'] . "\nCode: " . $val['code'] . "\nError Message: " . $val['message'] . "\n\n";
                        break;
                    }
                }

                if ( !empty( $unsubscribe_emails ) )
                    $email->unsubscribe_bulk( $unsubscribe_emails, $account->id );

                if ( !empty( $errors ) )
                    throw new ModelException( $errors );
            }

            $synced_email_ids = array_keys( $emails );
        }

        // Set all the emails that were updated to say they were updated
        if ( count( $synced_email_ids ) > 1 )
            $email->sync_bulk( $synced_email_ids );
    }

    /**
     * Remove All
     *
     * @throws ModelException
     *
     * @param Account $account
     */
    public function remove_all( Account $account ) {
        if ( $account->mc_list_id ) {
            library( 'MCAPI' );
            $mc = new MCAPI( Config::key('mc-api') );

            // Delete list gruop
            $mc->listInterestGroupDel( $account->mc_list_id, $this->name );

            // Error Handling
            if ( $mc->errorCode )
                throw new ModelException( $mc->errorMessage, $mc->errorCode );
        }

        $this->remove();
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'email_list_id' => $this->email_list_id
        ), 'i' );
    }

    /**
     * List All
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailList[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT el.`email_list_id`, el.`name`, el.`description`, el.`date_created`, IF( 1 = e.`status`, COUNT( DISTINCT ea.`email_id` ), 0 ) AS count FROM `email_lists` AS el LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` AND e.`status` = 1 ) WHERE 1 $where GROUP BY el.`email_list_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailList' );
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
            "SELECT COUNT( DISTINCT el.`email_list_id` ) FROM `email_lists` AS el LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = el.`email_list_id` ) LEFT JOIN `emails` AS e ON ( e.`email_id` = ea.`email_id` AND e.`status` = 1 ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
