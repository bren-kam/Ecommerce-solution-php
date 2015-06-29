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

    /**
     * Get by product
     *
     * @param int $website_id
     * @param int $product_id
     * @return ProductOption[]
     */
    public function get_by_product( $website_id, $product_id ) {
        return $this->prepare(
            'SELECT po.`id`, po.`name`, po.`type` FROM `product_option` po LEFT JOIN `product_option_product` pop ON ( pop.`product_option_id` = po.`id` ) WHERE po.`website_id` = :website_id AND pop.`product_id` = :product_id'
            , 'ii'
            , array( ':website_id' => $website_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOption' );
    }
}
