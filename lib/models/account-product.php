<?php
class AccountProduct extends ActiveRecordBase {
    const BLOCKED = 1;
    const UNBLOCKED = 0;
    const ACTIVE = 1;
    const INACTIVE = 0;
    const ON_SALE = 1;
    const OFF_SALE = 0;

    // Columns
    public $website_id, $product_id, $alternate_price, $price, $sale_price, $wholesale_price, $inventory
        , $additional_shipping_amount, $weight, $additional_shipping_type
        , $alternate_price_name, $meta_title, $meta_description, $meta_keywords, $price_note
        , $product_note, $ships_in, $store_sku, $warranty_length, $alternate_price_strikethrough
        , $display_inventory, $inventory_tracking, $on_sale, $status, $sequence, $blocked, $active, $manual_price, $date_updated, $setup_fee;

    // Artificial columns
    public $link, $industry, $coupons, $created_by, $count;

    // Other columns
    public $description;

    /**
     * @var ProductOption[]
     */
    protected $product_options;

    // Columns from other tables
    public $brand_id, $category_id, $category, $parent_category, $brand, $slug, $sku, $name, $image, $price_min;

    public $four_piece_skus = array( 'D265-01','D313-01','D314-01','D315-01','D389-01','D408-01','D314-124','D329-124','D542-024','D542-030' );
    public $one_piece_skus = array( 'D299-08','D328-320','D553-124','D553-130','D569-224','D542-124','D542-130','D608-624','D608-630' );

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_products' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'product_id' => $this->product_id
            , 'alternate_price' => $this->alternate_price
            , 'price' => $this->price
            , 'sale_price' => $this->sale_price
            , 'wholesale_price' => $this->wholesale_price
            , 'inventory' => $this->inventory
            , 'active' => $this->active
        ), 'iiddddii', true );
    }

    /**
     * Get
     *
     * @param int $product_id
     * @param int $account_id
     */
    public function get( $product_id, $account_id ) {
        $this->prepare(
            'SELECT wp.*, p.`price_min` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`product_id` = :product_id AND wp.`website_id` = :account_id'
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
            "SELECT p.`product_id`, p.`sku`, p.`name`, p.`description`, p.`price` AS price_wholesale, p.`price_min` as price_map, wp.`price`, wp.`sale_price` AS price_sale, wp.`alternate_price` AS price_msrp, i.`name` AS industry, c.`category_id`, c.`name` AS category, COALESCE( c2.`name`, '' ) AS parent_category, b.`name` AS brand, CONCAT('http://', i.`name`, '.retailcatalog.us/products/', p.`product_id`, '/large/', pi.`image` ) AS image, u.`contact_name` AS created_by FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `categories` AS c2 ON ( c2.`category_id` = c.`parent_category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) WHERE wp.`website_id` = :account_id AND wp.`status` = 1 AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' AND ( p.`parent_product_id` IS NULL OR p.`parent_product_id` = 0 ) AND pi.`sequence` = 0 GROUP BY wp.`product_id`"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountProduct' );
    }

    /**
     * Get By IDs
     *
     * @param int[] $product_ids
     * @param int $account_id
     */
    public function get_by_ids( $product_ids, $account_id ) {

        if ( empty( $product_ids) )
            return array();

        $product_ids_sql = implode( ',', $product_ids );

        $this->prepare(
            'SELECT wp.*, p.`price_min` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`product_id` = :product_id AND wp.`website_id` = :account_id'
            , 'ii'
            , array( ':product_id' => $product_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get Auto Price Candidates
     *
     * @param int $account_id
     * @return array
     */
    public function get_auto_price_count( $account_id ) {
        return $this->prepare(
            "SELECT b.`name` AS brand, COUNT( wp.`product_id` ) AS count FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) INNER JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND p.`price` > 0 AND wbc.`category_id` IS NULL AND wp.`manual_price` = 0 GROUP BY b.`brand_id`"
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Get Auto Price Example
     *
     * @param int $account_id
     */
    public function get_auto_price_example( $account_id ) {
        $this->prepare(
            "SELECT wp.*, p.`price_min` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND p.`price` > 0 AND wbc.`category_id` IS NULL AND wp.`manual_price` = 0 LIMIT 1"
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get Auto Price Example
     *
     * @param int $account_id
     * @return array
     */
    public function get_auto_priceable_brands( $account_id ) {
        return $this->prepare(
            "SELECT DISTINCT p.`brand_id` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND p.`price` > 0 AND wbc.`category_id` IS NULL"
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->get_col();
    }

    /**
     * Get Non-Auto Price Candidates
     *
     * @param int $account_id
     * @return AccountProduct[]
     */
    public function get_non_autoprice_products( $account_id ) {
        return $this->prepare(
            "SELECT p.`sku`, p.`name`, wp.`price`, wp.`price_note` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND (p.`price` = 0 OR wp.`manual_price` = 1) AND wbc.`category_id` IS NULL"
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->get_results( PDO::FETCH_CLASS, 'AccountProduct' );
    }

    /**
     * Get Auto Price Candidates
     *
     * @param array $category_ids
     * @param int $brand_id
     * @param float $price
     * @param float $sale_price
     * @param float $alternate_price
     * @param float $price_ending
     * @param int $account_id
     */
    public function auto_price( array $category_ids, $brand_id, $price, $sale_price, $alternate_price, $price_ending, $account_id ) {
        if ( empty( $category_ids ) )
            return;

        // Setup variables
        $set = array();
        $run_2pc = false;

        $double_categories = array(
            132 // Dining Room > Side Chairs
            , 131 // Dining Room > Arm Chairs
            , 142 // Dining Room > Bar Stools
        );

        // Round to the ending
        $price_ending = number_format( (float) $price_ending, 2 );

        // Won't do anything, has to be lower
        if ( $price < 0 ) {
            $set[] = 'wp.`price` = 0.01';
        } elseif ( 0 != $price ) {
            $set[] = 'wp.`price` = ceilEnding( p.`price` * ( 1 + ' . (float) $price . ' ), ' . $price_ending . ')';
        }

        if ( $sale_price < 0 ) {
            $set[] = 'wp.`sale_price` = 0.01';
        } elseif ( 0 != $sale_price ) {
            $set[] = 'wp.`sale_price` = ceilEnding( p.`price` * ( 1 + ' . (float) $sale_price . ' ), ' . $price_ending . ')';
        }

        if ( $alternate_price < 0 ) {
            $set[] = 'wp.`alternate_price` = 0.01';
        } elseif ( 0 != $alternate_price ) {
            $set[] = 'wp.`alternate_price` = ceilEnding( p.`price` * ( 1 + ' . (float) $alternate_price . ' ), ' . $price_ending . ')';
        }

        if ( $price_ending < 0 )
            $price_ending = 0;

        if ( empty( $set ) )
            return;

        // Protect Category IDS from DB
        foreach ( $category_ids as &$category_id ) {
            $category_id = (int) $category_id;

            // See if we have to run the categories
            if ( in_array( $category_id, $double_categories ) )
                $run_2pc = true;
        }

        $set = implode( ',', $set );
        $category_ids_string = implode( ',', $category_ids );


        $where = '';
        $inner_join = '';
        // Add the where by Brand
        if ( $brand_id > 0 && $brand_id != Brand::ARTIFICIAL_ASHLEY_EXPRESS ) {
            $where = ' AND p.`brand_id` = ' . (int) $brand_id;
        } else if ( $brand_id == Brand::ARTIFICIAL_ASHLEY_EXPRESS ) {
            // Ashley Express Products
            $inner_join = 'INNER JOIN `website_product_ashley_express` wpae ON ( p.`product_id` = wpae.`product_id` AND wpae.`website_id` = wp.`website_id` ) ';
        }

        // Run once
        $this->prepare(
            "UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) {$inner_join} SET {$set} WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND p.`price` > 0 AND wp.`manual_price` = 0 AND wbc.`category_id` IS NULL AND p.`category_id` IN($category_ids_string)" . $where
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->query();

        if ( $run_2pc ) {
            $this->auto_price_multiple($category_ids, $brand_id, $price, $sale_price, $alternate_price, $price_ending, $account_id, 2);
            $this->auto_price_multiple($category_ids, $brand_id, $price, $sale_price, $alternate_price, $price_ending, $account_id, 4, " AND p.`sku` IN('" . implode( "','", $this->four_piece_skus ) . "')" );
            $this->auto_price_multiple($category_ids, $brand_id, $price, $sale_price, $alternate_price, $price_ending, $account_id, 1, " AND p.`sku` IN('" . implode( "','", $this->one_piece_skus ) . "')" );
        }
    }

    /**
     * Auto Price 2PC Categories
     *
     * @param array $category_ids
     * @param int $brand_id
     * @param float $price
     * @param float $sale_price
     * @param float $alternate_price
     * @param float $price_ending
     * @param int $account_id
     * @param int $multiple [optional]
     * @param string $where [optional]
     **/
    protected function auto_price_multiple( array $category_ids, $brand_id, $price, $sale_price, $alternate_price, $price_ending, $account_id, $multiple = 2, $where = '' ) {
        // These categories need to be doubled in price because the items are sold in couples
        $double_categories = array(
            132 // Dining Room > Side Chairs
            , 131 // Dining Room > Arm Chairs
            , 142 // Dining Room > Bar Stools
        );
        // 2pc only works for Ashley products
        $ashley_brand_ids = array(8, 170, 171, 588, 805, Brand::ARTIFICIAL_ASHLEY_EXPRESS);
        if ( $brand_id != 0 && !in_array( (int)$brand_id, $ashley_brand_ids ) )
            return;

        $set = array();
        $multiple = (int) $multiple;

        // Round to the ending
        $price_ending = number_format( (float) $price_ending, 2 );

        if ( $price > 0 )
            $set[] = 'wp.`price` = ceilEnding( p.`price` * ' . $multiple  . ' * ( 1 + ' . (float) $price . ' ), ' . $price_ending . ')';

        if ( $sale_price > 0 )
            $set[] = 'wp.`sale_price` = ceilEnding( p.`price` * ' . $multiple  . ' * ( 1 + ' . (float) $sale_price . ' ), ' . $price_ending . ')';

        if ( $alternate_price > 0 )
            $set[] = 'wp.`alternate_price` = ceilEnding( p.`price` * ' . $multiple  . ' * ( 1 + ' . (float) $alternate_price . ' ), ' . $price_ending . ')';

        if ( empty( $set ) )
            return;

        $new_category_ids = array();

        // Protect Category IDS from DB
        foreach ( $category_ids as &$category_id ) {
            $category_id = (int) $category_id;

            if ( in_array( $category_id, $double_categories ) )
                $new_category_ids[] = $category_id;
        }

        if ( empty( $new_category_ids ) )
            return;

        $set = implode( ',', $set );
        $new_category_ids_string = implode( ',', $new_category_ids );

        // Add the where
        $inner_join = '';
        // Add the where by Brand
        if ( is_numeric($brand_id) && $brand_id > 0 && $brand_id != Brand::ARTIFICIAL_ASHLEY_EXPRESS ) {
            $where .= ' AND p.`brand_id` = ' . (int) $brand_id;
        } else if ( is_numeric($brand_id) && $brand_id == Brand::ARTIFICIAL_ASHLEY_EXPRESS ) {
            // Ashley Express Products
            $inner_join = 'INNER JOIN `website_product_ashley_express` wpae ON ( p.`product_id` = wpae.`product_id` AND wpae.`website_id` = wp.`website_id` ) ';
        } else {
            // 2pc only applied to Ashley products
            $where .= ' AND p.`brand_id` IN ('. implode(',', $ashley_brand_ids) .') ';
        }

        // Run once
        $this->prepare(
            "UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) {$inner_join} SET {$set} WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND p.`price` > 0 AND wp.`manual_price` = 0 AND wbc.`category_id` IS NULL AND p.`category_id` IN($new_category_ids_string)" . $where
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->query();
    }

    /**
     * Get Max price
     *
     * @param int $account_id
     * @return float
     */
    public function get_max_price( $account_id ) {
        return $this->prepare(
            "SELECT ROUND( MAX( combined.price ), 2 ) FROM ( SELECT IF ( wp.`sale_price` > 0, wp.`sale_price`, wp.`price` ) AS price FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`category_id` = p.`category_id` AND wbc.`website_id` = wp.`website_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = 'public' ) AS combined"
            , 'iii'
            , array( ':account_id' => $account_id, ':blocked' => self::UNBLOCKED, ':active' => self::ACTIVE )
        )->get_var();
    }

    /**
     * Get Count
     *
     * @param int $account_id
     * @return int
     */
    public function count( $account_id ) {
        $account_id = (int) $account_id;

        return $this->get_var( "SELECT COUNT( DISTINCT p.`product_id` ) FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN website_products AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`blocked` = 0 AND wp.`active` = 1 AND ( p.`website_id` = 0 || p.`website_id` = $account_id ) AND wp.`website_id` = $account_id AND p.`publish_visibility` = 'public' AND p.`parent_product_id` IS NULL AND p.`publish_date` <> '0000-00-00 00:00:00'" );
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
            , 'alternate_price_name' => strip_tags($this->alternate_price_name)
            , 'price_note' => strip_tags($this->price_note)
            , 'product_note' => strip_tags($this->product_note)
            , 'warranty_length' => strip_tags($this->warranty_length)
            , 'display_inventory' => $this->display_inventory
            , 'inventory_tracking' => $this->inventory_tracking            
            , 'on_sale' => $this->on_sale
            , 'status' => strip_tags($this->status)
            , 'meta_title' => strip_tags($this->meta_title)
            , 'meta_description' => strip_tags($this->meta_description)
            , 'meta_keywords' => strip_tags($this->meta_keywords)
            , 'wholesale_price' => strip_tags($this->wholesale_price)
            , 'additional_shipping_amount' => $this->additional_shipping_amount
            , 'weight' => strip_tags($this->weight)
            , 'additional_shipping_type' => strip_tags($this->additional_shipping_type)
            , 'ships_in' => strip_tags($this->ships_in)
            , 'store_sku' => strip_tags($this->store_sku)
            , 'active' => $this->active
            , 'manual_price' => $this->manual_price
            , 'setup_fee' => $this->setup_fee
        ), array(
            'website_id' => $this->website_id
            , 'product_id' => $this->product_id
        ), 'dddissssiiissssddiissidd', 'ii' );
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
     * @param string $order_by (optional) a 'ORDER BY' clause to add
     * @param int $page (optional) page number
	 * @return AccountProduct[]
	 */
	public function search( $account_id, $limit = 20, $where = '', $order_by = '', $page = 1 ) {

        $sql_order_by = ($order_by ? "$order_by, " : "") . "wp.`sequence` ASC";

        if ( 0 == $limit ) {
            $sql_limit = '';
        } else {
            $starting_product = ( $page - 1 ) * $limit;
            $sql_limit = "LIMIT $starting_product, $limit";
        }

        $sql = 'SELECT p.`product_id`,';
        $sql .= 'p.`name`, p.`slug`, b.`name` AS brand, p.`sku`, p.`status`, c.`category_id`,';
        $sql .= 'c.`name` AS category, pi.`image`, wp.`price`, wp.`alternate_price`, wp.`alternate_price_name`,';
        $sql .= 'wp.`sequence`, DATE( p.`publish_date` ) AS publish_date, pi.`image`, i.`name` AS industry, p.`brand_id` ';
        $sql .= 'FROM `products` AS p ';
        $sql .= 'LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) ';
        $sql .= 'LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) ';
        $sql .= 'LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) ';
        $sql .= 'LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) ';
        $sql .= 'LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) ';
        $sql .= 'LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`category_id` = p.`category_id` AND wbc.`website_id` = wp.`website_id` ) ';
        $sql .= "WHERE p.`publish_visibility` = 'public' AND wp.`blocked` = 0 AND wp.`active` = 1 AND wp.`website_id` = $account_id AND ( pi.`sequence` = 0 OR pi.`sequence` IS NULL ) AND p.`date_created` <> '0000-00-00 00:00:00' AND p.`parent_product_id` IS NULL AND wbc.`category_id` IS NULL";
        $sql .= $where;
        $sql .= " GROUP BY p.`product_id`";
        $sql .= " ORDER BY $sql_order_by ";
        $sql .= " $sql_limit";

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
		$sql .= 'LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) ';
		$sql .= 'LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) ';
		$sql .= 'LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) ';
		$sql .= 'LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) ';
		$sql .= "WHERE p.`publish_visibility` = 'public' AND wp.`blocked` = 0 AND wp.`active` = 1 AND wp.`website_id` = $account_id AND ( pi.`sequence` = 0 OR pi.`sequence` IS NULL ) AND p.`date_created` <> '0000-00-00 00:00:00' AND p.`parent_product_id` IS NULL ";
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
                , 'price' => NULL
                , 'sale_price' => NULL
                , 'alternate_price' => NULL
                , 'on_sale' => NULL
                , 'sequence' => NULL
                , 'active' => NULL
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
     * Fetch product by SKU
     *
     * @param int $account_id
     * @param string $sku
     * @return array
     */
    public function get_by_sku( $account_id, $sku){
        if( empty( $account_id ) || empty ( $sku ) )
            return;

        $account_id = (int) $account_id;

        return $this->prepare(
                'SELECT p.`product_id`, p.`name`, p.`sku` FROM `website_products` AS wp LEFT JOIN `products` AS p  ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`website_id` = :account_id AND p.`sku` LIKE :sku'
                , 'i'
                , array( ':account_id' => $account_id, ':sku' => $sku.'%'  )
            )->get_results( PDO::FETCH_CLASS, 'Product' );
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
            "SELECT DISTINCT p.`product_id` AS value, p.`$field` AS name FROM `products` AS p LEFT JOIN `website_industries` as wi ON ( wi.`industry_id` = p.`industry_id` ) WHERE p.`publish_visibility` = 'public' AND p.`parent_product_id` IS NULL AND ( p.`website_id` = 0 OR p.`website_id` = :account_id ) $where ORDER BY `$field` LIMIT 10"
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
	public function autocomplete_by_account( $query, $field, $account_id, $limit = 10 ) {
        $where = '';

        // Support more than one field
		if ( is_array( $field ) ) {
            $select_fields = array();
            $statements = array();
			foreach ( $field as $f ) {
                $select_fields[] = "p.`$f` AS `$f`";
				$statements[] = "`{$f}` LIKE " . $this->quote( '%' . $query . '%' );
			}
            $select_fields = implode(', ', $select_fields);
            $where .= ' AND (' . implode(' OR ', $statements ) . ' ) ';
            $order_by = "`$f`";
		} else {
            $select_fields = "p.`$field` AS name" ;
			$where = " AND `{$field}` LIKE " . $this->quote( '%' . $query . '%' );
            $order_by = "`$field`";
		}

        $limit_sql = ( $limit > 0 ) ? " LIMIT $limit " : "";

        return $this->prepare(
            "SELECT DISTINCT p.`product_id` AS value, $select_fields FROM `website_products` AS wp INNER JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_industries` as wi ON ( wi.`industry_id` = p.`industry_id` ) WHERE p.`publish_visibility` = 'public' AND p.`parent_product_id` IS NULL AND wp.`website_id` = :account_id AND wp.`blocked` = 0 AND wp.`active` = 1 $where ORDER BY $order_by $limit_sql"
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
            "SELECT p.`product_id`, p.`name`, p.`sku`, p.`status`, b.`name` AS brand FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN website_products AS wp ON ( wp.`product_id` = p.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`category_id` = p.`category_id` AND wbc.`website_id` = wp.`website_id` ) WHERE wp.`active` = 1 AND wbc.`category_id` IS NULL AND p.`parent_product_id` IS NULL $where GROUP BY p.`product_id` $order_by LIMIT $limit"
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
            "SELECT COUNT( DISTINCT p.`product_id` ) FROM `products` AS p LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN website_products AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`active` = 1 AND p.`parent_product_id` IS NULL $where"
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
            "SELECT wp.`product_id`, wp.`alternate_price`, wp.`price`, wp.`sale_price`, wp.`alternate_price_name`, wp.`price_note`, p.`sku`, p.`name` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00' $where GROUP BY wp.`product_id` $order_by LIMIT $limit"
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
        $statement = $this->prepare_raw( "UPDATE `website_products` SET `alternate_price` = :alternate_price, `price` = :price, `sale_price` = :sale_price, `price_note` = :price_note WHERE `website_id` = :account_id AND `blocked` = 0 AND `active` = 1 AND `product_id` = :product_id" );
        $statement
            ->bind_param( ':alternate_price', $alternate_price, PDO::PARAM_INT )
            ->bind_param( ':price', $price, PDO::PARAM_INT )
            ->bind_param( ':sale_price', $sale_price, PDO::PARAM_INT )
            ->bind_param( ':price_note', $price_note, PDO::PARAM_STR )
            ->bind_value( ':account_id', $account_id, PDO::PARAM_INT )
            ->bind_param( ':product_id', $product_id, PDO::PARAM_INT );

        foreach ( $values as $product_id => $array ) {
            // Make sure all values have a value
            $alternate_price = $price = $sale_price = 0;
            $price_note = '';

            // Get the values
            extract( $array );

            $price_note = strip_tags($price_note);

            $statement->query();
        }
    }

    /**
     * Multiply Product Prices by Sku
     *
     * @param int $account_id
     * @param array $prices
     * @param float $price_multiplier
     * @param float $sale_price_multiplier
     * @param float $alternate_price_multiplier
     */
    public function multiply_product_prices_by_sku( $account_id, array $prices, $price_multiplier, $sale_price_multiplier, $alternate_price_multiplier ) {
        // Type Juggling
        $price_multiplier = (float) $price_multiplier;
        $sale_price_multiplier = (float) $sale_price_multiplier;
        $alternate_price_multiplier = (float) $alternate_price_multiplier;

         // Prepare statement
        $statement = $this->prepare_raw( "UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) SET wp.`price` = :price * :price_multiplier, wp.`sale_price` = :price_2 * :sale_price_multiplier, wp.`alternate_price` = :price_3 * :alternate_price_multiplier, wp.`price_note` = :price_note WHERE wp.`website_id` = :account_id AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`sku` = :sku" );
        $statement
            ->bind_value( ':account_id', $account_id, PDO::PARAM_INT )
            ->bind_param( ':sku', $sku, PDO::PARAM_STR )
            ->bind_param( ':price_note', $price_note, PDO::PARAM_STR )
            ->bind_param( ':price', $price, PDO::PARAM_INT )
            ->bind_value( ':price_multiplier', $price_multiplier, PDO::PARAM_INT )
            ->bind_param( ':price_2', $price, PDO::PARAM_INT )
            ->bind_value( ':sale_price_multiplier', $sale_price_multiplier, PDO::PARAM_INT )
            ->bind_param( ':price_3', $price, PDO::PARAM_INT )
            ->bind_value( ':alternate_price_multiplier', $alternate_price_multiplier, PDO::PARAM_INT );

        foreach ( $prices as $array ) {
            // Make sure all values have a value
            $price = 0;
            $sku = $price_note = '';

            // Get the values
            extract( $array );

            $statement->query();
        }
    }

    /**
     * Remove Discontinued Products
     */
    public function remove_all_discontinued() {
        // The websites that will need to reorganize their categories
        $website_ids = $this->get_discontinued_website_ids();

        // Remove the products
        $this->remove_all_discontinued_products();

        // Reorganize categories
        if ( !empty( $website_ids ) ) {
            $account_category = new AccountCategory();
            $category = new Category();

            // Cloudflare library
            library('cloudflare-api');

            foreach ( $website_ids as $website_id ) {
                $account_category->reorganize_categories( $website_id, $category );

                // Clear CloudFlare Cache
                $account = new Account();
                $account->get( $website_id );
                $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

                if ( $cloudflare_zone_id ) {
                    $cloudflare = new CloudFlareAPI( $account );
                    $cloudflare->purge( $cloudflare_zone_id );
                }
            }
        }
    }

    /**
     * Adjust to Minimum price
     *
     * @param int $account_id
     * @return int
     */
    public function adjust_to_minimum_price( $account_id ) {
        $this->prepare( 'UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) SET wp.`price` = IF( p.`price_min` > wp.`price` AND wp.`price` > 0, p.`price_min`, wp.`price` ), wp.`sale_price` = IF ( p.`price_min` > wp.`sale_price` AND wp.`sale_price` > 0, p.`price_min`, wp.`sale_price` ), wp.`alternate_price` = IF( p.`price_min` > wp.`alternate_price` AND wp.`alternate_price` > 0, p.`price_min`, wp.`alternate_price` ) WHERE wp.`website_id` = :website_id AND p.`price_min` > 0'
            , 'i'
            , array( ':website_id' => $account_id )
        )->query();

        return $this->get_row_count();
    }

    /**
     * Adjust to Minimum price
     *
     * @param array $category_ids
     * @param int $account_id
     * @param int $brand_id [optional]
     */
    public function reset_prices( array $category_ids, $account_id, $brand_id = NULL ) {
        if ( empty( $category_ids ) )
            return;

        // DB proof category ids
        foreach ( $category_ids as &$category_id ) {
            $category_id = (int) $category_id;
        }

        $category_ids = implode( ',', $category_ids );

        $where = ( $brand_id ) ? ' AND p.`brand_id` = ' . (int) $brand_id : '';

        $this->prepare( "UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) SET wp.`price` = 0, wp.`sale_price` = 0, wp.`alternate_price` = 0 WHERE wp.`website_id` = :account_id AND wp.`blocked` = :blocked AND wp.`active` = :active AND p.`publish_visibility` = :publish_visibility AND p.`price` > 0 AND wbc.`category_id` IS NULL AND p.`category_id` IN($category_ids)" . $where
            , 'iiis'
            , array(
                ':account_id' => $account_id
                , ':blocked' => self::UNBLOCKED
                , ':active' => self::ACTIVE
                , ':publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC
            )
        )->query();
    }

    /**
     * Get Discontinued Products Website IDs
     *
     * @return array
     */
    protected function get_discontinued_website_ids() {
        //  AND p.`timestamp` < DATE_SUB( NOW(), INTERVAL 60 DAY )
        return $this->prepare(
            "SELECT wp.`website_id` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`active` = :active AND p.`status` = 'discontinued'"
            , 'i'
            , array( ':active' => AccountProduct::ACTIVE )
        )->get_col();
    }

    /**
     * Get Discontinued Products Website IDs
     */
    protected function remove_all_discontinued_products() {
        //  AND p.`timestamp` < DATE_SUB( NOW(), INTERVAL 60 DAY )
        $this->query( "UPDATE `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) SET wp.`active` = 0 WHERE wp.`active` = 1 AND p.`status` = 'discontinued'" );
    }

    /**
     * Reset price by account
     *
     * @param int $account_id
     */
    public function reset_price_by_account( $account_id ) {
        parent::update( array(
            'alternate_price' => 0
            , 'price' => 0
            , 'sale_price' => 0
            , 'setup_fee' => 0
        ), array( 'website_id' => $account_id ), 'i', 'i' );
    }

    /**
     * Get Manually Priced By Account
     *
     * @param int $account_id
     * @return AccountProduct[]
     */
    public function get_manually_priced_by_account( $account_id ) {
        return $this->prepare(
            "SELECT wp.*, p.`sku`, p.`name`, p.`price_min`, c.`category_id` AS category_id, c.`name` AS category, COALESCE( c2.`name`, '' ) AS parent_category, b.`name` AS brand, u.`contact_name` AS created_by FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `categories` AS c ON ( c.`category_id` = p.`category_id` ) LEFT JOIN `categories` AS c2 ON ( c2.`category_id` = c.`parent_category_id` ) LEFT JOIN `brands` AS b ON ( b.`brand_id` = p.`brand_id` ) LEFT JOIN `users` AS u ON ( u.`user_id` = p.`user_id_created` ) WHERE wp.`website_id` = :account_id AND wp.`status` = 1 AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' AND wp.`manual_price` = 1 GROUP BY wp.`product_id`"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountProduct' );
    }

    /**
     * Null Manually Priced by Account
     *
     * Sets all products as no manually priced, so auto price tool can get them
     * @param $account_id
     */
    public function null_manually_priced_by_account( $account_id ) {
       $this->update(
           array( 'manual_price' => 0 )
           , array( 'website_id' => $account_id )
           , 'i'
           , 'i'
       );
    }

    /**
     * Lock Prices by Account
     *
     * Set all products as manually priced, so they can't be touched by Auto Price tool
     * @param $account_id
     */
    public function lock_prices_by_account( $account_id ) {
        $this->update(
            array( 'manual_price' => 1 )
            , array( 'website_id' => $account_id )
            , 'i'
            , 'i'
        );
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
            $product_id = $this->product_id;

        return 'http://' . str_replace( ' ', '', $industry ) . '.retailcatalog.us/products/' . $product_id . '/' . ($size ? ($size . '/') : '') .$image;
    }

    /**
     * Count Products By Brand Ids
     *
     * @param $website_id
     * @param $brand_ids
     * @return int
     */
    public function count_products_by_brand_ids($website_id, $brand_ids) {
        if ( empty($brand_ids) )
            return 0;

        return $this->get_var(
            "SELECT COUNT(*)
             FROM website_products wp
             INNER JOIN products p ON p.product_id = wp.product_id
             WHERE wp.website_id = {$website_id} AND p.brand_id IN (". implode(',', $brand_ids) .")"
        );
    }

    /**
     * Link to product_options
     *
     * @param bool $force_refresh [optional]
     * @return ProductOption[]|array
     */
    public function product_options( $force_refresh = false ){
        if ( $force_refresh || empty( $this->product_options ) ) {
            $product_option = new ProductOption();
            $product_options = $product_option->get_by_product( $this->website_id, $this->product_id );

            foreach ( $product_options as $product_option ) {
                $this->product_options[$product_option->id] = $product_option;
            }
        }

        return ( $this->product_options ) ? $this->product_options : array();
    }

    /**
     * Get Child Prices
     *
     * @param int $parent_product_id
     * @param int $website_id
     * @return array
     */
    public function get_child_prices( $parent_product_id, $website_id ) {
       return $this
           ->prepare(
               "SELECT p.`product_id`, p.`sku`, p.`name`, p.`price` as wholesale_price, p.`price_min` as map_price, wp.`price`, wp.`sale_price`, wp.`alternate_price`
                FROM `products` p
                LEFT JOIN `website_products` wp ON p.`product_id` = wp.`product_id`
                WHERE p.`parent_product_id` = :parent_product_id AND p.`website_id` = :website_id AND p.`publish_visibility` = 'public'"
               , "i"
               , [":parent_product_id" => $parent_product_id, ":website_id" => $website_id]
           )
           ->get_results(PDO::FETCH_ASSOC);
    }
}
