<?php
class AccountCategory extends ActiveRecordBase {
    public $website_id, $category_id, $product_id, $title, $content, $meta_title, $meta_description, $meta_keywords, $image_url, $top, $header_script, $date_updated;

    // Available from other tables
    public $parent_category_id, $slug;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_categories' );
    }

    /**
     * Get
     *
     * @param int $account_id
     * @param int $category_id
     * @return array
     */
    public function get( $account_id, $category_id ) {
        $this->prepare(
            "SELECT wc.`website_id`, wc.`category_id`, IF ( '' = wc.`title`, c.`name`, wc.`title` ) AS title, wc.`content`, wc.`meta_title`, wc.`meta_description`, wc.`meta_keywords`, wc.`image_url`, wc.`top`, c.`slug`, wc.`header_script` FROM `website_categories` AS wc LEFT JOIN `categories` AS c ON ( c.`category_id` = wc.`category_id` ) WHERE wc.`website_id` = :account_id AND wc.`category_id` = :category_id"
            , 'ii'
            , array( ':account_id' => $account_id, ':category_id' => $category_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get All
     *
     * @param int $account_id
     * @return array
     */
    public function get_all( $account_id ) {
        return $this->prepare(
            "SELECT wc.`website_id`, wc.`category_id`, IF ( '' = wc.`title`, c.`name`, wc.`title` ) AS title, wc.`content`, wc.`meta_title`, wc.`meta_description`, wc.`meta_keywords`, wc.`image_url`, wc.`top`, c.`slug`, wc.`header_script` FROM `website_categories` AS wc LEFT JOIN `categories` AS c ON ( c.`category_id` = wc.`category_id` ) WHERE wc.`website_id` = :account_id"
            , 'ii'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Get All IDs
     *
     * @param int $account_id
     * @return array
     */
    public function get_all_ids( $account_id ) {
        return $this->prepare(
            'SELECT DISTINCT wc.`category_id` FROM `website_categories` AS wc LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`category_id` = wc.`category_id` AND wbc.`website_id` = wc.`website_id` ) WHERE wc.`website_id` = :account_id AND wbc.`category_id` IS NULL'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'title' => strip_tags( $this->title )
            , 'content' => format::strip_only( $this->content, '<script>' )
            , 'meta_title' => strip_tags($this->meta_title)
            , 'meta_description' => strip_tags($this->meta_description)
            , 'meta_keywords' => strip_tags($this->meta_keywords)
            , 'product_id' => $this->product_id
            , 'image_url' => strip_tags($this->image_url)
            , 'top' => $this->top
            , 'header_script' => $this->header_script
        ), array(
            'website_id' => $this->website_id
        , 'category_id' => $this->category_id
        ), 'sssssisis', 'ii' );
    }

    /**
     * Hide
     *
     * @param int $account_id
     * @param array $category_ids
     */
    public function hide( $account_id, array $category_ids ) {
        $category = new Category();

        // Type Juggling
        $account_id = (int) $account_id;

        foreach ( $category_ids as &$cid ) {
            $cid = (int) $cid;

            // Hide Children
            $children = $category->get_by_parent( $cid );
            if ( $children ) {
                $children_to_block = array();
                foreach ( $children as $child ) {
                    $children_to_block[] = $child->category_id;
                }
                $this->hide( $account_id, $children_to_block );
            }
        }

        $values = "( $account_id, " . implode( " ), ( $account_id, ", $category_ids ) . ' )';

        // Insert into blocked list
        $this->query( "INSERT INTO `website_blocked_category` ( `website_id`, `category_id` ) VALUES $values ON DUPLICATE KEY UPDATE `website_id` = VALUES(`website_id`)" );
    }

    /**
     * Unhide
     *
     * @param int $account_id
     * @param array $category_ids
     */
    public function unhide( $account_id, array $category_ids ) {
        // Type Juggling
        $account_id = (int) $account_id;

        foreach ( $category_ids as &$cid ) {
            $cid = (int) $cid;
        }

        // Turn into usable format
        $category_ids = implode( ',', $category_ids );

        // Unhide categories
        $this->query( "DELETE FROM `website_blocked_category` WHERE `website_id` = $account_id AND `category_id` IN ( $category_ids )" );
    }

    /**
     * Deactivate products by account
     *
     * @param int $account_id
     */
    public function delete_by_account( $account_id ) {
        parent::delete( array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Reorganize Categories
     *
     * @param int $account_id
     * @param Category $category
     */
    public function reorganize_categories( $account_id, Category $category ) {
        // Get data
        $category->get_all();
        $category_ids = $this->get_category_ids( $account_id );
		$website_category_ids = $this->get_website_category_ids( $account_id );
        $blocked_categories = $this->get_blocked_website_category_ids( $account_id );

        // Incorporate all the child categories of the array;
        // @Fix perhaps we should include all of this at the time of blocking a category?
        foreach ( $blocked_categories as $cid ) {
            $child_categories = $category->get_all_children( $cid );

            /**
             * @var Category $child_category
             */
            foreach( $child_categories as $child_category ) {
                $blocked_categories[] = $child_category->id;
            }
        }

        $blocked_categories = array_unique( $blocked_categories );

        // Clean data
        if ( $key = array_search( NULL, $category_ids ) )
			unset( $category_ids[$key] );

		if ( $key = array_search( NULL, $website_category_ids ) )
			unset( $website_category_ids[$key] );

        // Now go through and decide what categories to add and what to delete
		$new_category_ids = $product_category_ids = $remove_category_ids = array();

		// Find out what categories we need to add
		if ( is_array( $category_ids ) )
		foreach ( $category_ids as $cid ) {
			if ( empty( $cid ) )
				continue;

			// Start forming complete list of product categories
			$product_category_ids[] = $cid;

			// If the website does not already have the category and it has not already been added
			if ( !in_array( $cid, $website_category_ids ) && !in_array( $cid, $new_category_ids ) && !in_array( $cid, $blocked_categories ) )
				$new_category_ids[] = $cid;

			// Get the parent categories of this category
			$parent_category_ids = $category->get_all_parent_category_ids( $cid );

			// Loop through parent ids
			if ( is_array( $parent_category_ids ) )
			foreach ( $parent_category_ids as $pcid ) {
				// Forming complete list
				$product_category_ids[] = $pcid;

				// If the website does not already have it and it has not already been added and it is not in the blocked list
				if ( !in_array( $pcid, $website_category_ids ) && !in_array( $pcid, $new_category_ids ) && !in_array( $pcid, $blocked_categories ) )
					$new_category_ids[] = $pcid;
			}
		}

		// Only want the unique values
		$product_category_ids = array_unique( $product_category_ids );

		// IF NULL exists, remove it
		if ( $key = array_search( NULL, $product_category_ids ) )
			unset( $product_category_ids[$key] );

		sort( $product_category_ids );

		foreach ( $website_category_ids as $wcid ) {
            // If it's not in their products or if it's blocked, remove
			if ( !in_array( $wcid, $product_category_ids ) || in_array( $wcid, $blocked_categories ) )
				$remove_category_ids[] = $wcid;
		}

		// Bulk add categories
		$this->bulk_add_categories( $account_id, $new_category_ids, $category );

		// Remove extra categoryes
        if ( count( $remove_category_ids ) > 0 )
            $this->remove_categories( $account_id, $remove_category_ids );

        // Set Images for Brands/Categories
        $this->update_brand_categories( $account_id );
	}

    /**
	 * Bulk Add categories
	 *
     * @param int $account_id
	 * @param array $category_ids
	 * @param Category $category
	 */
	protected function bulk_add_categories( $account_id, $category_ids, $category ) {
        if ( !is_array( $category_ids ) || 0 == count( $category_ids ) )
			return;

        $website_category_ids = array();

        foreach ( $category_ids as $cid ) {
            $child_categories = $category->get_all_children( $cid );

            foreach ( $child_categories as $cat ) {
                $website_category_ids[] = $cat->id;
            }

            $website_category_ids[] = $cid;
        }

        $website_category_ids = array_unique( $website_category_ids );

		// If there are any categories that need to be added
		$category_images = ar::assign_key( $this->get_website_category_images( $account_id, $website_category_ids ), 'category_id', true );

		// Create insert
		$values = array();

		foreach ( $category_ids as $cid ) {
			// If we have an image, use it
			if ( isset( $category_images[$cid] ) ) {
				$image = $category_images[$cid];
			} else {
				// If not, that means it is a parent category. Choose the first child category with an image, and use it

				// Get child categories
				$child_categories = $category->get_all_children( $cid );

                /**
                 * @var Category $cc
                 */
                // Find the first available image
                foreach ( $child_categories as $cc ) {
					if ( isset( $category_images[$cc->id] ) ) {
						// Assign the image
						$image = $category_images[$cc->id];

						// Don't need to loop any further
						break;
					}
				}
			}

            /**
             * @var string $image
             */
            // Create the values
            $values[] = array( 'account_id' => $account_id, 'category_id' => $cid, 'image' => $image );
		}

		// Add the values
		if ( count( $values ) > 0 )
            $this->add_categories_by_array( $values );
	}

    /**
	 * Remove Categories from a website
	 *
     * @param int $account_id
	 * @param array $category_ids
	 */
	public function remove_categories( $account_id, array $category_ids ) {
		// Make sure they're MySQL safe
		foreach ( $category_ids as &$cid ) {
			$cid = (int) $cid;
		}

		$this->prepare(
            'DELETE FROM `website_categories` WHERE `website_id` = :account_id AND `category_id` IN(' . implode( ',', $category_ids ) . ')'
            , 'i'
            , array( ':account_id' => $account_id )
        )->query();
	}

    /**
     * Get category ids for reorganize categories
     *
     * @param $account_id
     * @return array
     */
    protected function get_category_ids( $account_id ) {
        return $this->prepare(
            "SELECT DISTINCT p.`category_id` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public'"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    /**
     * Get website category ids for reorganize categories
     *
     * @param $account_id
     * @return array
     */
    protected function get_website_category_ids( $account_id ) {
        return $this->prepare(
            "SELECT DISTINCT `category_id` FROM `website_categories` WHERE `website_id` = :account_id"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    /**
     * Get blocked website category ids
     *
     * @param $account_id
     * @return array
     */
    public function get_blocked_website_category_ids( $account_id ) {
        return $this->prepare(
            "SELECT DISTINCT `category_id` FROM `website_blocked_category` WHERE `website_id` = :account_id"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    /**
     * Get Website Category Images
     *
     * @param int $account_id
     * @param array $category_ids
     * @return array
     */
    public function get_website_category_images( $account_id, $category_ids ) {
        // Protection
        $where = '';
        if ( $category_ids ) {
            foreach ($category_ids as &$cid) {
                $cid = (int)$cid;
            }
            $where = " AND p.`category_id` IN(" . implode( ',', $category_ids ) . ") ";
        }

        return $this->prepare(
            "SELECT p.`category_id`, CONCAT( 'http://', i.`name`, '.retailcatalog.us/products/', p.`product_id`, '/small/', pi.`image` ) FROM `products` AS p LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` ) WHERE p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND pi.`sequence` = 0 AND wp.`website_id` = :account_id AND wp.`product_id` IS NOT NULL AND wp.`active` = 1 $where GROUP BY p.`category_id`"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Add Categories by array
     *
     * @param array $values_array
     */
    protected function add_categories_by_array( array $values_array ) {
        $values = '';
        $images = array();

        foreach ( $values_array as $va ) {
            if ( !empty( $values ) )
                $values .= ', ';

            $values .= '( ' . (int) $va['account_id'] . ', ' . (int) $va['category_id'] . ", ? )";

            // Store that image
            $images[] = strip_tags($va['image']);
        }

        $this->prepare(
            "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES $values ON DUPLICATE KEY UPDATE `category_id` = VALUES( `category_id` )"
            , str_repeat( 's', count( $values_array ) )
            , $images
        )->query();
    }

    /**
	 * Get all
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return AccountCategory[]
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT wc.`category_id`, IF ( '' = wc.`title`, c.`name`, wc.`title` ) AS title, wc.`website_id`, wc.`product_id`, wc.`image_url`, wc.`date_updated`, c.`slug` FROM `website_categories` AS wc LEFT JOIN `categories` AS c ON ( c.`category_id` = wc.`category_id` ) WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'AccountCategory' );
	}

	/**
	 * Count all
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( wc.`category_id` )  FROM `website_categories` AS wc LEFT JOIN `categories` AS c ON ( c.`category_id` = wc.`category_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
     * Update Brand Caregories
     *
     * @param $account_id
     * @return array
     */
    protected function update_brand_categories( $account_id ) {
        return $this->prepare(
            "INSERT INTO website_brand_category(`website_id`, `brand_id`, `category_id`, `image_url`)
                SELECT wp.`website_id`, p.`brand_id`, p.`category_id`,
                    CASE SUBSTR(pi.`image`, 1, 4) WHEN 'http' THEN pi.`image` ELSE CONCAT( 'http://', i.`name`, '.retailcatalog.us/products/', p.`product_id`, '/small/', pi.`image` ) END
                FROM `products` p
                INNER JOIN `industries` i ON ( p.`industry_id` = i.`industry_id` )
                INNER JOIN `product_images` pi ON ( p.`product_id` = pi.`product_id` )
                INNER JOIN `website_products` wp ON ( p.`product_id` = wp.`product_id` )
                LEFT JOIN `website_blocked_category` wbc ON ( wp.`website_id` = wbc.`website_id` AND p.`category_id` = wbc.`category_id` )
                WHERE wp.`website_id` = :account_id AND pi.`sequence` = 0
                    AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`publish_visibility` = 'public'
                    AND wbc.`category_id` IS NULL
                GROUP BY p.`brand_id`, p.`category_id`
                ON DUPLICATE KEY UPDATE `image_url` = VALUES(`image_url`);
            "
            , 'i'
            , array( ':account_id' => $account_id )
        )->query();
    }

    /**
     * Update category image and linked product
     *
     * @param $website_id
     * @param $product_id
     * @return bool
     */
    public function reassign_image( $website_id, $product_id ){
        // Find distinct categories which got affected
        $categories = $this->prepare(
            "SELECT DISTINCT wc.`category_id`, wc.`website_id`
            FROM `website_categories` AS wc
            LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = wc.`product_id` AND wc.`website_id` = wp.`website_id` )
            LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` )
            WHERE wc.`website_id` = :website_id AND wp.`product_id` = :product_id AND ( wp.`blocked` = 1 OR wp.`active` = 0 OR  p.`publish_visibility` = 'deleted' OR p.`status` = 'discontinued' )"
            , 'i'
            , array( ':website_id' => $website_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountCategory' );

        foreach ( $categories as $category) {
            //Find a product which can be used to replace the image
            $product = $this->prepare(
                "SELECT wp.`product_id`, p.`name`, p.`category_id`, c.`parent_category_id`, c.`name`, pi.`image` AS image, i.`name` AS industry , p.`status`, p.`publish_visibility`
                FROM `website_products` AS wp
                LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` )
                LEFT JOIN `website_categories` as wc ON (wc.`category_id` = p.`category_id` AND wp.`website_id` = wc.`website_id` )
                LEFT JOIN `categories` as c ON ( wc.`category_id` = c.`category_id` )
                LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` )
                LEFT JOIN `industries` as i ON ( i.`industry_id` = p.`industry_id`)
                WHERE
                wp.`website_id` = :website_id
                AND (c.`category_id` = :category_id  OR c.`parent_category_id` = :category_id)
                AND p.`status` = 'in-stock' AND p.`publish_visibility` = 'public'
                ORDER BY  p.`category_id` ASC LIMIT 1"
                , 'ii'
                , array( ':website_id' => $website_id, ':category_id' => $category->category_id )
            )->get_row( PDO::FETCH_CLASS, 'Product' );

            if( $product ){
                $image_url = $product->get_image_url($product->image, 'small', $product->industry, $product->product_id );

                $account_category = new AccountCategory();
                $account_category->get($category->website_id, $category->category_id);

                $account_category->product_id = $product->product_id;
                $account_category->image_url = $image_url;
                $account_category->save();
            }
        }

        return $categories;
    }

    /**
     * Get Website Category with Stale Images
     *
     * @param int $account_id
     * @return array
     */
    public function get_all_with_stale_images( ) {
        return $this->get_results(
            "SELECT wc.`website_id`, wc.`category_id`, p.`product_id`, wc.`title` , wc.`image_url`, wp.`blocked`, wp.`active`, p.`publish_visibility`
            FROM `website_categories` AS wc
            LEFT JOIN `website_products` AS wp ON (wp.`product_id` = wc.`product_id` AND wc.`website_id`=wp.`website_id`)
            LEFT JOIN `products` AS p ON (p.`product_id` = wp.`product_id`)
            WHERE
            (wp.`blocked`=1 OR wp.`active`=0 OR p.`publish_visibility`='deleted' OR p.`publish_visibility`='discontinued' )
            ORDER BY wc.`title`", PDO::FETCH_ASSOC );
    }

}
