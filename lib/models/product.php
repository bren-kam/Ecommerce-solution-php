<?php
class Product extends ActiveRecordBase {
    const PUBLISH_VISIBILITY_PUBLIC = 'public';
    const PUBLISH_VISIBILITY_PRIVATE = 'private';
    const PUBLISH_VISIBILITY_DELETED = 'deleted';

    // The columns we will have access to
    public $id, $product_id, $category_id, $brand_id, $industry_id, $website_id, $name, $slug, $description, $sku
        , $country, $price, $price_min, $price_net, $price_freight, $price_discount, $status, $weight, $depth, $height
        , $length, $product_specifications, $publish_visibility, $publish_date, $user_id_created, $user_id_modified
        , $date_created, $timestamp, $parent_product_id;

    // Artificial columns
    public $images, $industry, $order, $created_by, $updated_by, $specifications;

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
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`parent_product_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, b.`name` AS brand, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id`, c.`name` AS category FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `industries` AS i ON (p.`industry_id` = i.`industry_id`) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) WHERE p.`product_id` = :product_id GROUP BY p.`product_id`'
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
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`parent_product_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON ( p.`industry_id` = i.`industry_id` ) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) WHERE p.`sku` = :sku GROUP BY p.`product_id`'
            , 's'
            , array( ':sku' => $sku )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
    }

    /**
     * Get by brand
     *
     * @param int $brand_id
     * @return Product[]
     */
    public function get_by_brand( $brand_id ) {
        return $this->prepare(
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`parent_product_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON ( p.`industry_id` = i.`industry_id` ) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) WHERE p.`brand_id` = :brand_id GROUP BY p.`product_id`'
            , 'is'
            , array( ':brand_id' => $brand_id )
        )->get_results( PDO::FETCH_CLASS, 'Product' );
    }

    /**
     * Get by sku and brand
     *
     * @param string $sku
     * @param int $brand_id
     * @param int $website_id
     */
    public function get_by_sku_by_brand( $sku, $brand_id, $website_id = 0 ) {
        $where = '';
        if ( $website_id ) {
            $where = " AND p.website_id = {$website_id} ";
        }
        $this->prepare(
            "SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`parent_product_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON ( p.`industry_id` = i.`industry_id` ) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) WHERE p.`brand_id` = :brand_id AND p.`sku` = :sku $where GROUP BY p.`product_id` ORDER BY p.`product_id` DESC LIMIT 1"
            , 'is'
            , array( ':brand_id' => $brand_id, ':sku' => $sku )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
    }

    /**
     * Get by sku and website
     *
     * @param int $product_id
     * @param int $website_id
     */
    public function get_by_id_by_website( $product_id, $website_id ) {
        $this->prepare(
            "SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`parent_product_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON ( p.`industry_id` = i.`industry_id` ) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) WHERE p.`product_id` = :product_id AND p.`website_id` = :website_id GROUP BY p.`product_id` ORDER BY p.`product_id` DESC LIMIT 1"
            , 'ii'
            , array( ':product_id' => $product_id, ':website_id' => $website_id )
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
            "SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`parent_product_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id`, b.`name` AS brand FROM `products` AS p LEFT JOIN `industries` AS i ON (p.`industry_id` = i.`industry_id`) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE p.`product_id` IN($product_ids_ordered) GROUP BY p.`product_id` ORDER BY FIELD( p.`product_id`,  $product_ids_ordered )"
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
     * Get specifications
     *
     * @return array
     */
    public function get_specifications() {
        $this->specifications = $this->prepare(
            "SELECT `key`, `value` FROM `product_specification` WHERE `product_id` = :product_id ORDER BY `sequence`"
            , 's'
            , array( ':product_id' => $this->id )
        )->get_results( PDO::FETCH_OBJ );

        return $this->specifications;
    }

    /**
     * Get by slug
     *
     * @param string $slug
     */
    public function get_by_slug( $slug ) {
        $this->prepare(
            'SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id` FROM `products` AS p LEFT JOIN `industries` AS i ON ( p.`industry_id` = i.`industry_id` ) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) WHERE p.`slug` = :slug GROUP BY p.`product_id`'
            , 's'
            , array( ':slug' => $slug )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->product_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->id = $this->product_id = $this->insert( array(
            'category_id' => $this->category_id
            , 'website_id' => $this->website_id
            , 'user_id_created' => $this->user_id_created
            , 'publish_visibility' => 'deleted'
            , 'date_created' => $this->date_created
            , 'parent_product_id' => $this->parent_product_id
        ), 'iiiss' );
    }

    /**
     * Add Images
     *
     * @param array $images
     * @param bool $skip_getimagesize [optional]
     */
    public function add_images( array $images, $skip_getimagesize = false ) {
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

            if ( $skip_getimagesize ) {
                $width = $height = 0;
            } else {
                list($width, $height) = getimagesize($this->get_image_url( $images[$sequence], 'large', $this->industry ) );
            }

            if ( !$width || !$height )
                $width = $height = 0;

            $values .= "( $product_id, ?, $sequence, $width, $height )";

        }

        // Insert the values
        $this->prepare(
            "INSERT INTO `product_images` ( `product_id`, `image`, `sequence`, `width`, `height` ) VALUES $values"
            , str_repeat( 's', $image_count )
            , $images
        )->query();
    }

    /**
     * Add Specifications
     *
     * @param array $specifications
     */
    public function add_specifications( array $specifications ) {
        // Determine how many images we have
        $specification_count = count( $specifications );

        // Don't want to add no tags
        if ( 0 == $specification_count )
            return;

        // Declare variable
        $values = '';
        $product_id = (int) $this->id;

        // Create the array for all the values
        for( $sequence = 0; $sequence < $specification_count; $sequence++ ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $product_id, ?, ?, $sequence )";
        }

        $specification_values = array();

        foreach ( $specifications as $spec ) {
            $specification_values[] = $spec[0];
            $specification_values[] = $spec[1];
        }

        // Insert the values
        $this->prepare(
            "INSERT INTO `product_specification` ( `product_id`, `key`, `value`, `sequence` ) VALUES $values"
            , str_repeat( 'ss', $specification_count )
            , $specification_values
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
                , 'parent_product_id' => $this->parent_product_id
                , 'name' => strip_tags($this->name)
                , 'slug' => strip_tags($this->slug)
                , 'description' => format::strip_only( $this->description, '<script>' )
                , 'sku' => strip_tags($this->sku)
                , 'country' => strip_tags($this->country)
                , 'price' => $this->price
                , 'price_min' => $this->price_min
                , 'price_net' => $this->price_net
                , 'price_freight' => $this->price_freight
                , 'price_discount' => $this->price_discount
                , 'status' => strip_tags($this->status)
                , 'weight' => (float) $this->weight
                , 'depth' => $this->depth
                , 'height' => $this->height
                , 'length' => $this->length
                , 'publish_date' => strip_tags($this->publish_date)
                , 'publish_visibility' => strip_tags($this->publish_visibility)
                , 'user_id_modified' => $this->user_id_modified
            )
            , array( 'product_id' => $this->id )
            , 'iiiiisssssdddddsddsdssi'
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
     * Delete specifications
     */
    public function delete_specifications() {
        $this->prepare(
            'DELETE FROM `product_specification` WHERE `product_id` = :product_id'
            , 'i'
            , array( ':product_id' => $this->id )
        )->query();
    }

    /**
     * Clones a product
     *
     * @param int $product_id
     * @param int $user_id
     * @throws ModelException
     */
	public function clone_product( $product_id, $user_id ) {
		// Type Juggling
		$product_id = (int) $product_id;
		$user_id = (int) $user_id;

		// Make sure it's a real product
		$exists = $this->get_var( "SELECT `product_id` FROM `products` WHERE `product_id` = $product_id" );

		// Check to see if it exists
		if ( !$exists )
			throw new ModelException('Cannot clone an nonexistent product');

        // Clone product
		$this->query( "INSERT INTO `products` ( `category_id`, `brand_id`, `industry_id`, `name`, `slug`, `description`, `status`, `sku`, `country`, `price`, `price_min`, `price_net`, `price_freight`, `price_discount`, `product_specifications`, `publish_visibility`, `publish_date`, `user_id_created`, `date_created` ) SELECT `category_id`, `brand_id`, `industry_id`, CONCAT( `name`, ' (Clone)' ), CONCAT( `slug`, '-2' ), `description`, `status`, CONCAT( `sku`, '-2' ), `country`, `price`, `price_min`, `price_net`, `price_freight`, `price_discount`, `product_specifications`, `publish_visibility`, `publish_date`, $user_id, NOW() FROM `products` WHERE `product_id` = $product_id" );

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
            "SELECT p.`product_id`, p.`name`, b.`name` AS brand, p.`sku`, c.`name` AS category FROM `products` AS p LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE p.`publish_visibility` <> 'deleted' AND p.`parent_product_id` IS NULL $where GROUP BY p.`product_id` $order_by LIMIT $limit"
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
            "SELECT COUNT( DISTINCT p.`product_id` ) FROM `products` AS p LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE p.`publish_visibility` <> 'deleted' AND p.`parent_product_id` IS NULL $where"
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
	 * @return array
	 */
	public function autocomplete( $query, $field, $as, $where ) {
		// Get results
		return $this->prepare(
            "SELECT $field AS $as FROM `products` AS p LEFT JOIN `categories` AS c ON ( p.`category_id` = c.`category_id` ) LEFT JOIN `brands` AS b ON ( p.`brand_id` = b.`brand_id` ) LEFT JOIN `product_images` AS pi ON ( p.`product_id` = pi.`product_id` ) WHERE pi.`sequence` = 0 AND $field LIKE :query $where GROUP BY $field ORDER BY $field LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
     * Discontinue Ashley Products By Skus
     *
     * @param array $skus
     * @param int $user_id
     * @return int
     */
    public function discontinue_ashley_products_by_skus( array $skus, $user_id ) {
        if ( empty( $skus ) )
            return 0;

        // Define
        $user_id = (int) $user_id;
        $sku_count = count( $skus );

        $this->prepare(
            "UPDATE `products` SET `status` = 'discontinued', `user_id_modified` = $user_id WHERE `user_id_created` = 353 AND `sku` IN(" . substr( str_repeat( ', ?', $sku_count ), 2 ) . ')'
            , str_repeat( 's', $sku_count )
            , $skus
        )->query();

        return $this->get_row_count();
    }

    /*
     * Upload image
     *
     * @throws InvalidParametersException
     *
     * @param string $image_url
     * @param string $slug
     * @param string $industry
     * @return string
     */
    public function upload_image( $image_url, $slug, $industry ) {
        $curl = new curl;
        $file = new File;

        if ( is_null( $industry ) )
            throw new InvalidParametersException( _('Industry must not be null') );

        $new_image_name = rawurldecode($slug);
        $image_extension = strtolower( f::extension( $image_url ) );
        $full_image_name = "{$new_image_name}.{$image_extension}";
        // $image_path = '/gsr/systems/backend/admin/media/downloads/scratchy/' . $full_image_name;
        $image_path = '/tmp/' . $full_image_name;

        // If it already exists, no reason to go on
        if( is_file( $image_path ) && curl::check_file( "http://{$industry}.retailcatalog.us/products/{$this->id}/thumbnail/{$full_image_name}" ) )
            return $full_image_name;

        // Download the file
        $copied = @copy( $image_url, $image_path );

        if ( !$copied )
            throw new ErrorException( _("Could not copy '$image_url' to '$image_path'") );

        $file->upload_image( $image_path, $new_image_name, 350, 350, $industry, "products/{$this->id}/", false, true );
        $file->upload_image( $image_path, $new_image_name, 64, 64, $industry, "products/{$this->id}/thumbnail/", false, true );
        $file->upload_image( $image_path, $new_image_name, 200, 200, $industry, "products/{$this->id}/small/", false, true );
        $full_image_name = $file->upload_image( $image_path, $new_image_name, 1000, 1000, $industry, "products/{$this->id}/large/" );

        @unlink( $image_path );

        return $full_image_name;
    }

    /**
     * Get Image URL
     *
     * @param $image
     * @param string $size
     * @param string $industry
     * @param null $product_id
     * @return string
     */
    public function get_image_url($image, $size = "small", $industry = "furniture", $product_id = null ) {

        // If it's an absolute URL, just return it
        if ( stripos( $image, 'http' ) === 0 )
            return $image;

        if ( !$product_id )
            $product_id = $this->id;

        return 'http://' . str_replace( ' ', '', $industry ) . '.retailcatalog.us/products/' . $product_id . '/' . ($size ? ($size . '/') : '') .$image;
    }


    /**
     * Discontinue Orphan Packages
     * @param bool $echo_log
     * @throws ModelException
     */
    public function discontinue_orphan_packages( $echo_log = false ) {
        if ( $echo_log )
            echo "Getting packages...\n";

        $packages = $this->get_results(
            "SELECT product_id, sku FROM products WHERE user_id_created = 1477 AND publish_visibility = 'public' AND status = 'in-stock'"
            , PDO::FETCH_ASSOC
        );

        if ( $echo_log )
            echo "Getting pieces...\n";
        $active_pieces = $this->get_col(
            "SELECT sku FROM products WHERE user_id_created = 353 AND publish_visibility = 'public' AND status = 'in-stock'"
        );

        foreach ( $packages as $package ) {
            // These will be used twice
            $sku_pieces = explode( '/', $package['sku'] );
            $serie = array_shift( $sku_pieces );
            // Remove anything within parenthesis on SKU Pieces
            $regex = '/\(([^)]*)\)/';
            foreach ( $sku_pieces as $k => $sp ) {
                // remove things in parenthesis
                $piece = preg_replace( $regex, '', $sp );

                if ( !in_array("{$serie}{$piece}", $active_pieces) && !in_array("{$serie}-{$piece}", $active_pieces) ) {
                    if ( $echo_log )
                        echo "Discontinuing {$package['sku']} as {$serie}-{$piece} is discontinued\n";
                    $this->query("UPDATE products SET status = 'discontinued' WHERE product_id = '{$package['product_id']}' LIMIT 1");
                }
            }

        }

        if ( $echo_log )
            echo "Finished\n";
    }

    /**
     * Get By Parent
     * @param $parent_product_id
     * @return Product[]
     */
    public function get_by_parent( $parent_product_id ) {
        return $this->prepare(
            'SELECT p.`product_id` as `id`, p.`product_id`, p.`brand_id`, p.`industry_id`, p.`website_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`country`, p.`price`, p.`price_min`, p.`price_net`, p.`price_freight`, p.`price_discount`, p.`weight`, p.`depth`, p.`height`, p.`length`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, b.`name` AS brand, i.`name` AS industry, u.`contact_name` AS created_user, u2.`contact_name` AS updated_user, w.`title` AS website, p.`category_id`, c.`name` AS category, p.`parent_product_id` FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `industries` AS i ON (p.`industry_id` = i.`industry_id`) LEFT JOIN `users` AS u ON ( p.`user_id_created` = u.`user_id` ) LEFT JOIN `users` AS u2 ON ( p.`user_id_modified` = u2.`user_id` ) LEFT JOIN `websites` AS w ON ( p.`website_id` = w.`website_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) WHERE p.`parent_product_id` = :parent_product_id GROUP BY p.`product_id`'
            , 'i'
            , array( ':parent_product_id' => $parent_product_id )
        )->get_results( PDO::FETCH_CLASS, 'Product' );
    }
}
