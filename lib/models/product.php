<?php
class Product extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $product_id, $name, $sku, $status, $publish_visibility, $publish_date;

    // Columns from other tables
    public $brand;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'products' );

        // We want to make sure they match
        if ( isset( $this->product_id ) )
            $this->id = $this->product_id;
    }

    /**
     * Get
     *
     * @param int $product_id
     */
    public function get( $product_id ) {
		$this->prepare(
            'SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`website_id`, a.`name`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`price`, a.`weight`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, b.`name` AS industry, c.`contact_name` AS created_user, d.`contact_name` AS updated_user, e.`title` AS website FROM `products` AS a LEFT JOIN `industries` AS b ON (a.`industry_id` = b.`industry_id`) LEFT JOIN `users` AS c ON ( a.`user_id_created` = c.`user_id` ) LEFT JOIN `users` AS d ON ( a.`user_id_modified` = d.`user_id` ) LEFT JOIN `websites` AS e ON ( a.`website_id` = e.`website_id` ) WHERE a.`product_id` = :product_id'
            , 'i'
            , array( ':product_id' => $product_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
    }

    /**
     * Update
     */
    public function update() {
        parent::update(
            array(
                'publish_visibility' => $this->publish_visibility
            )
            , array( 'product_id' => $this->id )
            , 's'
            , 'i'
        );
    }

    /**
	 * Clones a product
	 *
	 * @param int $product_id
	 */
	public function clone_product( $product_id, $user_id ) {
		// Type Juggling
		$product_id = (int) $product_id;
		$user_id = (int) $user_id;

		// Make sure it's a real product
		$exists = $this->get_var( "SELECT `product_id` FROM `products` WHERE `product_id` = $product_id" );

		// Check to see if it exists
		if ( !$exists )
			return false;

		// Clone product
		$this->query( "INSERT INTO `products` ( `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, `user_id_created`, `date_created` ) SELECT `brand_id`, `industry_id`, CONCAT( `name`, ' (Clone)' ), CONCAT( `slug`, '-2' ), `description`, `status`, CONCAT( `sku`, '-2' ), `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, $user_id, NOW() FROM `products` WHERE `product_id` = $product_id" );

		// Get the new product ID
		$this->id = $this->product_id = $this->get_insert_id();

		// Clone categories
		$this->query( "INSERT INTO `product_categories` ( `product_id`, `category_id` ) SELECT $this->id, `category_id` FROM `product_categories` WHERE `product_id` = $product_id" );

		// Clone product groups
		$this->query( "INSERT INTO `product_group_relations` ( `product_group_id`, `product_id` ) SELECT `product_group_id`, $this->id FROM `product_group_relations` WHERE `product_id` = $product_id" );

		// Clone tags
		$this->query( "INSERT INTO `tags` ( `object_id`, `type`, `value` ) SELECT $this->id, 'product', `value` FROM `tags` WHERE `object_id` = $product_id AND `type` = 'product'" );

		// Clone attributes items
		$this->query( "INSERT INTO `attribute_item_relations` ( `attribute_item_id`, `product_id` ) SELECT `attribute_item_id`, $this->id FROM `attribute_item_relations` WHERE `product_id` = $product_id" );
    }

    /**
	 * Get all information of the products
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT a.`product_id`, a.`name`, d.`name` AS brand, a.`sku`, a.`status`, DATE( a.`publish_date` ) AS publish_date, c.`name` AS category, e.`contact_name` AS created_by, f.`contact_name` AS updated_by FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON (a.brand_id = d.brand_id) LEFT JOIN `users` AS e ON ( a.`user_id_created` = e.`user_id` ) LEFT JOIN `users` AS f ON ( a.`user_id_modified` = f.`user_id` ) WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Product' );
	}

	/**
	 * Count all the products
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare(
            "SELECT COUNT( a.`product_id` ) FROM `products` AS a LEFT JOIN `product_categories` AS b ON (a.product_id = b.product_id) LEFT JOIN `categories` AS c ON (b.category_id = c.category_id) LEFT JOIN `brands` AS d ON (a.brand_id = d.brand_id) LEFT JOIN `users` AS e ON ( a.`user_id_created` = e.`user_id` ) LEFT JOIN `users` AS f ON ( a.`user_id_modified` = f.`user_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
     * @param string $as
	 * @param string $where
	 * @return bool
	 */
	public function autocomplete( $query, $field, $as, $where ) {
		// Get results
		return $this->prepare(
            "SELECT $field AS $as FROM `products` AS p LEFT JOIN `product_categories` AS pc ON ( p.`product_id` = pc.`product_id` ) LEFT JOIN `categories` AS c ON ( pc.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS b ON ( p.`brand_id` = b.`brand_id` ) LEFT JOIN `product_images` AS pi ON ( p.`product_id` = pi.`product_id` ) WHERE pi.`sequence` = 0 AND $field LIKE :query $where GROUP BY $field ORDER BY $field LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}}
