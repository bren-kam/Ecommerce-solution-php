<?php
class Posting extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_user_id, $fb_page_id, $website_page_id, $key, $access_token, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_posting' );
    }

    /**
	 * Determine if user is connected
	 *
	 * @param int $fb_user_id
	 * @return bool
	 */
	public function connected( $fb_user_id ) {
		// See if there is a website_id associated with the user
		$website_id = $this->prepare(
            'SELECT smfbp.`website_id` FROM `sm_facebook_page` AS smfbp LEFT JOIN `sm_posting` AS smp ON ( smp.`sm_facebook_page_id` = smfbp.`id` ) WHERE smfbp.`status` = 1 AND smp.`fb_user_id` = :fb_user_id'
            , 'i'
            , array( ':fb_user_id' => $fb_user_id )
        )->get_var();

		return $website_id > 0;
	}

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_user_id
	 * @return array
	 */
	public function get_connected_pages( $fb_user_id ) {
		return $this->prepare(
            'SELECT `fb_page_id` FROM `sm_posting` WHERE `fb_user_id` = :fb_user_id'
            , 'i'
            , array( ':fb_user_id' => $fb_user_id )
        )->get_col();
	}

    /**
     * Connect
     */
    public function connect() {
        parent::update( array(
            'fb_user_id' => $this->fb_user_id
            , 'fb_page_id' => $this->fb_page_id
            , 'access_token' => $this->access_token
        ), array(
            'key' => $this->key
        ), 'iis', 's' );
    }

    /**
     * Update Token
     */
    public function update_token() {
        parent::update( array(
            'fb_user_id' => $this->fb_user_id
            , 'access_token' => $this->access_token
        ), array(
            'fb_page_id' => $this->fb_page_id
        ), 'is', 'i' );
    }
}