<?php
class AccountBrand extends ActiveRecordBase {
    public $website_id, $brand_id, $name, $content, $meta_title, $meta_description, $meta_keywords, $top, $date_updated;

    // Fields from other tables
    public $slug;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_brands' );
    }

    /**
     * Get
     *
     * @param int $account_id
     * @param int $brand_id
     * @return array
     */
    public function get( $account_id, $brand_id ) {

        $this->prepare(
            "SELECT wb.`website_id`, b.`brand_id`, IF( '' = wb.`name` OR wb.`name` IS NULL, b.`name`, wb.`name` ) AS name, b.slug, wb.`content`, wb.`meta_title`, wb.`meta_description`, wb.`meta_keywords`, wb.`top` FROM `brands` AS b LEFT JOIN `website_brands` AS wb ON ( b.`brand_id` = wb.`brand_id` AND wb.`website_id` = :account_id ) WHERE b.`brand_id` = :brand_id"
            , 'ii'
            , array( ':account_id' => $account_id, ':brand_id' => $brand_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get All
     *
     * @param int $account_id
     * @return array
     */
    public function get_all_ids( $account_id ) {
        return $this->prepare(
            'SELECT DISTINCT wb.`brand_id` FROM `website_brands` AS wb WHERE wb.`website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
            , 'name' => strip_tags( $this->name )
            , 'content' => format::strip_only( $this->content, '<script>' )
            , 'meta_title' => strip_tags($this->meta_title)
            , 'meta_description' => strip_tags($this->meta_description)
            , 'meta_keywords' => strip_tags($this->meta_keywords)
            , 'top' => $this->top
        ), 'iissssssi');
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'name' => strip_tags( $this->name )
            , 'content' => format::strip_only( $this->content, '<script>' )
            , 'meta_title' => strip_tags($this->meta_title)
            , 'meta_description' => strip_tags($this->meta_description)
            , 'meta_keywords' => strip_tags($this->meta_keywords)
            , 'top' => $this->top
        ), array(
            'website_id' => $this->website_id
            , 'brand_id' => $this->brand_id
        ), 'ssssssi', 'ii' );
    }

    /**
	 * Get all
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return AccountBrand[]
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT b.`brand_id`, IF( '' = wb.`name` OR wb.`name` IS NULL, b.`name`, wb.`name` ) AS name, wb.`date_updated`, b.`slug` FROM `brands` AS b LEFT JOIN `website_brands` AS wb ON ( b.`brand_id` = wb.`brand_id` ) INNER JOIN `products` AS p ON ( p.`brand_id` = b.`brand_id` ) INNER JOIN `website_products` AS wp ON ( p.`product_id` = wp.`product_id` ) WHERE 1 $where $order_by GROUP BY b.`brand_id` LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'AccountBrand' );
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
        return $this->prepare( "SELECT COUNT( DISTINCT b.`brand_id` )  FROM `brands` AS b LEFT JOIN `website_brands` AS wb ON ( b.`brand_id` = wb.`brand_id` ) INNER JOIN `products` AS p ON ( p.`brand_id` = b.`brand_id` ) INNER JOIN `website_products` AS wp ON ( p.`product_id` = wp.`product_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
