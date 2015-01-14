<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextReview extends ActiveRecordBase {

    public $id, $website_yext_review_id, $location_id, $site_id, $title, $content, $url, $date_created, $rating, $author_name;

    // From other tables
    public $location_name, $location_address;

    public function __construct() {
        parent::__construct( 'website_yext_review' );
    }

    /**
     * List All
     * @param $variables
     * @return WebsiteYextReview[]
     */
    public function list_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT r.* FROM website_yext_review r INNER JOIN website_yext_location l ON l.website_yext_location_id = r.location_id WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextReview' );
    }

    /**
     * Count All
     * @param $variables
     * @return int
     */
    public function count_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT COUNT(*) FROM website_yext_review r INNER JOIN website_yext_location l ON l.website_yext_location_id = r.location_id WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /*
     * Create
     * @throws ModelException
     */
    public function create() {
        $this->insert(
            [
                'website_yext_review_id' => $this->id
                , 'location_id' => $this->location_id
                , 'site_id' => $this->site_id
                , 'rating' => $this->rating
                , 'title' => $this->title
                , 'content' => $this->content
                , 'author_name' => $this->author_name
                , 'url' => $this->url
                , 'date_created' => $this->date_created
            ]
            , 'iisisssss'
        );

        $this->website_yext_review_id = $this->id;
    }

    /**
     * Get
     * @param  int $review_id
     * @param  int $account_id
     */
    public function get( $review_id, $account_id ) {
        $this->prepare(
            "SELECT r.*, l.name as location_name, l.address as location_address FROM website_yext_review r INNER JOIN website_yext_location l ON r.location_id = l.website_yext_location_id WHERE r.website_yext_review_id = :review_id AND l.website_id = :account_id"
            , 'ii'
            , [ ':review_id' => $review_id, ':account_id' => $account_id ]
        )->get_row( PDO::FETCH_INTO, $this );
        $this->id = $this->website_yext_review_id;
    }


    /**
     * Get Sites
     * @param int $account_id
    */
    public function get_sites( $account_id ) {
        return $this->get_col(
            "SELECT DISTINCT r.site_id FROM website_yext_review r INNER JOIN website_yext_location l ON r.location_id = l.website_yext_location_id WHERE l.website_id = $account_id"
        );
    }

}
