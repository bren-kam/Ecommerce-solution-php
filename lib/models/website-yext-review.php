<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextReview extends ActiveRecordBase {

    public $location_id, $site_id, $title, $content, $url, $date_created, $rating, $author_name;

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
            "SELECT * FROM website_yext_review WHERE 1 $where $order_by LIMIT $limit"
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
            "SELECT COUNT(*) FROM website_yext_review WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /**
     * Insert Bulk
     * @param $reviews
     * @param $account_id
     * @throws ModelException
     */
    public function insert_bulk( $reviews, $account_id ) {

        if ( empty( $reviews ) )
            return;

        $rows = [];
        foreach ( $reviews as $review ) {
            $rows[] = "( $account_id, '{$review->locationId}', " . $this->quote($review->siteId) . ", " . $this->quote($review->rating) . ", " . $this->quote($review->title) . ", " . $this->quote($review->content) . ", " . $this->quote($review->authorName) . ", " . $this->quote($review->url) . ", " . $this->quote($review->reviewDate) . " )";
        }

        $this->query( "INSERT INTO website_yext_review( website_id, location_id, site_id, rating, title, content, author_name, url, date_created ) VALUES" . implode( ',', $rows ) );
    }

    /**
     * Remove By Account ID
     * @param $account_id
     * @throws ModelException
     */
    public function remove_by_account_id( $account_id ) {
        $this->query( "DELETE FROM website_yext_review WHERE website_id = {$account_id}" );
    }

}
