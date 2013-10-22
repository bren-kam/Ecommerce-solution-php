<?php
class SocialMediaProducts extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $key, $content, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_products' );
    }

    /**
     * Get
     *
     * @param int $sm_facebook_page_id
     */
    public function get( $sm_facebook_page_id ) {
        $this->prepare(
            'SELECT `sm_facebook_page_id`, `fb_page_id`, `key`, `content` FROM `sm_products` WHERE `sm_facebook_page_id` = :sm_facebook_page_id'
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
            , 'key' => strip_tags($this->key)
            , 'date_created' => $this->date_created
        ), 'iss' );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'content' => $this->content
        ), array(
            'sm_facebook_page_id' => $this->sm_facebook_page_id
        ), 's', 'i' );
    }
}