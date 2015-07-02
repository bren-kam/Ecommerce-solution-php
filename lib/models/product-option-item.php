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
     * Get by id
     *
     * @param int $product_option_item_id
     * @param int $product_option_id
     */
    public function get( $product_option_item_id, $product_option_id ) {
        $this->prepare(
            'SELECT `id`, `name` FROM `product_option_item` WHERE `product_option_item_id` = :product_option_item_id AND `product_option_id` = :product_option_id'
            , 'ii'
            , array( ':product_option_item_id' => $product_option_item_id, ':product_option_id' => $product_option_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get by product option
     *
     * @param int $product_option_id
     * @return ProductOptionItem[]
     */
    public function get_by_product_option( $product_option_id ) {
        return $this->prepare(
            'SELECT * FROM `product_option_item` WHERE `product_option_id` = :product_option_id'
            , 'i'
            , array( ':product_option_id' => $product_option_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOptionItem' );
    }

    /**
     * Add Relations
     *
     * @param array $product_ids
     */
    public function add_relations( array $product_ids ) {
        if ( !$product_ids )
            return;

        $product_ids_count = count( $product_ids );
        $values = substr( str_repeat( ',(' . (int) $this->id . ', ? )', $product_ids_count ), 1 );

        $this->prepare(
            'INSERT INTO `product_option_item_product` (`product_option_item_id`, `product_id`) VALUES ' . $values
            , str_repeat( 's', count( $product_ids ) )
            , $product_ids
        )->query();
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete_associated_products();
        $this->remove_self();
    }

    /**
     * Remove self
     */
    protected function remove_self() {
        $this->delete([
            'product_option_item_id' => $this->id
        ], 'i' );
    }

    /**
     * Delete Associated Products
     */
    protected function delete_associated_products() {
        $this->prepare(
            'DELETE p.* FROM `products` p LEFT JOIN `product_option_item_product` poip ON ( poip.`product_id` = p.`product_id` ) WHERE poip.`product_option_item_id` = :product_option_item_id'
            , 'i'
            , [':product_option_item_id' => $this->id]
        )->query();
    }
}