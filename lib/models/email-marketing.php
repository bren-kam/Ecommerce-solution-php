<?php
class EmailMarketing extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
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
