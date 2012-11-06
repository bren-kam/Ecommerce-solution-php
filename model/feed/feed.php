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
            "SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`weight`, a.`volume`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, a.`date_created`, a.`timestamp`, b.`name` AS industry, GROUP_CONCAT( DISTINCT c.`category_id` ) AS categories, GROUP_CONCAT( DISTINCT d.`image` ) AS images, GROUP_CONCAT( DISTINCT e.`attribute_item_id` ) AS attributes, GROUP_CONCAT( DISTINCT f.`product_group_id` ) AS product_groups FROM `products` AS a LEFT JOIN `industries` AS b ON ( a.`industry_id` = b.`industry_id` ) LEFT JOIN `product_categories` AS c ON ( a.`product_id` = c.`product_id` ) LEFT JOIN `product_images` AS d ON ( a.`product_id` = d.`product_id` ) LEFT JOIN `attribute_item_relations` AS e ON ( a.`product_id` = e.`product_id` ) LEFT JOIN `product_group_relations` AS f ON ( a.`product_id` = f.`product_id` ) WHERE a.`publish_visibility` <> 'deleted' $where GROUP BY a.`product_id` ORDER BY a.`product_id` LIMIT :starting_point, :limit"
            , 'ii'
            , array(
                ':starting_point' => $starting_point
                , ':limit' => $limit
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