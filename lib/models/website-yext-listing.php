<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextListing extends ActiveRecordBase {

    public $location_id, $site_id, $status, $url, $screenshot_url;

    public function __construct() {
        parent::__construct( 'website_yext_listing' );
    }

    /**
     * List All
     * @param $variables
     * @return WebsiteYextListing[]
     */
    public function list_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT * FROM website_yext_listing WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextListing' );
    }

    /**
     * Count All
     * @param $variables
     * @return int
     */
    public function count_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT COUNT(*) FROM website_yext_listing WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /**
     * Insert Bulk
     * @param $listings
     * @param $account_id
     * @throws ModelException
     */
    public function insert_bulk( $listings, $account_id ) {

        if ( empty( $listings ) )
            return;

        $rows = [];
        foreach ( $listings as $listing ) {
            $rows[] = "( $account_id, '{$listing->locationId}', '{$listing->siteId}', '{$listing->status}', '{$listing->url}', '{{$listing->screenshotUrl}' )";
        }

        $this->query( "INSERT INTO website_yext_listing( website_id, location_id, site_id, `status`, url, screenshot_url ) VALUES" . implode( ',', $rows ) );
    }

    /**
     * Remove By Account ID
     * @param $account_id
     * @throws ModelException
     */
    public function remove_by_account_id( $account_id ) {
        $this->query( "DELETE FROM website_yext_listing WHERE website_id = {$account_id}" );
    }

}
