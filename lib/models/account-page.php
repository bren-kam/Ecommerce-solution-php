<?php
class AccountPage extends ActiveRecordBase {
    public $id, $website_page_id, $website_id, $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $mobile, $status, $date_created, $date_updated;

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
     */
    public function get( $account_page_id ) {
        $this->prepare(
            'SELECT `website_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `mobile` FROM `website_pages` WHERE `website_page_id` = :account_page_id'
            , 'i'
            , array( ':account_page_id' => $account_page_id )
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
            'SELECT `website_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `mobile` FROM `website_pages` WHERE `website_id` = :account_id AND `slug` = :slug'
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
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'slug' => $this->slug
            , 'title' => $this->title
            , 'content' => $this->content
            , 'date_created' => $this->date_created
        ), 'issss' );

        $this->id = $this->website_page_id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        parent::update( array(
            'slug' => $this->slug
            , 'title' => $this->title
            , 'content' => $this->content
            , 'meta_title' => $this->meta_title
            , 'meta_description' => $this->meta_description
            , 'meta_keywords' => $this->meta_keywords
            , 'mobile' => $this->mobile
        ), array( 'website_page_id' => $this->id )
        , 'ssssssi', 'i' );
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
                , 'mobile' => NULL
                , 'status' => 1
            ), array( 'website_id' => $template_account_id )
        );
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
            "SELECT `website_page_id`, `slug`, `title`, `status`, `date_updated` FROM `website_pages` WHERE 1 $where $order_by LIMIT $limit"
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

    /**
     * Delete
     */
    public function remove() {
        $this->delete( array( 'website_page_id' => $this->id ), 'i' );
    }
}
