<?php
class ProductOption extends ActiveRecordBase {
    const MATTRESS_SIZES = 357;

    // The columns we will have access to
    public $id, $product_option_id, $type, $title, $name;

    // Columns from other tables
    public $product_option_list_item_id, $value;

    /**
     * @fix needs to remove the option_
     */

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_options' );

        if ( isset( $this->product_option_id ) )
            $this->id = $this->product_option_id;
    }

    /**
     * Get Product Option
     *
     * @param int $product_option_id
     */
    public function get( $product_option_id ) {
        $this->prepare(
            'SELECT `product_option_id`, `option_type` AS type, `option_title` AS title, `option_name` AS name FROM `product_options` WHERE `product_option_id` = :product_option_id'
            , 's'
            , array( ':product_option_id' => $product_option_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_option_id;
    }

    /**
     * Get All
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( 'SELECT `product_option_id`, `option_type` AS type, `option_title` AS title, `option_name` AS name FROM `product_options`', PDO::FETCH_CLASS, 'ProductOption' );
    }

    /**
     * Get by product
     *
     * @param int $product_id
     * @return ProductOption[]
     */
    public function get_by_product( $product_id ) {
        return $this->prepare(
            'SELECT po.`product_option_id`, po.`option_type` AS type, po.`option_name` AS name, poli.`product_option_list_item_id`, poli.`value` FROM `product_options` AS po LEFT JOIN `product_option_list_items` AS poli ON ( poli.`product_option_id` = po.`product_option_id` ) LEFT JOIN `product_option_relations` AS por ON ( por.`product_option_id` = po.`product_option_id` ) LEFT JOIN `products` AS p ON ( p.`brand_id` = por.`brand_id` ) WHERE p.`product_id` = :product_id ORDER BY poli.`sequence`'
            , 'i'
            , array( ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOption' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'option_type' => strip_tags($this->type)
            , 'option_title' => strip_tags($this->title)
            , 'option_name' => strip_tags($this->name)
        ), 'sss' );

        $this->product_option_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update an attribute item
     */
    public function save() {
        parent::update( array(
            'option_type' => strip_tags($this->type)
            , 'option_title' => strip_tags($this->title)
            , 'option_name' => strip_tags($this->name)
        ), array(
            'product_option_id' => $this->id
        ), 'sss', 'i' );
    }

    /**
     * Delete Product Option
     */
    public function delete() {
        if ( isset( $this->id ) ) {
            $this->delete_relations();
            $this->delete_list_items();
            $this->delete_self();
        }
    }

    /**
     * Delete the relations of product options with brands
     */
    protected function delete_relations() {
        $this->prepare(
            'DELETE FROM `product_option_relations` WHERE `product_option_id` = :product_option_id'
            , 'i'
            , array( 'product_option_id' => $this->id )
        )->query();
    }

    /**
     * Delete all list items relating to product options
     */
    protected function delete_list_items() {
        $this->prepare(
            'DELETE FROM `product_option_list_items` WHERE `product_option_id` = :product_option_id'
            , 'i'
            , array( 'product_option_id' => $this->id )
        )->query();
    }

    /**
     * Delete the Product Option itself
     */
    protected function delete_self() {
        parent::delete( array( 'product_option_id' => $this->id ), 'i' );
    }

    /**
     * Delete Relations of product options
     */

    /**
     * Delete relations by By Brand
     *
     * @param int $brand_id
     */
    public function delete_relations_by_brand( $brand_id ) {
        $this->prepare(
            'DELETE FROM `product_option_relations` WHERE `brand_id` = :brand_id'
            , 'i'
            , array( ':brand_id' => $brand_id )
        )->query();
    }

    /**
	 * Get listing information
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

		return $this->prepare( "SELECT `product_option_id`, `option_title` AS title, `option_name` AS name, `option_type` AS type FROM `product_options` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'ProductOption' );
	}

	/**
	 * Count the listing items
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( `product_option_id` ) FROM `product_options` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
