<?php
class EmailMessage extends ActiveRecordBase {
    public $id, $email_message_id, $email_template_id, $mc_campaign_id, $subject, $message, $type, $status, $date_sent, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_messages' );

        // We want to make sure they match
        if ( isset( $this->email_message_id ) )
            $this->id = $this->email_message_id;
    }

    /**
	 * Update scheduled emails
	 *
	 * This function assumes MailChimp will send the email at the right time.
	 * We simply mark it as sent when it has past the date it is SUPPOSED to send
	 *
	 * @return bool
	 */
	public function update_scheduled_emails() {
		$this->query( "UPDATE `email_messages` SET `status` = 2 WHERE `status` = 1 AND `date_sent` < NOW()" );
    }
}
