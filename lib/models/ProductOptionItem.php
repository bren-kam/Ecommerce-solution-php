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
}