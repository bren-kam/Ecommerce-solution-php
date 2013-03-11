<?php
class CraigslistTag extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $craigslist_tag_id, $object_id, $type;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'craigslist_tags' );

        // We want to make sure they match
        if ( isset( $this->craigslist_tag_id ) )
            $this->id = $this->craigslist_tag_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'craigslist_tag_id' => $this->craigslist_tag_id
            , 'object_id' => $this->object_id
            , 'type' => $this->type
        ), 'iis', true );

        $this->id = $this->craigslist_tag_id;
    }

    /**
     * Get By all IDS
     *
     * @param int $product_id
     * @param int $category_id
     * @param int $parent_category_id
     * @return CraigslistTag[]
     */
    public function get_by_all( $product_id, $category_id, $parent_category_id ) {
        return $this->prepare(
            "SELECT * FROM `craigslist_tags` WHERE ( `type` = 'category' AND `object_id` IN( :category_id, :parent_category_id ) ) OR ( `type` = 'product' AND `object_id` = :product_id )"
            , 'iii'
            , array( ':category_id' => $category_id, ':parent_category_id' => $parent_category_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'CraigslistTag' );
    }
}
