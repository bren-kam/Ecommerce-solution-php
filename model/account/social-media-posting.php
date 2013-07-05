<?php
class SocialMediaPosting extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_user_id, $fb_page_id, $key, $access_token, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_posting' );
    }

    /**
     * Get
     *
     * @param int $sm_facebook_page_id
     */
    public function get( $sm_facebook_page_id ) {
        $this->prepare(
            'SELECT `sm_facebook_page_id`, `fb_user_id`, `fb_page_id`,  `key`, `access_token` FROM `sm_posting` WHERE `sm_facebook_page_id` = :sm_facebook_page_id'
            , 'i'
            , array( ':sm_facebook_page_id' => $sm_facebook_page_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'sm_facebook_page_id' => $this->sm_facebook_page_id
            , 'key' => $this->key
            , 'date_created' => $this->date_created
        ), 'iss' );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'fb_user_id' => $this->fb_user_id
            , 'fb_page_id' => $this->fb_page_id
            , 'access_token' => $this->access_token
        ), array(
            'sm_facebook_page_id' => $this->sm_facebook_page_id
        ), 'iis', 'i' );
    }
}