<?php
class EmailMessage extends ActiveRecordBase {
    public $id, $email_message_id, $website_id, $email_template_id, $mc_campaign_id, $subject, $message, $type, $status, $date_sent, $date_created;

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
     * Get
     *
     * @param int $email_message_id
     * @param int $account_id
     */
    public function get( $email_message_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `email_messages` WHERE `email_message_id` = :email_message_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':email_message_id' => $email_message_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

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

    /**
     * Get Dashboard Messages By Account
     *
     * @param int $account_id
     * @return EmailMessage[]
     */
    public function get_dashboard_messages_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_message_id`, `mc_campaign_id`, `subject` FROM `email_messages` WHERE `website_id` = :account_id AND `status` = 2 ORDER BY `date_sent` DESC LIMIT 5'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * List Pages
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailMessage[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `email_message_id`, `mc_campaign_id`, `subject`, `status`, `date_sent` FROM `email_messages` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * Count all the pages
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( `email_message_id` ) FROM `email_messages` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
