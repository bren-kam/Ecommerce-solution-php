<?php
class AccountProduct extends ActiveRecordBase {
    // Columns
    public $website_id, $product_id, $alternate_price, $alternate_price_name, $price;

    // Artificial columns
    public $link, $industry;

    // Columns from other tables
    public $category_id, $brand, $slug, $sku, $name, $image;

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
		$sql = 'SELECT DISTINCT COUNT( p.`product_id` )';
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
	 * Add Bulk
	 *
	 * @param int $account_id
     * @param array $industry_ids
	 * @param array $product_skus
	 */
	public  function add_bulk( $account_id, array $industry_ids, array $product_skus ) {
        // Make sure they entered in SKUs
        if ( 0 == count( $product_skus ) || 0 == $industry_ids )
            return;

        // Make account id safe
        $account_id = (int) $account_id;

        // Make industry IDs safe
        foreach ( $industry_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $industry_ids_sql = implode( ',', $industry_ids );

        // Split into chunks so we can do queries one at a time
		$product_sku_chunks = array_chunk( $product_skus, 500 );

		foreach ( $product_sku_chunks as $product_skus ) {
            // Get the count
            $product_sku_count = count( $product_skus );

			// Turn it into a string
			$product_skus_sql = '?' . str_repeat( ',?', $product_sku_count - 1 );

			// Magical Query
			// Insert website products
			$this->prepare(
                "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $account_id, `product_id` FROM `products` WHERE `industry_id` IN( $industry_ids_sql ) AND `publish_visibility` = 'public' AND `status` <> 'discontinued' AND `sku` IN ( $product_skus_sql ) GROUP BY `sku` ON DUPLICATE KEY UPDATE `active` = 1"
                , str_repeat( 's', $product_sku_count )
                , $product_skus
            )->query();
		}
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
     * Remove product
     */
    public function remove() {
        $this->prepare(
            'UPDATE `website_products` SET `active` = 0 WHERE `product_id` = :product_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':product_id' => $this->product_id, ':account_id' => $this->website_id )
        )->query();
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
        );
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
}
