<?php
class EmailMarketing extends ActiveRecordBase {
    /**
     * @var ActiveCampaignAPI @ac
     */
    protected $ac;

    /**
     * Setup the account initial data
     *
     * @param Account $account [optional]
     */
    public function __construct( Account $account = NULL ) {
        parent::__construct( '' );

        if ( !is_null( $account ) ) {
            $settings = $account->get_settings( 'ac-api-key', 'ac-api-url', 'ac-account', 'ac-username', 'ac-password' );

            if ( empty( $settings['ac-api-key'] ) ) {
                library('ac/ActiveCampaign.class');
                $ac = new ActiveCampaign( $settings['ac-account'] . Config::key('ac-account-domain'), null, $settings['ac-username'], $settings['ac-password'] );
                $ac_user = $ac->api('user/me');

                $settings['ac-api-url'] = $ac_user->apiurl;
                $settings['ac-api-key'] = $ac_user->apikey;

                // Save the settings
                $account->set_settings( $settings );

            }

            library('ac-api');
            $this->ac = new ActiveCampaignAPI( $settings['ac-api-url'], $settings['ac-api-key'] );
        }
    }

    /**
	 * Synchronize email lists
     *
     * @param Account $account
	 */
	public function synchronize_email_lists( Account $account ) {
        // Look through remote lists
        $this->ac->setup_list();
        $ac_lists = $this->ac->list->list_all();

        // Look through local lists
        $email_list = new EmailList();
        $lists = $email_list->get_by_account( $account->id );

        // Initialize variables
        $ac_list_ids = $synced_ac_list_ids = $ac_remaining_list_ids = array();

        // Create a list of IDS
        foreach ( $ac_lists as $acl ) {
            if ( !is_object( $acl ) )
                continue;

            $ac_list_ids[] = $acl->id;
        }

        // Create any lists
        foreach ( $lists as $list ) {
            if ( in_array( $list->ac_list_id, $ac_list_ids ) ) {
                $synced_ac_list_ids[] = $list->ac_list_id;
            } else {
                $this->ac->list->add( $list->name, $account->ga_profile_id, url::domain( $account->domain, false ) );
            }
        }

        // Get the remaining list ids that need to be removed
        $ac_remaining_list_ids = array_diff( $ac_list_ids, $synced_ac_list_ids );

        if ( !empty( $ac_remaining_list_ids ) )
            $this->ac->list->delete_multiple( $ac_remaining_list_ids );
	}

    /***** PROTECTED FUNCTIONS *****/

    /**
     * Get Mailchimp > Website Index
     *
     * @return array
     */
    protected function get_mailchimp_website_index() {
        return ar::assign_key( $this->get_results( "SELECT `website_id`, `mc_list_id` FROM `websites` WHERE `mc_list_id` <> '0' AND `email_marketing` <> 0", PDO::FETCH_ASSOC ), 'website_id', true );
    }

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
     * Get Unsynced Emails
     *
     * @return array
     */
    protected function get_unsynced_emails() {
        return $this->get_results( "SELECT e.`email`, e.`email_id`, e.`name`, GROUP_CONCAT( el.`name` ) AS interests, w.`mc_list_id`, w.`website_id` FROM `emails` AS e INNER JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) INNER JOIN `email_lists` AS el ON ( el.`email_list_id` = ea.`email_list_id` ) INNER JOIN `websites` AS w ON ( w.`website_id` = el.`website_id` ) WHERE e.`status` = 1 AND ( e.`date_synced` = '0000-00-00 00:00:00' OR e.`timestamp` > e.`date_synced` ) AND w.`email_marketing` = 1 GROUP BY el.`website_id`, e.`email`", PDO::FETCH_OBJ );
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
