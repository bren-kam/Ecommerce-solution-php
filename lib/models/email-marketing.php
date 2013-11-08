<?php
class EmailMarketing extends ActiveRecordBase {
    /**
     * @var ActiveCampaignAPI
     */
    protected $ac;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
    }

    /**
     * Add Email List
     *
     * @throws ModelException
     *
     * @param Account $account
     * @param EmailList $email_list
     * @return int
     */
    public function add_email_list( Account $account, EmailList $email_list ) {
        if ( !$this->ac instanceof ActiveCampaignAPI )
            $this->ac = $this->setup_ac( $account );

        // Setup what we need
        $this->ac->setup_list();
        $this->ac->setup_webhook();

        extract( $account->get_settings( 'address', 'city', 'state', 'zip' ) );

        $ac_list_id = $this->ac->list->add( $email_list->name, $account->ga_profile_id, url::domain( $account->domain, false ), $account->title, $address, $city, $state, $zip );

        if ( !$ac_list_id )
            throw new ModelException( "Failed to create email list:\n" . $this->ac->message() );

        // Make sure we can work on webhooks
        $this->ac->setup_webhook();

        // Add unsubscribe webhook for this list
        $this->ac->webhook->add(
            'Unsubscribe Hook'
            , url::add_query_arg( 'aid', $account->id, 'http://admin.greysuitretail.com/hooks/ac/unsubscribe/' )
            , $ac_list_id
            , 'unsubscribe'
            , array( 'public', 'system', 'admin' )
        );

        // Add campaign sent webhook for this list
        $this->ac->webhook->add(
            'Campaign Sent Hook'
            , url::add_query_arg( 'aid', $account->id, 'http://admin.greysuitretail.com/hooks/ac/sent-campaign/' )
            , $ac_list_id
            , 'sent'
            , array( 'public', 'system', 'admin', 'api' )
        );

        return $ac_list_id;
    }

    /***** PROTECTED FUNCTIONS *****/

    /**
     * Bulk unsubscribe by account
     *
     * @param int $account_id
     * @param array $emails
     */
    protected function bulk_unsubscribe( $account_id, array $emails ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $email_count = count( $emails );
        $email_format = substr( str_repeat( ',?', $email_count ), 1 );

        $this->prepare(
            "UPDATE `emails` SET `status` = 0 WHERE `website_id` = $account_id AND `email` IN ( $email_format )"
            , str_repeat( 's', $email_count )
            , $emails
        );
    }

    /**
     * Bulk mark cleaned
     *
     * @param int $account_id
     * @param array $emails
     */
    protected function bulk_mark_cleaned( $account_id, array $emails ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $email_count = count( $emails );
        $email_format = substr( str_repeat( ',?', $email_count ), 1 );

        $this->prepare(
            "UPDATE `emails` SET `status` = 2 WHERE `website_id` = $account_id AND `email` IN ( $email_format )"
            , str_repeat( 's', $email_count )
            , $emails
        );
    }

    /**
     * Synchronize emails
     *
     * @param array $email_ids
     */
    protected function synchronize_emails( array $email_ids ) {
        // Type Juggling
        foreach ( $email_ids as &$email_id ) {
            $email_id = (int) $email_id;
        }

        $email_ids = implode( ',', $email_ids );

        // Mark these emails as synced
        $this->query( "UPDATE `emails` SET `date_synced` = NOW() WHERE `email_id` IN( $email_ids )" );
    }
}
