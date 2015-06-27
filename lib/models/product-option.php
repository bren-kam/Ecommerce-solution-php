<?php
class ProductOption extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $name, $type;

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
            , 'name' => $this->name
            , 'type' => $this->type
        ], 'iss');
    }

    /**
     * Add Product Option > Product Relations
     *
     * @param array $product_ids
     * @throws ModelException
     */
    public function add_relations( array $product_ids ) {
        $values = '';

        foreach ( $product_ids as &$product_id ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= '('  . (int) $this->id  . ', ' . (int) $product_id . ')';
        }

        $this->query( 'INSERT INTO `product_option_product` ( `product_option_id`, `product_id` ) VALUES ' . $values );
    }
}
