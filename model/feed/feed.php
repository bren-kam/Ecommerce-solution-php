<?php
class Feed extends ActiveRecordBase {
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
    public function get_products( $start_date, $end_date, $starting_point, $limit ) {
        // Use the variables if necessary
        $where = '';

        if ( !empty( $start_date ) )
            $where .= " AND a.`timestamp` >= '" . $this->quote( $start_date ) . "'";

        if ( !empty( $end_date ) )
            $where .= " AND a.`timestamp` < '" . $this->quote( $end_date ) . "'";

        $starting_point = ( !empty( $starting_point ) ) ? $starting_point : 0;
        $limit = ( !empty( $limit ) ) ? $limit : 10000;

        if ( $limit > 10000000 )
            $limit = 10000000;

        return $this->prepare(
            "SELECT p.`product_id`, p.`category_id` AS categories, p.`brand_id`, p.`industry_id`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`weight`, p.`volume`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, p.`date_created`, p.`timestamp`, i.`name` AS industry, GROUP_CONCAT( DISTINCT pi.`image` ) AS images, GROUP_CONCAT( DISTINCT air.`attribute_item_id` ) AS attributes, GROUP_CONCAT( DISTINCT pgr.`product_group_id` ) AS product_groups FROM `products` AS p LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id` ) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) LEFT JOIN `attribute_item_relations` AS air ON ( air.`product_id` = p.`product_id` ) LEFT JOIN `product_group_relations` AS pgr ON ( pgr.`product_id` = p.`product_id` ) WHERE p.`publish_visibility` <> 'deleted' AND p.`website_id` = 0 $where GROUP BY p.`product_id` ORDER BY p.`product_id` LIMIT :starting_point, :limit"
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
        return $this->get_results( "SELECT * FROM `brands`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Categories
     *
     * @return array
     */
    public function get_categories() {
        return $this->get_results( "SELECT `category_id`, `parent_category_id`, `name`, `slug`, `sequence`, `date_updated` FROM `categories`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Industries
     *
     * @return array
     */
    public function get_industries() {
        return $this->get_results( "SELECT * FROM `industries`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Attributes
     *
     * @return array
     */
    public function get_attributes() {
        return $this->get_results( "SELECT a.*, GROUP_CONCAT( ar.`category_id` ) AS categories FROM `attributes` AS a LEFT JOIN `attribute_relations` AS ar ON ( ar.`attribute_id` = a.`attribute_id` ) GROUP BY a.`attribute_id`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Attribute Items
     *
     * @return array
     */
    public function get_attribute_items() {
        return $this->get_results( "SELECT * FROM `attribute_items`", PDO::FETCH_ASSOC );
    }

    /**
     * Get Product Groups
     *
     * @return array
     */
    public function get_product_groups() {
        return $this->get_results( "SELECT * FROM `product_groups`", PDO::FETCH_ASSOC );
    }
}