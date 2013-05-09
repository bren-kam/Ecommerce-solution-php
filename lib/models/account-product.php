<?php
class AccountProduct extends ActiveRecordBase {
    // Columns
    public $website_id, $product_id, $alternate_price, $price, $sale_price, $wholesale_price, $inventory
        , $additional_shipping_amount, $weight, $protection_amount, $additional_shipping_type
        , $alternate_price_name, $meta_title, $meta_description, $meta_keywords, $protection_type, $price_note
        , $product_note, $ships_in, $store_sku, $warranty_length, $alternate_price_strikethrough
        , $display_inventory, $on_sale, $status, $sequence, $blocked, $active, $date_updated;

    // Artificial columns
    public $link, $industry, $coupons, $product_options, $created_by;

    // Columns from other tables
    public $category_id, $category, $brand, $slug, $sku, $name, $image;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_products' );
    }

    /**
     * Get
     *
     * @param int $product_id
     * @param int $account_id
     */
    public function get( $product_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `website_products` WHERE `product_id` = :product_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':product_id' => $product_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     *
     * @return AccountProduct[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            "SELECT p.`sku`, p.`name`, c.`name` AS category, b.`name` AS brand, u.`contact_name` AS created_by FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `product_categories` AS pc ON ( pc.`product_id` = p.`product_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = pc.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) WHERE wp.`website_id` = :account_id AND wp.`status` = 1 AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' GROUP BY wp.`product_id`"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountProduct' );
    }

    /**
     * Get Count
     *
     * @param int $account_id
     * @return int
     */
    public function count( $account_id ) {
        $account_id = (int) $account_id;

        return $this->get_var( "SELECT COUNT( DISTINCT p.`product_id` ) FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN website_products AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`blocked` = 0 AND wp.`active` = 1 AND ( p.`website_id` = 0 || p.`website_id` = $account_id ) AND wp.`website_id` = $account_id AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00'" );
    }

    /**
     * Save
     */
    public function save() {
        parent::update( array(
            'alternate_price' => $this->alternate_price
            , 'price' => $this->price
            , 'sale_price' => $this->sale_price
            , 'inventory' => $this->inventory
            , 'alternate_price_name' => $this->alternate_price_name
            , 'price_note' => $this->price_note
            , 'product_note' => $this->product_note
            , 'warranty_length' => $this->warranty_length
            , 'display_inventory' => $this->display_inventory
            , 'on_sale' => $this->on_sale
            , 'status' => $this->status
            , 'meta_title' => $this->meta_title
            , 'meta_description' => $this->meta_description
            , 'meta_keywords' => $this->meta_keywords
            , 'wholesale_price' => $this->wholesale_price
            , 'additional_shipping_amount' => $this->additional_shipping_amount
            , 'weight' => $this->weight
            , 'protection_amount' => $this->protection_amount
            , 'additional_shipping_type' => $this->additional_shipping_type
            , 'protection_type' => $this->protection_type
            , 'ships_in' => $this->ships_in
            , 'store_sku' => $this->store_sku
            , 'active' => $this->active
        ), array(
            'website_id' => $this->website_id
            , 'product_id' => $this->product_id
        ), 'iiiissssiiisssiiiissssi', 'ii' );
    }

    /**
     * Update the sequence of many products
     *
     * @param int $account_id
     * @param array $product_ids
     */
    public function update_sequence( $account_id, array $product_ids ) {
        // Starting with 0 for a sequence
        $sequence = 0;

        // Prepare statement
        $statement = $this->prepare_raw( 'UPDATE `website_products` SET `sequence` = :sequence WHERE `product_id` = :product_id AND `website_id` = :account_id' );
        $statement->bind_param( ':sequence', $sequence, 'i' )
            ->bind_param( ':product_id', $product_id, 'i' )
            ->bind_value( ':account_id', $account_id, 'i' );

        // Loop through the statement and update anything as it needs to be updated
        foreach ( $product_ids as $product_id ) {
            $statement->query();

            $sequence++;
        }
    }

    /**
	 * Gets Website Products
	 *
     * @param int $account_id
	 * @param int $limit (optional) the number of products to get
	 * @param string $where (optional) a 'WHERE' clause to add on to the SQL Statement
     * @param int $page
	 * @return AccountProduct[]
	 */
	 public function search( $account_id, $limit = 20, $where = '', $page = 1 ) {
		// Instantiate Classes
        if ( 0 == $limit ) {
            $sql_limit = '';
        } else {
            $starting_product = ( $page - 1 ) * $limit;
            $sql_limit = "LIMIT $starting_product, $limit";
        }

		$sql = 'SELECT p.`product_id`,';
		$sql .= 'p.`name`, p.`slug`, b.`name` AS brand, p.`sku`, p.`status`, c.`category_id`,';
		$sql .= 'c.`name` AS category, pi.`image`, wp.`price`, wp.`alternate_price`, wp.`alternate_price_name`,';
		$sql .= 'wp.`sequence`, DATE( p.`publish_date` ) AS publish_date, pi.`image`, i.`name` AS industry ';
		$sql .= 'FROM `products` AS p ';
		$sql .= 'LEFT JOIN `product_categories` AS pc ON ( pc.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `categories` AS c ON ( c.`category_id` = pc.`category_id` ) ';
		$sql .= 'LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) ';
		$sql .= 'LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) ';
		$sql .= "WHERE p.`publish_visibility` = 'public' AND wp.`blocked` = 0 AND wp.`active` = 1 AND wp.`website_id` = $account_id AND ( pi.`sequence` = 0 OR pi.`sequence` IS NULL ) AND p.`date_created` <> '0000-00-00 00:00:00' ";
		$sql .= $where;
		$sql .= " GROUP BY p.`product_id` ORDER BY wp.`sequence` ASC $sql_limit";

		return $this->get_results( $sql, PDO::FETCH_CLASS, 'AccountProduct' );
	}

    /**
	 * Gets Website Products
	 *
     * @param int $account_id
	 * @param string $where (optional) a 'WHERE' clause to add on to the SQL Statement
	 * @return int
	 */
	 public function search_count( $account_id, $where = '' ) {
		$sql = 'SELECT COUNT( DISTINCT p.`product_id` )';
		$sql .= 'FROM `products` AS p ';
		$sql .= 'LEFT JOIN `product_categories` AS pc ON ( pc.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `categories` AS c ON ( c.`category_id` = pc.`category_id` ) ';
		$sql .= 'LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) ';
		$sql .= 'LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) ';
		$sql .= "WHERE p.`publish_visibility` = 'public' AND wp.`blocked` = 0 AND wp.`active` = 1 AND wp.`website_id` = $account_id AND ( pi.`sequence` = 0 OR pi.`sequence` IS NULL ) AND p.`date_created` <> '0000-00-00 00:00:00' ";
		$sql .= $where;
		$sql .= " ORDER BY wp.`sequence` ASC";

		return $this->get_var( $sql );
	}

    /**
     * Deactivate products by account
     *
     * @param int $account_id
     */
    public function deactivate_by_account( $account_id ) {
        parent::update( array( 'active' => 0 ), array( 'website_id' => $account_id ), 'i', 'i' );
    }

    /**
     * Copy products
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_by_account( $template_account_id, $account_id ) {
        $this->copy( $this->table, array(
                'website_id' => $account_id
                , 'product_id' => NULL
                , 'status' => NULL
                , 'on_sale' => NULL
                , 'sequence' => NULL
                , 'active' => 1
            ), array( 'website_id' => $template_account_id )
        );
    }

    /**
     * Add Bulk All
     *
     * @param int $account_id
     * @param array $industry_ids
     * @param array $product_skus
     * @return array
     */
    public function add_bulk_all( $account_id, array $industry_ids, array $product_skus ) {
        // Setup variables
        $pre_not_added_skus = $already_existed_skus = $not_added_skus = array();

        // Get the count of the products that would be added (exclude ones that the website already has)
        $adding_skus = $this->get_bulk_skus_to_be_added( $account_id, $industry_ids, $product_skus );

        // Figure out if we have to check for existing skus
        foreach ( $product_skus as $sku ) {
            $sku = trim( $sku );

            if ( !in_array( $sku, $adding_skus ) )
                $pre_not_added_skus[] = $sku;
        }

        // If we do
        if ( count( $pre_not_added_skus ) > 0 ) {
            $already_existed_skus = $this->get_bulk_already_existed_skus( $account_id, $product_skus );

            foreach ( $pre_not_added_skus as $sku ) {
                if ( !in_array( $sku, $already_existed_skus ) )
                    $not_added_skus[] = $sku;
            }
        }

        // Add the skus
        $quantity = $this->add_bulk( $account_id, $industry_ids, $product_skus );

        // Return everything we need to
        return array( $quantity, count( $already_existed_skus ), $not_added_skus );
    }

    /**
     * Get the SKUs that will be added for bulk
     *
     * @param int $account_id
     * @param array $industry_ids
   	 * @param array $product_skus
     * @return array
     */
    public function get_bulk_skus_to_be_added( $account_id, array $industry_ids, array $product_skus ) {
        // Type Juggling
        $account_id = (int) $account_id;

        // Make industry IDs safe
        foreach ( $industry_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $industry_ids_sql = implode( ',', $industry_ids );

        // Split into chunks so we can do queries one at a time
        $product_sku_chunks = array_chunk( $product_skus, 500 );

        $adding_skus = array();

        foreach ( $product_sku_chunks as $product_skus ) {
            // Get the count
            $product_sku_count = count( $product_skus );

            // Turn it into a string
            $product_skus_sql = '?' . str_repeat( ',?', $product_sku_count - 1 );

            // Magical Query
            // Insert website products
            $adding_skus = array_merge( $this->prepare(
                "SELECT p.`sku` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE p.`industry_id` IN ( $industry_ids_sql ) AND ( p.`website_id` = 0 OR p.`website_id` = $account_id ) AND p.`publish_visibility` = 'public' AND p.`sku` IN ( $product_skus_sql ) AND ( wp.`product_id` IS NULL OR wp.`active` = 0 )"
                , str_repeat( 's', $product_sku_count )
                , $product_skus
            )->get_col( PDO::FETCH_ASSOC ), $adding_skus );
        }

        return $adding_skus;
    }

    /**
     * Get the SKUs that will be added for bulk
     *
     * @param int $account_id
   	 * @param array $product_skus
     * @return array
     */
    public function get_bulk_already_existed_skus( $account_id, array $product_skus ) {
        // Type Juggling
        $account_id = (int) $account_id;

        // Split into chunks so we can do queries one at a time
        $product_sku_chunks = array_chunk( $product_skus, 500 );

        $already_existed_skus = array();

        foreach ( $product_sku_chunks as $product_skus ) {
            // Get the count
            $product_sku_count = count( $product_skus );

            // Turn it into a string
            $product_skus_sql = '?' . str_repeat( ',?', $product_sku_count - 1 );

            // Magical Query
            // Insert website products
            $already_existed_skus = array_merge( $this->prepare(
                "SELECT p.`sku` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE p.`sku` IN ( $product_skus_sql ) AND wp.`active` = 1"
                , str_repeat( 's', $product_sku_count )
                , $product_skus
            )->get_col( PDO::FETCH_ASSOC ), $already_existed_skus );
        }

        return $already_existed_skus;
    }

    /**
	 * Add Bulk
	 *
	 * @param int $account_id
     * @param array $industry_ids
	 * @param array $product_skus
     * @return int
	 */
	public function add_bulk( $account_id, array $industry_ids, array $product_skus ) {
        // Make sure they entered in SKUs
        if ( empty( $product_skus ) || empty( $industry_ids ) )
            return 0;

        // Make account id safe
        $account_id = (int) $account_id;

        // Make industry IDs safe
        foreach ( $industry_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $industry_ids_sql = implode( ',', $industry_ids );

        // Split into chunks so we can do queries one at a time
		$product_sku_chunks = array_chunk( $product_skus, 500 );

        // Count the products added
        $count = 0;

		foreach ( $product_sku_chunks as $product_skus ) {
            // Get the count
            $product_sku_count = count( $product_skus );

			// Turn it into a string
			$product_skus_sql = '?' . str_repeat( ',?', $product_sku_count - 1 );

			// Magical Query
			// Insert website products
			$this->prepare(
                "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $account_id, p.`product_id` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE ( p.`website_id` = 0 OR p.`website_id` = $account_id ) AND p.`industry_id` IN( $industry_ids_sql ) AND p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND p.`sku` IN ( $product_skus_sql ) AND ( wp.`product_id` IS NULL OR wp.`active` = 0 ) GROUP BY `sku` ON DUPLICATE KEY UPDATE `active` = 1"
                , str_repeat( 's', $product_sku_count )
                , $product_skus
            )->query();

            $count += $this->get_row_count();
		}

        return $count;
	}

    /**
     * Add Bulk Count
     *
     * @param int $account_id
     * @param array $industry_ids
     * @param array $product_skus
     * @return int
     */
    public function add_bulk_count( $account_id, array $industry_ids, array $product_skus ) {
        // Make account id safe
        $account_id = (int) $account_id;

        // Make industry IDs safe
        foreach ( $industry_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $industry_ids_sql = implode( ',', $industry_ids );

        // Get the count
        $product_sku_count = count( $product_skus );

        // Turn it into a string
        $product_skus_sql = '?' . str_repeat( ',?', $product_sku_count - 1 );

        // Count how many would be entered
        // Insert website products
        return $this->prepare(
            "SELECT COUNT( DISTINCT p.`sku` ) FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE ( p.`website_id` = 0 OR p.`website_id` = $account_id ) AND p.`industry_id` IN( $industry_ids_sql ) AND p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND p.`sku` IN ( $product_skus_sql ) AND ( wp.`product_id` IS NULL OR wp.`active` = 0 )"
            , str_repeat( 's', $product_sku_count )
            , $product_skus
        )->get_var();
    }

    /**
	 * Add Bulk By Product IDs
	 *
	 * @param int $account_id
     * @param array $product_ids
	 */
	public function add_bulk_by_ids( $account_id, array $product_ids ) {
        // Make sure they entered in SKUs
        if ( empty( $product_ids ) )
            return;

        // Make account id safe
        $account_id = (int) $account_id;
        $values = '';

        // Make industry IDs safe
        foreach ( $product_ids as $product_id ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $account_id, " . (int) $product_id . ' )';
        }

        // Insert website products
        $this->query( "INSERT INTO `website_products` ( `website_id`, `product_id` ) VALUES $values ON DUPLICATE KEY UPDATE `active` = 1" );
	}

    /**
     * Add Bulk by Brand
     *
     * @param int $account_id
     * @param int $brand_id
     * @param array $industries
     * @return int
     */
    public function add_bulk_by_brand( $account_id, $brand_id, array $industries ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $brand_id = (int) $brand_id;

        foreach ( $industries as &$industry_id ) {
            $industry_id = (int) $industry_id;
        }

        // Magical Query - Insert website products
        $this->query( "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $account_id, p.`product_id` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE ( p.`website_id` = 0 OR p.`website_id` = $account_id ) AND p.`industry_id` IN(" . implode( ',', $industries ) . ") AND p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND p.`brand_id` = $brand_id AND ( wp.`product_id` IS NULL OR wp.`active` = 0 ) ON DUPLICATE KEY UPDATE `active` = 1" );

        return $this->get_row_count();
    }

    /**
     * Add Bulk By Brand Count
     *
     * @param int $account_id
     * @param int $brand_id
     * @param array $industries
     * @return int
     */
    public function add_bulk_by_brand_count( $account_id, $brand_id, array $industries ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $brand_id = (int) $brand_id;

        foreach ( $industries as &$industry_id ) {
            $industry_id = (int) $industry_id;
        }

        return $this->get_var( "SELECT COUNT( p.`product_id` ) FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE ( p.`website_id` = 0 OR p.`website_id` = $account_id ) AND p.`industry_id` IN ( " . implode( ',', $industries ) . " ) AND p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND p.`brand_id` = $brand_id AND ( wp.`product_id` IS NULL OR wp.`active` = 0 )" );
    }

    /**
	 * Deactivate a bunch of products at once
	 *
	 * @param int $account_id
	 * @param array $product_ids
	 */
	public function remove_bulk( $account_id, array $product_ids ) {
		if ( 0 == count( $product_ids ) )
			return;

        // Make the product IDs safe
        foreach ( $product_ids as &$pid ) {
            $pid = (int) $pid;
        }

		// Deactivate in chunks of 500
		$product_id_chunks = array_chunk( $product_ids, 500 );

		foreach ( $product_id_chunks as $product_ids_array ) {
			$this->prepare(
                "UPDATE `website_products` SET `active` = 0 WHERE `website_id` = :account_id AND `product_id` IN(" . implode( ',', $product_ids_array ) . ')'
                , 'i'
                , array( ':account_id' => $account_id )
            )->query();
		}
	}

    /**
     * Block Products
	 *
     * @param int $account_id
	 * @param array $industry_ids
	 * @param array $skus
	 */
	public function block_by_sku( $account_id, array $industry_ids, array $skus ) {
        if ( empty( $skus ) || empty( $industry_ids ) )
            return;

        // Make the ints ints
        $account_id = (int) $account_id;

        foreach ( $industry_ids as &$industry_id ) {
            $industry_id = (int) $industry_id;
        }

        $industries = implode( ',', $industry_ids );
        $sku_count = count( $skus );
        $sku_string = substr( str_repeat( ', ?', $sku_count ), 2 );

		// Magical Query #2
		// Insert website products
		$this->prepare(
            "INSERT INTO `website_products` ( `website_id`, `product_id`, `blocked`, `active` ) SELECT DISTINCT $account_id, p.`product_id`, 1, 0 FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $account_id ) WHERE p.`industry_id` IN($industries) AND ( p.`website_id` = 0 OR p.`website_id` = $account_id ) AND p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND p.`sku` IN ( $sku_string ) ON DUPLICATE KEY UPDATE `blocked` = 1, `active` = 0"
            , str_repeat( 's', $sku_count )
            , $skus
        )->query();
    }

    /**
     * Block Products
     *
     * @param int $account_id
     * @param array $product_ids
     */
    public function unblock( $account_id, array $product_ids ) {
        // Make sure they entered in SKUs
        if ( empty( $product_ids ) )
            return;

         // Make account id safe
        $account_id = (int) $account_id;

        // Escape all the SKUs
        foreach ( $product_ids as &$pid ) {
            $pid = (int) $pid;
        }

        // Turn it into a string
        $product_ids = implode( ",", $product_ids );

        // Unblock products
        $this->query( "UPDATE `website_products` SET `blocked` = 0 WHERE `website_id` = $account_id AND `product_id` IN ( $product_ids )" );
    }

    /**
     * Get Blocked Products
     *
     * @param int $account_id
     * @return Product[]
     */
    public function get_blocked( $account_id ) {
        return $this->prepare(
            'SELECT p.`product_id`, p.`name`, p.`sku` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = 1'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'Product' );
    }

    /**
	 * Removes all sale items from a website
     *
     * @param int $account_id
	 */
	public function remove_sale_items( $account_id ) {
		$this->update(
            array( 'on_sale' => 0 )
            , array( 'website_id' => $account_id )
            , 'i', 'i'
        );
	}

    /**
     * Delete from accounts
     *
     * @param int $product_id
     */
    public function delete_by_product( $product_id ) {
        parent::update( array( 'active' => 0 ), array( 'product_id' => $product_id ), 'i', 'i' );
    }

    /**
     * Remove Discontinued products
     *
     * @param int $account_id
     */
    public function remove_discontinued( $account_id ) {
        $this->prepare(
            "UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) SET wp.`active` = 0 WHERE wp.`website_id` = :account_id AND wp.`active` = 1 AND p.`status` = 'discontinued'"
            , 'i'
            , array( ':account_id' => $account_id )
        )->query();
    }

    /**
	 * Gets the data for an autocomplete request by account
	 *
	 * @param string $query
     * @param string|array $field
     * @param int $account_id
     * @param bool $custom_products [optional]
	 * @return array
	 */
	public function autocomplete_all( $query, $field, $account_id, $custom_products = false ) {
        $where = '';

        // Support more than one field
		if ( is_array( $field ) ) {
			// The initial and last parent are needed due to the multiple static-WHERE's
			foreach ( $field as $f ) {
				$where .= ( empty( $where ) ) ? ' AND ( ' : ' OR ';

				$where .= "`{$f}` LIKE " . $this->quote( '%' . $query . '%' );
			}

			// Close the open paren
			$where .= ' )';
		} else {
			$where = " AND `{$field}` LIKE " . $this->quote( '%' . $query . '%' );
		}

        if ( $custom_products )
            $where .= ' AND p.`website_id` = ' . (int) $account_id;


        return $this->prepare(
            "SELECT DISTINCT p.`product_id` AS value, p.`$field` AS name FROM `products` AS p LEFT JOIN `website_industries` as wi ON ( wi.`industry_id` = p.`industry_id` ) WHERE p.`publish_visibility` = 'public' AND ( p.`website_id` = 0 OR p.`website_id` = :account_id ) $where ORDER BY `$field` LIMIT 10"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
	 * Gets the data for an autocomplete request by account
	 *
	 * @param string $query
     * @param string|array $field
     * @param int $account_id
	 * @return array
	 */
	public function autocomplete_by_account( $query, $field, $account_id ) {
        $where = '';

        // Support more than one field
		if ( is_array( $field ) ) {
			// The initial and last parent are needed due to the multiple static-WHERE's
			foreach ( $field as $f ) {
				$where .= ( empty( $where ) ) ? ' AND ( ' : ' OR ';

				$where .= "`{$f}` LIKE " . $this->quote( '%' . $query . '%' );
			}

			// Close the open paren
			$where .= ' )';
		} else {
			$where = " AND `{$field}` LIKE " . $this->quote( '%' . $query . '%' );
		}

        return $this->prepare(
            "SELECT DISTINCT p.`product_id` AS value, p.`$field` AS name FROM `website_products` AS wp INNER JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_industries` as wi ON ( wi.`industry_id` = p.`industry_id` ) WHERE p.`publish_visibility` = 'public' AND wp.`website_id` = :account_id AND wp.`blocked` = 0 AND wp.`active` = 1 $where ORDER BY `$field` LIMIT 10"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Get all information of the products
     *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
     * @return Product[]
     */
    public function list_products( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT p.`product_id`, p.`name`, p.`sku`, p.`status`, b.`name` AS brand FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN website_products AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`active` = 1 $where GROUP BY p.`product_id` $order_by LIMIT $limit"
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
    public function count_products( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT p.`product_id` ) FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN website_products AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`active` = 1 $where GROUP BY p.`product_id`"
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
    public function list_product_prices( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT wp.`product_id`, wp.`alternate_price`, wp.`price`, wp.`sale_price`, wp.`alternate_price_name`, wp.`price_note`, p.`sku` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00' $where GROUP BY wp.`product_id` $order_by LIMIT $limit"
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
    public function count_product_prices( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( wp.`product_id` ) FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00' $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }

    /**
     * Set Product Prices
     *
     * @param int $account_id
     * @param array $values
     */
    public function set_product_prices( $account_id, array $values ) {
         // Prepare statement
        $statement = $this->prepare_raw( "UPDATE `website_products` SET `alternate_price` = :alternate_price, `price` = :price, `sale_price` = :sale_price, `alternate_price_name` = :alternate_price_name, `price_note` = :price_note WHERE `website_id` = :account_id AND `blocked` = 0 AND `active` = 1 AND `product_id` = :product_id" );
        $statement
            ->bind_param( ':alternate_price', $alternate_price, PDO::PARAM_INT )
            ->bind_param( ':price', $price, PDO::PARAM_INT )
            ->bind_param( ':sale_price', $sale_price, PDO::PARAM_INT )
            ->bind_param( ':alternate_price_name', $alternate_price_name, PDO::PARAM_STR )
            ->bind_param( ':price_note', $price_note, PDO::PARAM_STR )
            ->bind_value( ':account_id', $account_id, PDO::PARAM_INT )
            ->bind_param( ':product_id', $product_id, PDO::PARAM_INT );

        foreach ( $values as $product_id => $array ) {
            // Make sure all values have a value
            $alternate_price = 0;
            $price = 0;
            $sale_price = 0;
            $alternate_price_name = '';
            $price_note = '';

            // Get the values
            extract( $array );

            $statement->query();
        }
    }
}
