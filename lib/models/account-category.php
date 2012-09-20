<?php
class AccountCategory extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_categories' );
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
        $category_ids = $this->get_category_ids( $account_id );
		$website_category_ids = $this->get_website_category_ids( $account_id );

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
			if ( !in_array( $cid, $website_category_ids ) && !in_array( $cid, $new_category_ids ) )
				$new_category_ids[] = $cid;

			// Get the parent categories of this category
			$parent_category_ids = $category->get_all_parent_category_ids( $cid );

			// Loop through parent ids
			if ( is_array( $parent_category_ids ) )
			foreach ( $parent_category_ids as $pcid ) {
				// Forming complete list
				$product_category_ids[] = $pcid;

				// If the website does not already have it and it has not already been added
				if ( !in_array( $pcid, $website_category_ids ) && !in_array( $pcid, $new_category_ids ) )
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
			if ( !in_array( $wcid, $product_category_ids ) )
				$remove_category_ids[] = $wcid;
		}

		// Bulk add categories
		$this->bulk_add_categories( $account_id, $new_category_ids, $category );

		// Remove extra categoryes
        if ( count( $remove_category_ids ) > 0 )
            $this->remove_categories( $account_id, $remove_category_ids );
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

		// If there are any categories that need to be added
		$category_images = ar::assign_key( $this->get_website_category_images( $account_id, $category_ids ), 'category_id', true );

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

						// Don't need to loop any furhter
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
	protected function remove_categories( $account_id, array $category_ids ) {
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
            "SELECT DISTINCT b.`category_id` FROM `website_products` AS a LEFT JOIN `product_categories` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `products` AS c ON ( a.`product_id` = c.`product_id` ) WHERE a.`website_id` = :account_id AND a.`active` = 1 AND c.`publish_visibility` = 'public'"
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
     * Get Website Category Images
     *
     * @param int $account_id
     * @param array $category_ids
     * @return array
     */
    protected function get_website_category_images( $account_id, $category_ids ) {
        // Protection
        foreach ( $category_ids as &$cid ) {
            $cid = (int) $cid;
        }

        return $this->prepare(
            "SELECT a.`category_id`, CONCAT( 'http://', c.`name`, '.retailcatalog.us/products/', b.`product_id`, '/small/', d.`image` ) FROM `product_categories` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) LEFT JOIN `industries` AS c ON ( b.`industry_id` = c.`industry_id` ) LEFT JOIN `product_images` AS d ON ( b.`product_id` = d.`product_id` ) LEFT JOIN `website_products` AS e ON ( b.`product_id` = e.`product_id` ) WHERE a.`category_id` IN(" . implode( ',', $category_ids ) . ") AND b.`publish_visibility` = 'public' AND b.`status` <> 'discontinued' AND d.`sequence` = 0 AND e.`website_id` = :account_id AND e.`product_id` IS NOT NULL GROUP BY a.`category_id`"
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
            $images[] = $va['image'];
        }

        $this->prepare(
            "INSERT INTO `website_categories` ( `website_id`, `category_id`, `image_url` ) VALUES $values ON DUPLICATE KEY UPDATE `category_id` = VALUES( `category_id` )"
            , str_repeat( 's', count( $values_array ) )
            , $images
        )->query();
    }
}
