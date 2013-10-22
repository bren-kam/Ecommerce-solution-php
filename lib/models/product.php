<?php
class Product extends ActiveRecordBase {
    const PUBLISH_VISIBILITY_PUBLIC = 'public';
    const PUBLISH_VISIBILITY_PRIVATE = 'private';
    const PUBLISH_VISIBILITY_DELETED = 'deleted';

    // The columns we will have access to
    public $id, $product_id, $category_id, $brand_id, $industry_id, $website_id, $name, $slug, $description, $sku
        , $price, $list_price, $status, $weight, $product_specifications, $publish_visibility, $publish_date
        , $user_id_created, $user_id_modified, $date_created;

    // Artificial columns
    public $images, $industry, $order, $created_by, $updated_by;

    // Columns from other tables
    public $brand, $category;

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
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`weight`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, b.`name` AS brand, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id`, c.`name` AS category FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `industries` AS i ON (p.`industry_id` = i.`industry_id`) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) WHERE p.`product_id` = :product_id GROUP BY p.`product_id`'
            , 'i'
            , array( ':product_id' => $product_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
    }

    /**
     * Get by sku
     *
     * @param string $sku
     */
    public function get_by_sku( $sku ) {
        $this->prepare(
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`weight`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON ( p.`industry_id` = i.`industry_id` ) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) WHERE p.`sku` = :sku GROUP BY p.`product_id`'
            , 's'
            , array( ':sku' => $sku )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
    }

    /**
     * Get by ids
     *
     * @param array $product_ids
     * @return Product[]
     */
    public function get_by_ids( array $product_ids ) {
        if ( empty( $product_ids ) )
            return array();

        foreach ( $product_ids as & $product_id ) {
            $product_id = (int) $product_id;
        }

        $product_ids_ordered = implode( ',', $product_ids );

        return $this->get_results(
            "SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`weight`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id`, b.`name` AS brand FROM `products` AS p LEFT JOIN `industries` AS i ON (p.`industry_id` = i.`industry_id`) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE p.`product_id` IN($product_ids_ordered) GROUP BY p.`product_id` ORDER BY FIELD( p.`product_id`,  $product_ids_ordered )"
            , PDO::FETCH_CLASS
            , 'Product'
        );
    }

    /**
     * Get Images
     *
     * @return array
     */
    public function get_images() {
        return $this->prepare(
            "SELECT `image` FROM `product_images` WHERE `product_id` = :product_id AND `image` <> '' ORDER BY `sequence`"
            , 's'
            , array( ':product_id' => $this->id )
        )->get_col();
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'category_id' => $this->category_id
            , 'website_id' => $this->website_id
            , 'user_id_created' => $this->user_id_created
            , 'publish_visibility' => 'deleted'
            , 'date_created' => $this->date_created
        ), 'iiiss' );

        $this->id = $this->product_id = $this->get_insert_id();
    }

    /**
     * Add Images
     *
     * @param array $images
     */
    public function add_images( array $images ) {
        // Determine how many images we have
        $image_count = count( $images );

        // Don't want to add no tags
        if ( 0 == $image_count )
            return;

        // Declare variable
        $values = '';
        $product_id = (int) $this->id;

        // Create the array for all the values
        for( $sequence = 0; $sequence < $image_count; $sequence++ ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $product_id, ?, $sequence )";
        }

        // Insert the values
        $this->prepare(
            "INSERT INTO `product_images` ( `product_id`, `image`, `sequence` ) VALUES $values"
            , str_repeat( 's', $image_count )
            , $images
        )->query();
    }

    /**
     * Update
     */
    public function save() {
        parent::update(
            array(
                'category_id' => $this->category_id
                , 'brand_id' => $this->brand_id
                , 'industry_id' => $this->industry_id
                , 'website_id' => $this->website_id
                , 'name' => strip_tags($this->name)
                , 'slug' => strip_tags($this->slug)
                , 'description' => format::strip_only( $this->description, '<script>' )
                , 'sku' => strip_tags($this->sku)
				, 'price' => $this->price
                , 'status' => strip_tags($this->status)
                , 'weight' => $this->weight
                , 'product_specifications' => strip_tags($this->product_specifications)
                , 'publish_date' => strip_tags($this->publish_date)
                , 'publish_visibility' => strip_tags($this->publish_visibility)
                , 'user_id_modified' => $this->user_id_modified
            )
            , array( 'product_id' => $this->id )
            , 'iiiissssdsisssi'
            , 'i'
        );
    }

    /**
     * Delete Images
     */
    public function delete_images() {
        $this->prepare(
            'DELETE FROM `product_images` WHERE `product_id` = :product_id'
            , 'i'
            , array( ':product_id' => $this->id )
        )->query();
    }

    /**
	 * Clones a product
	 *
	 * @param int $product_id
     * @param int $user_id
	 */
	public function clone_product( $product_id, $user_id ) {
		// Type Juggling
		$product_id = (int) $product_id;
		$user_id = (int) $user_id;

		// Make sure it's a real product
		$exists = $this->get_var( "SELECT `product_id` FROM `products` WHERE `product_id` = $product_id" );

		// Check to see if it exists
		if ( !$exists )
			return;

        // Clone product
		$this->query( "INSERT INTO `products` ( `category_id`, `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, `user_id_created`, `date_created` ) SELECT `category_id`, `brand_id`, `industry_id`, CONCAT( `name`, ' (Clone)' ), CONCAT( `slug`, '-2' ), `description`, `status`, CONCAT( `sku`, '-2' ), `price`, `list_price`, `product_specifications`, `publish_visibility`, `publish_date`, $user_id, NOW() FROM `products` WHERE `product_id` = $product_id" );

		// Get the new product ID
		$this->id = $this->product_id = $this->get_insert_id();

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
	 * @return Product[]
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT p.`product_id`, p.`website_id`, p.`name`, b.`name` AS brand, p.`sku`, p.`status`, DATE( p.`publish_date` ) AS publish_date, c.`name` AS category, u.`contact_name` AS created_by, u2.`contact_name` AS updated_by FROM `products` AS p LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = p.`user_id_modified` ) WHERE 1 $where $order_by LIMIT $limit"
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
            "SELECT COUNT( p.`product_id` ) FROM `products` AS p LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = p.`user_id_modified` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
     * Get all information of the products
     *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
     * @return Product[]
     */
    public function list_custom_products( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT p.`product_id`, p.`name`, b.`name` AS brand, p.`sku`, c.`name` AS category FROM `products` AS p LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE p.`publish_visibility` <> 'deleted' $where GROUP BY p.`product_id` $order_by LIMIT $limit"
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
    public function count_custom_products( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT p.`product_id` ) FROM `products` AS p LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE p.`publish_visibility` <> 'deleted' $where"
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
            "SELECT $field AS $as FROM `products` AS p LEFT JOIN `categories` AS c ON ( p.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS b ON ( p.`brand_id` = b.`brand_id` ) LEFT JOIN `product_images` AS pi ON ( p.`product_id` = pi.`product_id` ) WHERE pi.`sequence` = 0 AND $field LIKE :query $where GROUP BY $field ORDER BY $field LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}}
