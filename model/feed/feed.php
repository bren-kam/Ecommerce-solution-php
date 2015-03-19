<?php
class Feed extends ActiveRecordBase {

    /**
     * @var APIKey $api_key
     */
    public $api_key;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
    }

    /**
     * Get Products
     *
     * @param string $start_date
     * @param string $end_date
     * @param int $starting_point
     * @param int $limit
     * @return array
     */
    public function get_products( $start_date, $end_date, $starting_point, $limit, $ashley_id ) {
        // Use the variables if necessary
        $inner_join = '';
        $where = '';

        if ( !empty( $start_date ) )
            $where .= " AND a.`timestamp` >= '" . $this->quote( $start_date ) . "'";

        if ( !empty( $end_date ) )
            $where .= " AND a.`timestamp` < '" . $this->quote( $end_date ) . "'";

        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
        if ( $brands ) {
            $where .= " AND p.`brand_id` IN (" . implode( ',', $brands ) . ") ";
        }
        
		$ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
		
		$valid_ashley_id = false;

		if ( $ashley_accounts ) {
            foreach ( $ashley_accounts as &$aa ) {
				if ( $ashley_id == $aa )
					$valid_ashley_id = true;
				
                $aa = "'$aa'";
            }
			
			if ( empty( $ashley_id ) ) {
				$inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
				$where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
			}
        }

        $starting_point = ( !empty( $starting_point ) ) ? $starting_point : 0;
        $limit = ( !empty( $limit ) ) ? $limit : 10000;

		
        if ( $limit > 10000000 )
            $limit = 10000000;
		
		if ( $valid_ashley_id && !empty( $ashley_id ) ) {
			$inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
			$where .= " AND ws.`value` = " . $this->quote( $ashley_id ) . " AND p.`user_id_created` IN ( 353, 1477 ) ";
			//$inner_join .= " LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id`)";
			//$where .= " AND wp.`website_id` = ( SELECT `website_id` FROM `website_settings` WHERE `key` = 'ashley-ftp-username' AND `value` = " . $this->quote( $encrypted_ashley_id ) . ' ) AND p.`brand_id` IN(8, 170, 171, 588, 805)';
		}		
		
        return $this->prepare(
            "SELECT p.`product_id`, p.`category_id` AS categories, p.`brand_id`, p.`industry_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`price` AS price_wholesale, p.`price_min` AS price_map, p.`weight`, p.`volume`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, p.`date_created`, p.`timestamp`, i.`name` AS industry, GROUP_CONCAT( DISTINCT pi.`image` ) AS images, GROUP_CONCAT( DISTINCT air.`attribute_item_id` ) AS attributes, GROUP_CONCAT( DISTINCT pgr.`product_group_id` ) AS product_groups FROM `products` AS p LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) LEFT JOIN `attribute_item_relations` AS air ON ( air.`product_id` = p.`product_id` ) LEFT JOIN `product_group_relations` AS pgr ON ( pgr.`product_id` = p.`product_id` ) $inner_join WHERE p.`publish_visibility` <> 'deleted' AND p.`website_id` = 0 $where GROUP BY p.`product_id` ORDER BY p.`product_id` LIMIT :starting_point, :limit"
            , 'ii'
            , array(
                ':starting_point' => (int) $starting_point
                , ':limit' => (int) $limit
            )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Get Brands
     *
     * @return array
     */
    public function get_brands() {
        $inner_join = '';
        $where = '';

        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
        if ( $brands ) {
            $where .= " AND b.`brand_id` IN (" . implode( ',', $brands ) . ") ";
        }
        $ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
        if ( $ashley_accounts ) {
            foreach ( $ashley_accounts as &$aa ) {
                $aa = "'$aa'";
            }
            $inner_join .= " INNER JOIN products p ON p.brand_id = b.brand_id INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
            $where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
        }

        return $this->get_results( "SELECT b.* FROM `brands` b $inner_join WHERE 1 $where GROUP BY b.`brand_id`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Categories
     *
     * @return array
     */
    public function get_categories() {
        $inner_join = '';
        $where = '';

        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
        if ( $brands ) {
            $inner_join .= " INNER JOIN products p ON p.category_id = c.category_id ";
            $where .= " AND p.`brand_id` IN (" . implode( ',', $brands ) . ") ";
        }
        $ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
        if ( $ashley_accounts ) {
            foreach ( $ashley_accounts as &$aa ) {
                $aa = "'$aa'";
            }
            if ( !$brands )
                $inner_join .= " INNER JOIN products p ON p.category_id = c.category_id ";
            $inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
            $where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
        }

        return $this->get_results( "SELECT c.`category_id`, c.`parent_category_id`, c.`name`, c.`slug`, c.`sequence`, c.`date_updated` FROM `categories` c $inner_join WHERE 1 $where GROUP BY c.category_id", PDO::FETCH_ASSOC );
    }

    /**
     * Get Industries
     *
     * @return array
     */
    public function get_industries() {
        $inner_join = '';
        $where = '';

        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
        if ( $brands ) {
            $inner_join .= " INNER JOIN products p ON p.industry_id = i.industry_id ";
            $where .= " AND p.`brand_id` IN (" . implode( ',', $brands ) . ") ";
        }
        $ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
        if ( $ashley_accounts ) {
            foreach ( $ashley_accounts as &$aa ) {
                $aa = "'$aa'";
            }
            if ( !$brands )
                $inner_join .= " INNER JOIN products p ON p.industry_id = i.industry_id ";
            $inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
            $where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
        }

        return $this->get_results( "SELECT i.* FROM `industries` i $inner_join WHERE 1 $where GROUP BY i.industry_id", PDO::FETCH_ASSOC );
    }

    /**
     * Get Attributes
     *
     * @return array
     */
    public function get_attributes() {
        $inner_join = '';
        $where = '';

        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
        if ( $brands ) {
            $inner_join .= " INNER JOIN products p ON p.category_id = ar.category_id ";
            $where .= " AND p.`brand_id` IN (" . implode( ',', $brands ) . ") ";
        }
        $ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
        if ( $ashley_accounts ) {
            foreach ( $ashley_accounts as &$aa ) {
                $aa = "'$aa'";
            }
            if ( !$brands )
                $inner_join .= " INNER JOIN products p ON p.category_id = ar.category_id ";
            $inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
            $where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
        }

        return $this->get_results( "SELECT a.*, GROUP_CONCAT( DISTINCT ar.`category_id` ) AS categories FROM `attributes` AS a LEFT JOIN `attribute_relations` AS ar ON ( ar.`attribute_id` = a.`attribute_id` ) $inner_join WHERE 1 $where GROUP BY a.`attribute_id`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Attribute Items
     *
     * @return array
     */
    public function get_attribute_items() {
        $inner_join = '';
        $where = '';

//        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
//        if ( $brands ) {
//            $inner_join .= " INNER JOIN products p ON p.category_id = air.product_id ";
//            $where .= " AND p.`brand_id` IN (" . implode( ',', $brands ) . ") ";
//        }
//        $ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
//        if ( $ashley_accounts ) {
//            foreach ( $ashley_accounts as &$aa ) {
//                $aa = "'$aa'";
//            }
//            if ( !$brands )
//                $inner_join .= " INNER JOIN products p ON p.category_id = air.product_id ";
//            $inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
//            $where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
//        }

        return $this->get_results( "SELECT ai.* FROM `attribute_items` ai INNER JOIN attribute_item_relations air ON ai.attribute_item_id = air.attribute_item_id $inner_join WHERE 1 $where GROUP BY ai.attribute_item_id", PDO::FETCH_ASSOC );
    }

    /**
     * Get Product Groups
     *
     * @return array
     */
    public function get_product_groups() {
        $inner_join = '';
        $where = '';

        $brands = $this->api_key ? $this->api_key->get_brand_ids() : array();
        if ( $brands ) {
            $inner_join .= " INNER JOIN product_group_relations pgr ON pg.product_group_id = pgr.product_group_id INNER JOIN products p ON p.product_id = pgr.product_id ";
            $where .= " AND p.`brand_id` IN (" . implode( ',', $brands ) . ") ";
        }
        $ashley_accounts = $this->api_key ? $this->api_key->get_ashley_accounts() : array();
        if ( $ashley_accounts ) {
            foreach ( $ashley_accounts as &$aa ) {
                $aa = "'$aa'";
            }
            if ( !$brands )
                $inner_join .= " INNER JOIN product_group_relations pgr ON pg.product_group_id = pgr.product_group_id INNER JOIN products p ON p.product_id = pgr.product_id ";
            $inner_join .= " INNER JOIN website_products wp ON wp.product_id = p.product_id INNER JOIN website_settings ws ON ws.website_id = wp.website_id AND ws.`key` = 'ashley-ftp-username'";
            $where .= " AND ws.`value` IN (" . implode( ',', $ashley_accounts ) . ") AND p.`user_id_created` IN ( 353, 1477 ) ";
        }

        return $this->get_results( "SELECT pg.* FROM `product_groups` pg $inner_join WHERE 1 $where GROUP BY pg.product_group_id", PDO::FETCH_ASSOC );
    }
}