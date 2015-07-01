<?php

class ProductOptionItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $product_option_id, $name;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_option_item' );
    }

    /**
     * Creates a Produt Option Item
     */
    public function create() {
        $this->id = $this->insert([
            'product_option_id' => $this->product_option_id
            , 'name' => $this->name
        ], 'is' );
    }

    /**
     * Get by product option
     *
     * @param int $product_option_id
     * @return ProductOptionItem[]
     */
    public function get_by_product( $product_option_id ) {
        return $this->prepare(
            'SELECT * FROM `product_option_item` WHERE `product_option_id` = :product_option_id'
            , 'i'
            , array( ':product_option_id' => $product_option_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOptionItem' );
    }
}