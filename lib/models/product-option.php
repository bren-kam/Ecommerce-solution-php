<?php
class ProductOption extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $product_id, $name, $type;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_option' );
    }

    /**
     * Create
     */
    public function create() {
        $this->id = $this->insert([
            'website_id' => $this->website_id
            , 'product_id' => $this->product_id
            , 'name' => $this->name
            , 'type' => $this->type
        ], 'iiss');
    }

    /**
     * Get by product
     *
     * @param int $website_id
     * @param int $product_id
     * @return ProductOption[]
     */
    public function get_by_product( $website_id, $product_id ) {
        return $this->prepare(
            'SELECT `id`, `name`, `type` FROM `product_option` WHERE `website_id` = :website_id AND `product_id` = :product_id'
            , 'ii'
            , array( ':website_id' => $website_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOption' );
    }
}
