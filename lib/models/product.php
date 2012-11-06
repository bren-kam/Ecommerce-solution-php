<?php
class Product extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $product_id, $brand_id, $industry_id, $website_id, $name, $slug, $description, $sku, $status, $weight, $product_specifications, $publish_visibility, $publish_date, $user_id_created, $user_id_modified;

    // Artificial columns
    public $images;

    // Columns from other tables
    public $brand, $category_id;

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
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`weight`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, pc.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON (p.`industry_id` = i.`industry_id`) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `product_categories` AS pc ON ( p.`product_id` = pc.`product_id` ) WHERE p.`product_id` = :product_id GROUP BY p.`product_id`'
            , 'i'
            , array( ':product_id' => $product_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
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
        $this->insert( array(
            'website_id' => $this->website_id
            , 'user_id_created' => $this->user_id_created
            , 'publish_visibility' => 'deleted'
            , 'date_created' => dt::date('Y-m-d H:i:s')
        ), 'iiss' );

        $this->id = $this->product_id = $this->get_insert_id();
    }

    /**
     * Add Category
     *
     * @param int $category_id
     */
    public function add_category( $category_id ) {
        $this->prepare(
            'INSERT INTO `product_categories` ( `product_id`, `category_id` ) VALUES ( :product_id, :category_id )'
            , 'ii'
            , array( ':product_id' => $this->id, ':category_id' => $category_id )
        )->query();
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
    public function update() {
        parent::update(
            array(
                'brand_id' => $this->brand_id
                , 'industry_id' => $this->industry_id
                , 'website_id' => $this->website_id
                , 'name' => $this->name
                , 'slug' => $this->slug
                , 'description' => $this->description
                , 'sku' => $this->sku
                , 'status' => $this->status
                , 'weight' => $this->weight
                , 'product_specifications' => $this->product_specifications
                , 'publish_date' => $this->publish_date
                , 'publish_visibility' => $this->publish_visibility
                , 'user_id_modified' => $this->user_id_modified
            )
            , array( 'product_id' => $this->id )
            , 'iiisssssisssi'
            , 'i'
        );
    }

    /**
     * Delete Categories
     */
    public function delete_categories() {
        $this->prepare(
            'DELETE FROM `product_categories` WHERE `product_id` = :product_id'
            , 'i'
            , array( ':product_id' => $this->id )
        )->query();
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
            "SELECT p.`product_id`, p.`name`, b.`name` AS brand, p.`sku`, p.`status`, DATE( p.`publish_date` ) AS publish_date, c.`name` AS category, u.`contact_name` AS created_by, u2.`contact_name` AS updated_by FROM `products` AS p LEFT JOIN `product_categories` AS pc ON ( pc.`product_id` = p.`product_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = pc.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = p.`user_id_modified` ) WHERE 1 $where $order_by LIMIT $limit"
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
            "SELECT COUNT( p.`product_id` ) FROM `products` AS p LEFT JOIN `product_categories` AS pc ON ( pc.`product_id` = p.`product_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = pc.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = p.`user_id_modified` ) WHERE 1 $where"
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
