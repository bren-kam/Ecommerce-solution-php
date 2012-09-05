<?php
class Brand extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $brand_id, $name, $slug, $link, $image;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'brands' );

        if ( isset( $this->brand_id ) )
            $this->id = $this->brand_id;
    }

    /**
     * Get Brand
     *
     * @param int $brand_id
     */
    public function get( $brand_id ) {
        $this->prepare(
            'SELECT * FROM `brands` WHERE `brand_id` = :brand_id'
            , 's'
            , array( ':brand_id' => $brand_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->brand_id;
    }

    /**
     * Get All
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( 'SELECT * FROM `brands` ORDER BY `name`', PDO::FETCH_CLASS, 'Brand' );
    }

    /**
     * Get Relations
     *
     * @return array
     */
    public function get_product_option_relations() {
        return $this->prepare(
            'SELECT * FROM `product_option_relations` WHERE `brand_id` = :brand_id'
            , 's'
            , array( ':brand_id' => $this->id )
        )->get_col();
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'name' => $this->name
            , 'slug' => $this->slug
            , 'link' => $this->link
            , 'image' => $this->image
        ), 'ssss' );

        $this->brand_id = $this->id = $this->get_insert_id();
    }

    /**
     * Add Relations
     *
     * @param array $product_option_ids
     */
    public function add_product_option_relations( array $product_option_ids ) {
        if ( 0 == count( $product_option_ids ) )
            return;

        $values = '';
        $brand_id = (int) $this->id;

        foreach ( $product_option_ids as $poid ) {
            if ( !empty( $values ) )
                $values .= ',';

            $poid = (int) $poid;

            $values .= "( $poid, $brand_id )";
        }

        $this->query( "INSERT INTO `product_option_relations` ( `product_option_id`, `brand_id` ) VALUES $values" );
    }

    /**
     * Update
     */
    public function update() {
        parent::update( array(
            'name' => $this->name
            , 'slug' => $this->slug
            , 'link' => $this->link
            , 'image' => $this->image
        ), array(
            'brand_id' => $this->id
        ), 'ssss', 'i' );
    }

    /**
     * Delete Brand
     */
    public function delete() {
        if ( isset( $this->id ) )
            parent::delete( array( 'brand_id' => $this->id ), 'i' );
    }

    /**
     * Delete Relations
     */
    public function delete_product_option_relations() {
        $this->prepare(
            'DELETE FROM `product_option_relations` WHERE `brand_id` = :brand_id'
            , 'i'
            , array( ':brand_id' => $this->id )
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

		return $this->prepare( "SELECT `brand_id`, `name`, `link` FROM `brands` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Brand' );
	}

	/**
	 * Count all the checklists
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( `brand_id` ) FROM `brands` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
