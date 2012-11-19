<?php
class ProductOptionListItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $product_option_list_item_id, $product_option_id, $value, $sequence;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_option_list_items' );

        if ( isset( $this->product_option_list_item_id ) )
            $this->id = $this->product_option_list_item_id;
    }

    /**
     * Get
     *
     * @param int $product_option_list_item_id
     */
    public function get( $product_option_list_item_id ) {
        $this->prepare(
            'SELECT * FROM `product_option_list_items` WHERE `product_option_list_item_id` = :product_option_list_item_id'
            , 'i'
            , array( ':product_option_list_item_id' => $product_option_list_item_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_option_list_item_id;
    }

    /**
     * Get all product option list items for a product option
     *
     * @param int $product_option_id
     * @return array
     */
    public function get_all( $product_option_id ) {
        return $this->prepare(
            'SELECT `product_option_list_item_id`, `value` FROM `product_option_list_items` WHERE `product_option_id` = :product_option_id ORDER BY `sequence` ASC'
            , 's'
            , array( ':product_option_id' => $product_option_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOptionListItem' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'product_option_id' => $this->product_option_id
            , 'value' => $this->value
            , 'sequence' => $this->sequence
        ), 'isi' );

        $this->product_option_list_item_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update an attribute item
     */
    public function save() {
        parent::update( array(
            'value' => $this->value
            , 'sequence' => $this->sequence
        ), array(
            'product_option_list_item_id' => $this->id
        ), 'si', 'i' );
    }

    /**
     * Delete attribute item
     */
    public function delete() {
        parent::delete( array( 'product_option_list_item_id' => $this->id ), 'i' );
    }
}
