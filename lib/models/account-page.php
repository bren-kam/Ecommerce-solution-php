<?php
class AccountPage extends ActiveRecordBase {
    public $id, $website_page_id, $website_id, $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $status, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_pages' );

        // We want to make sure they match
        if ( isset( $this->website_page_id ) )
            $this->id = $this->website_page_id;
    }

    /**
     * Get all
     *
     * @param int $account_id
     * @return array
     */
    public function get_all( $account_id ) {
        return $this->prepare(
            'SELECT `website_page_id`, `slug` FROM `website_pages` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'slug' => $this->slug
            , 'title' => $this->title
            , 'content' => $this->content
            , 'date_created' => $this->date_created
        ), 'issss' );

        $this->id = $this->website_page_id = $this->get_insert_id();
    }

    /**
     * Copy pages
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_by_account( $template_account_id, $account_id ) {

        $this->copy( $this->table, array(
                'website_id' => $account_id
                , 'slug' => NULL
                , 'title' => NULL
                , 'content' => NULL
                , 'meta_title' => NULL
                , 'meta_description' => NULL
                , 'meta_keywords' => NULL
                , 'mobile' => NULL
                , 'status' => 1
            ), array( 'website_id' => $template_account_id )
        );
    }
}
