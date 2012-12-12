<?php
class Sweepstakes extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $email_list_id, $key, $before, $after, $start_date, $end_date, $contest_rules_url, $share_title, $share_image_url, $share_text, $date_created;

    // Artifical columns
    public $content, $valid;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_sweepstakes' );
    }

    /**
	 * Get Tab
	 *
	 * @param string $fb_page_id
     * @param bool $liked
	 * @return Sweepstakes
	 */
	public function get_tab( $fb_page_id, $liked ) {
        // Determine the field
		if ( $liked ) {
			$fields = '`after` AS content, `contest_rules_url`, IF ( NOW() > `start_date` AND NOW() < `end_date`, 1, 0 ) AS valid, `share_title`, `share_image_url`, `share_text`';
		} else {
			$fields = '`before` AS content, 1 AS valid';
		}

		return $this->prepare(
            "SELECT $fields FROM `sm_sweepstakes` WHERE `fb_page_id` = :fb_page_id"
            , 'i'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'Sweepstakes' );
	}

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return stdClass
	 */
	public function get_connected_website( $fb_page_id ) {
		return $this->prepare(
            'SELECT w.`title`, sms.`key` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_sweepstakes` AS sms ON ( sms.`sm_facebook_page_id` = smfbp.`id` ) WHERE sms.`fb_page_id` = :fb_page_id'
            , 'i'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_OBJ );
	}

    /**
     * Connect
     *
     * @param int $fb_page_id
     * @param string $key
     */
    public function connect( $fb_page_id, $key ) {
        parent::update( array(
            'fb_page_id' => $fb_page_id
        ), array(
            'key' => $key
        ), 'i', 's' );
    }

    /**
     * Get Domain
     *
     * @param int $fb_page_id
     * @return stdClass
     */
    protected function get_account( $fb_page_id ) {
        return $this->prepare(
            'SELECT w.`website_id` AS id, w.`domain`, sms.`email_list_id` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_sweepstakes` AS sms ON ( sms.`sm_facebook_page_id` = smfbp.`id` ) WHERE smfbp.`status` = 1 AND sms.`fb_page_id` = :fb_page_id'
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_OBJ );
    }

    /**
	 * Adds an email to the appropriate categories
	 *
	 * @param int $fb_page_id
	 * @param string $name
	 * @param string $email_address
	 */
	public function add_email( $fb_page_id, $name, $email_address ) {
        // We only want lowercase email addresses
		$email_address = strtolower( $email_address );

        // Get account
        $account = $this->get_account( $fb_page_id );

		// We need to get the email_id
        $email = new Email();
        $email->get_by_email( $account->id, $email_address );

        // The status needs to be 1 in either case of existence or lack thereof
        $email->status = 1;

        // Add or update email address
		if ( $email->id ) {
            $email->save();
		} else {
            $email->website_id = $account->id;
            $email->name = $name;
            $email->email = $email_address;
            $email->create();
		}

		// Get default email list id
        $email_list = new EmailList();
        $email_list->get_default_email_list( $account->id );
		
        // Add association
        $email->add_associations( array( $email_list->id, $account->email_list_id ) );
	}
}