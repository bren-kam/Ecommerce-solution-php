<?php
class AccountPage extends ActiveRecordBase {
    public $id, $website_page_id, $website_id, $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $mobile, $status, $top, $header_script, $date_created, $date_updated, $landing_page;

    // Artificial field
    public $products;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_pages' );

        // We want to make sure they match
        if ( isset( $this->website_page_id ) )
            $this->id = $this->website_page_id;
    }

    /**
     * Get
     *
     * @param int $account_page_id
     * @param int $account_id
     */
    public function get( $account_page_id, $account_id ) {
        $this->prepare(
            'SELECT `website_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `mobile`, `top`, `header_script`, `landing_page` FROM `website_pages` WHERE `website_page_id` = :account_page_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':account_page_id' => $account_page_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_page_id;
    }

    /**
     * Get by slug
     *
     * @param int $account_id
     * @param string $slug
     */
    public function get_by_slug( $account_id, $slug ) {
        $this->prepare(
            'SELECT `website_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `mobile`, `top`, `header_script` FROM `website_pages` WHERE `website_id` = :account_id AND `slug` = :slug'
            , 'is'
            , array( ':account_id' => $account_id, ':slug' => $slug )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_page_id;
    }

    /**
     * Get all
     *
     * @param int $account_id
     * @return array
     */
    public function get_all( $account_id ) {
        return $this->prepare(
            'SELECT `website_page_id`, `slug` FROM `website_pages` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Get all
     *
     * @param int $account_id
     * @return AccountPage[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `website_page_id`, `slug`, `title` FROM `website_pages` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountPage' );
    }

    /**
     * Get Product IDs
     *
     * @return array
     */
    public function get_product_ids() {
        return $this->prepare(
            'SELECT `product_id` FROM `website_page_product` WHERE `website_page_id` = :account_page_id ORDER BY `sequence`'
            , 'i'
            , array( ':account_page_id' => $this->id )
        )->get_col();
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();
        $this->status = 1;

        $this->insert( array(
            'website_id' => $this->website_id
            , 'slug' => strip_tags($this->slug)
            , 'title' => strip_tags($this->title)
            , 'content' => $this->content
            , 'status' => $this->status
            , 'top' => $this->top
            , 'date_created' => $this->date_created
            , 'landing_page' => $this->landing_page
        ), 'isssisi' );

        $this->id = $this->website_page_id = $this->get_insert_id();
    }

    /**
     * Add Products
     *
     * @param array $product_ids
     */
    public function add_products( array $product_ids ) {
        // Type Juggling
        $website_page_id = $this->id;

        // Initialize variables
        $sql_values = '';
        $sequence = 0;

        foreach ( $product_ids as $product_id ) {
            if ( !empty( $sql_values ) )
                $sql_values .= ',';

            // Type Juggling
            $product_id = (int) $product_id;

            $sql_values .= "( $website_page_id, $product_id, $sequence )";

            $sequence++;
        }

        $this->query( "INSERT INTO `website_page_product` ( `website_page_id`, `product_id`, `sequence` ) VALUES $sql_values ON DUPLICATE KEY UPDATE `website_page_id` = VALUES( `website_page_id` )" );
    }

    /**
     * Save
     */
    public function save() {
        parent::update( array(
            'slug' => strip_tags($this->slug)
            , 'title' => strip_tags(htmlentities($this->title))
            , 'content' => $this->content
            , 'meta_title' => strip_tags($this->meta_title)
            , 'meta_description' => strip_tags($this->meta_description)
            , 'meta_keywords' => strip_tags($this->meta_keywords)
            , 'mobile' => $this->mobile
            , 'top' => $this->top
            , 'header_script' => $this->header_script
            , 'landing_page' => $this->landing_page
        ), array( 'website_page_id' => $this->id )
        , 'ssssssiisi', 'i' );
    }

    /**
     * Copy pages
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_by_account( $template_account_id, $account_id ) {
        $this->copy( $this->table, array(
                'website_id' => $account_id
                , 'slug' => NULL
                , 'title' => NULL
                , 'content' => NULL
                , 'meta_title' => NULL
                , 'meta_description' => NULL
                , 'meta_keywords' => NULL
                , 'header_script' => NULL
                , 'mobile' => NULL
                , 'top' => 1
                , 'status' => 1
            ), array( 'website_id' => $template_account_id )
        );
    }

    /**
     * Delete
     */
    public function remove() {
        $this->delete( array( 'website_page_id' => $this->id ), 'i' );
    }

    /**
     * Count products
     * @return int
     */
    public function count_products() {
        return $this->prepare(
            'SELECT COUNT( `product_id` ) FROM `website_page_product` WHERE `website_page_id` = :website_page_id'
            , 'i'
            , array( ':website_page_id' => $this->id )
        )->get_var();
    }
    /**
     * Delete Products
     */
    public function delete_products() {
        $this->prepare(
            'DELETE FROM `website_page_product` WHERE `website_page_id` = :account_page_id'
            , 'i'
            , array( ':account_page_id' => $this->id )
        )->query();
    }

    /**
	 * Get all information of the checklists
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `website_page_id`, `slug`, `title`, `date_created`, `date_updated` FROM `website_pages` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'AccountPage' );
	}

	/**
	 * Count all the checklists
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( `website_page_id` ) FROM `website_pages` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
