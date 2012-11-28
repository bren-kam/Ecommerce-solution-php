<?php
class ShareAndSave extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $email_list_id, $maximum_email_list_id, $key, $before, $after, $minimum, $maximum, $share_title, $share_image_url, $share_text, $date_created;

    // Artifical columns
    public $content, $total;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_share_and_save' );
    }

    /**
	 * Get Tab
	 *
	 * @param string $fb_page_id
     * @param bool $liked
	 * @return ShareAndSave
	 */
	public function get_tab( $fb_page_id, $liked ) {
        // Determine the field
		if ( $liked ) {
			$fields = 'smsas.`after` AS content, smsas.`minimum`, smsas.`maximum`, smsas.`share_title`, smsas.`share_image_url`, smsas.`share_text`, COUNT( ea.`email_id` ) AS total';
		} else {
			$fields = 'smsas.`before` AS content, smsas.`minimum`, smsas.`maximum`, COUNT( ea.`email_id` ) AS total';
		}

		return $this->prepare(
            "SELECT $fields FROM `sm_share_and_save` AS smsas LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = smsas.`email_list_id` ) WHERE smsas.`fb_page_id` = :fb_page_id"
            , 'i'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'ShareAndSave' );
	}

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return stdClass
	 */
	public function get_connected_website( $fb_page_id ) {
		return $this->prepare(
            'SELECT w.`title`, smsas.`key` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_share_and_save` AS smsas ON ( smsas.`sm_facebook_page_id` = smfbp.`id` ) WHERE smsas.`fb_page_id` = :fb_page_id'
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
     * @return Account
     */
    protected function get_account( $fb_page_id ) {
        return $this->prepare(
            'SELECT w.`website_id`, w.`domain` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_email_sign_up` AS smesu ON ( smesu.`sm_facebook_page_id` = smfbp.`id` ) WHERE smfbp.`id`.`status` = 1 AND smesu.`fb_page_id` = :fb_page_id'
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'Account' );
    }

    /**
	 * Adds an email to the appropriate categories
	 *
	 * @param int $fb_page_id
	 * @param string $name
	 * @param string $email_address
	 * @return bool
	 */
	public function add_email( $fb_page_id, $name, $email_address ) {
        // We only want lowercase email addresses
		$email_address = strtolower( $email_address );

        // Get account
        $account = $this->get_account( $fb_page_id );

		// We need to get the email_id
        $email = new Email();
        $email->get_email_by_email( $account->id, $email_address );

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
        $email->add_associations( array( $email_list->id, $this->get_email_list_id( $fb_page_id ) ) );
	}

    /**
     * Get Email List ID
     *
     * @param int $fb_page_id
     * @return int
     */
    protected function get_email_list_id( $fb_page_id ) {
        return $this->prepare(
            'SELECT IF( COUNT( d.`email_id` ) >= smsas.`maximum`, smsas.`maximum_email_list_id`, smsas.`email_list_id` ) AS email_list_id FROM `sm_share_and_save` AS smsas ON ( b.`id` = c.`sm_facebook_page_id` ) LEFT JOIN `email_associations` AS ea ON ( ea.`email_list_id` = smsas.`email_list_id` ) WHERE smsas.`fb_page_id` = :fb_page_id'
            , 'i'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_var();
    }
}