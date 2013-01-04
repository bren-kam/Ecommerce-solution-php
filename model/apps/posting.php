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

		return $website_id != 0;
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
     *
     * @param int $fb_user_id
     * @param int $fb_page_id
     * @param string $access_token
     * @param string $key
     */
    public function connect( $fb_user_id, $fb_page_id, $access_token, $key ) {
        parent::update( array(
            'fb_user_id' => $fb_user_id
            , 'fb_page_id' => $fb_page_id
            , 'access_token' => $access_token
        ), array(
            'key' => $key
        ), 'iis', 's' );
    }

    /**
     * Update Token
     *
     * @param string $access_token
     * @param int $fb_page_id
     */
    public function update_access_token( $access_token, $fb_page_id ) {
        parent::update( array(
           'access_token' => $access_token
        ), array(
            'fb_page_id' => $fb_page_id
        ), 'is', 'i' );
    }
}