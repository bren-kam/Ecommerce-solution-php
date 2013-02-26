<?php
class EmailList extends ActiveRecordBase {
    public $id, $email_list_id, $category_id, $website_id, $name, $date_created;

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
            'SELECT `email_list_id`, `name` FROM `email_lists` WHERE `website_id` = :account_id'
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
            , 'date_created' => $this->date_created
        ), 'iss' );

        $this->id = $this->email_list_id = $this->get_insert_id();
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
    private function remove_bad_emails( Account $account ) {
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
    private function update_bulk( Account $account ) {
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
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

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

                foreach ( $vals['errors'] as $val ) {
                    $errors .= "Email: " . $val['row']['EMAIL'] . "\nCode: " . $val['code'] . "\nError Message: " . $val['message'] . "\n\n";
                }

                throw new ModelException( $errors );
            }

            $synced_email_ids = array_keys( $emails );
        }

        // Set all the emails that were updated to say they were updated
        if ( count( $synced_email_ids ) > 1 )
            $email->sync_bulk( $synced_email_ids );
    }
}
