<?php
class AboutUs extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $website_page_id, $key, $content, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_about_us' );
    }

    /**
	 * Get Tab
	 *
	 * @param string $fb_page_id
	 * @return string
	 */
	public function get_tab( $fb_page_id ) {
		// Get the tab
		$tab_data = $this->get_tab_data( $fb_page_id );

		if ( 0 != $tab_data->website_page_id ) {
			// If there was a website page id, we need to get the content from elsewhere
			$account_page = $this->get_tab_page( $fb_page_id, $tab_data->website_page_id );

			// Form Tab
			$tab = '<h1>' . $account_page->title . '</h1>' . html_entity_decode( $account_page->content, ENT_QUOTES, 'UTF-8' );
		} else {
			$tab = $tab_data->content;
		}

		return $tab;
	}

    /**
     * Get Tab Data
     *
     * @param string $fb_page_id
     * @return AboutUs
     */
    protected function get_tab_data( $fb_page_id ) {
        return $this->prepare(
            'SELECT IF( 0 = `website_page_id`, `content`, 0 ) AS content, `website_page_id` FROM `sm_about_us` WHERE `fb_page_id` = :fb_page_id'
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'AboutUs' );
    }

    /**
     * Get Tab Page
     *
     * @param string $fb_page_id
     * @param int $website_page_id
     * @return AccountPage
     */
    protected function get_tab_page( $fb_page_id, $website_page_id ) {
        return $this->prepare(
            'SELECT wp.`title`, wp.`content` FROM `website_pages` AS wp LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = wp.`website_id` ) LEFT JOIN `sm_about_us` AS smau ON ( smau.`sm_facebook_page_id` = smfbp.`id` ) WHERE wp.`website_page_id` = :website_page_id AND smfbp.`status` = 1 AND smau.`fb_page_id` = :fb_page_id'
            , 'is'
            , array( ':website_page_id' => $website_page_id, ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'AccountPage' );
    }

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return stdClass
	 */
	public function get_connected_website( $fb_page_id ) {
		return $this->prepare(
            'SELECT w.`title`, smau.`key` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_about_us` AS smau ON ( smau.`sm_facebook_page_id` = smfbp.`id` ) WHERE smau.`fb_page_id` = :fb_page_id'
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
}