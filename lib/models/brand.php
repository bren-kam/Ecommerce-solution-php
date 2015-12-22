<?php
class Brand extends ActiveRecordBase {
    const ARTIFICIAL_ASHLEY_EXPRESS = 1048576;

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
     * @return Brand[]
     */
    public function get_all() {
        return $this->get_results( "SELECT * FROM `brands` ORDER BY `name` WHERE `name` <> ''", PDO::FETCH_CLASS, 'Brand' );
    }

    /**
     * Get By Ids
     *
     * @param array $brand_ids
     * @return Brand[]
     */
    public function get_by_ids( array $brand_ids ) {
        if ( empty ( $brand_ids ) )
            return array();

        foreach ( $brand_ids as &$brand_id ) {
            $brand_id = (int) $brand_id;
        }

        return $this->get_results(
            'SELECT * FROM `brands` WHERE `brand_id` IN ( ' . implode( ',', $brand_ids ) . ' ) ORDER BY `name`'
            , PDO::FETCH_CLASS
            , 'Brand'
        );
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @return Brand[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT b.* FROM `brands` AS b LEFT JOIN `products` AS p ON ( p.`brand_id` = b.`brand_id` ) LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = 0 AND wp.`active` = 1 GROUP BY b.`brand_id` ORDER BY b.`name` ASC'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'Brand' );
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
     *
     * @return int
     */
    public function create() {
        $this->brand_id = $this->id = $this->insert( [
            'name' => strip_tags($this->name)
            , 'slug' => strip_tags($this->slug)
            , 'link' => strip_tags($this->link)
            , 'image' => strip_tags($this->image)
        ], 'ssss' );

        return $this->brand_id;
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
    public function save() {
        parent::update( array(
            'name' => strip_tags($this->name)
            , 'slug' => strip_tags($this->slug)
            , 'link' => strip_tags($this->link)
            , 'image' => strip_tags($this->image)
        ), array(
            'brand_id' => $this->id
        ), 'ssss', 'i' );
    }

    /**
     * Delete Brand
     */
    public function remove() {
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
	 * @return Brand[]
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

    /**
	 * Gets the data for autocomplete on all brands
	 *
	 * @param string $query
     * @param bool $custom_products [optional]
	 * @return array
	 */
	public function autocomplete_all( $query, $custom_products = false ) {
        return $this->prepare(
            'SELECT `brand_id` AS value, `name` FROM `brands` WHERE `name` LIKE :query ORDER BY `name` LIMIT 10'
            , 's'
            , array( ':query' => $query . '%' )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
	 * Gets the data for an autocomplete request by account
	 *
	 * @param string $query
     * @param int $account_id
	 * @return array
	 */
	public function autocomplete_by_account( $query, $account_id ) {
        return $this->prepare(
            "SELECT DISTINCT b.`brand_id` AS value, b.`name` FROM `brands` AS b LEFT JOIN `products` AS p ON ( p.`brand_id` = b.`brand_id` ) LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`category_id` = p.`category_id` AND wbc.`website_id` = wp.`website_id` ) WHERE b.`name` LIKE :query AND wp.`website_id` = :account_id AND wbc.`category_id` IS NULL ORDER BY b.`name` LIMIT 10"
            , 'si'
            , array( ':query' => $query . '%', ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
	 * Gets the data for an autocomplete request by account
	 *
	 * @param string $query
     * @param int $account_id
	 * @return array
	 */
	public function autocomplete_custom( $query, $account_id ) {
        return $this->prepare(
            "SELECT DISTINCT b.`brand_id` AS value, b.`name` FROM `brands` AS b LEFT JOIN `products` AS p ON ( p.`brand_id` = b.`brand_id` ) WHERE b.`name` LIKE :query AND p.`website_id` = :account_id ORDER BY b.`name` LIMIT 10"
            , 'si'
            , array( ':query' => $query . '%', ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }
}
