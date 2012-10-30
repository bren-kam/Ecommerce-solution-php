<?php
class MobileMessage extends ActiveRecordBase {
    public $id, $mobile_message_id, $website_id, $title, $message, $status, $date_sent, $date_created, $date_updated;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'mobile_messages' );

        // We want to make sure they match
        if ( isset( $this->mobile_message_id ) )
            $this->id = $this->mobile_message_id;
    }

    /**
	 * Update scheduled emails
	 *
	 * This function assumes Trumpia will send the email at the right time.
	 * We simply mark it as sent when it has past the date it is SUPPOSED to send
	 *
	 * @return bool
	 */
	public function update_scheduled() {
		$this->query( "UPDATE `mobile_messages` SET `status` = 2 WHERE `status` = 1 AND `date_sent` < NOW()" );
    }
}
