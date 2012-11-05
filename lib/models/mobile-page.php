<?php
class MobilePage extends ActiveRecordBase {
    public $id, $mobile_page_id, $website_id, $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $status, $updated_user_id, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'mobile_pages' );

        // We want to make sure they match
        if ( isset( $this->mobile_page_id ) )
            $this->id = $this->mobile_page_id;
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
            , 'date_created' => $this->date_created
        ), 'isss' );

        $this->id = $this->mobile_page_id = $this->get_insert_id();
    }
}
