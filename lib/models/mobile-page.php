<?php
class MobilePage extends ActiveRecordBase {
    public $id, $mobile_page_id, $website_id, $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $status, $updated_user_id, $date_created, $date_updated;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'mobile_pages' );

        // We want to make sure they match
        if ( isset( $this->mobile_page_id ) )
            $this->id = $this->mobile_page_id;
    }

    /**
     * Get
     *
     * @param int $mobile_page_id
     * @param int $account_id
     */
    public function get( $mobile_page_id, $account_id ) {
        $this->prepare(
            'SELECT `mobile_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `status` FROM `mobile_pages` WHERE `mobile_page_id` = :mobile_page_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':mobile_page_id' => $mobile_page_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->mobile_page_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'slug' => strip_tags($this->slug)
            , 'title' => strip_tags($this->title)
            , 'content' => $this->content
            , 'date_created' => $this->date_created
        ), 'issss' );

        $this->id = $this->mobile_page_id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'slug' => strip_tags($this->slug)
            , 'title' => strip_tags($this->title)
            , 'content' => $this->content
        ), array(
            'mobile_page_id' => $this->id
        ), 'sss', 'i' );
    }

    /**
	 * List Pages
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return MobilePage[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `mobile_page_id`, `slug`, `title`, `status`, `date_updated` FROM `mobile_pages` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'MobilePage' );
	}

    /**
     * Delete
     */
    public function remove() {
        $this->delete( array( 'mobile_page_id' => $this->id ), 'i' );
    }

    /**
	 * Count all the pages
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( `mobile_page_id` )  FROM `mobile_pages` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
