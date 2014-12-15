<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextBio extends ActiveRecordBase {

    public $id, $website_yext_bio_id, $website_id, $website_yext_location_id, $name;

    // From other tables
    public $location;

    public function __construct() {
        parent::__construct( 'website_yext_bio' );
    }

    /**
     * Get
     * @param $id
     * @param $website_id
     */
    public function get( $id, $website_id ) {
        $this->prepare(
            "SELECT * FROM website_yext_bio WHERE website_yext_bio_id = :id AND website_id = :website_id"
            , 'i'
            , [  ':id' => $id, ':website_id' => $website_id ]
        )->get_row( PDO::FETCH_INTO, $this );
        $this->id = $this->website_yext_bio_id;
    }

    /**
     * List All
     * @param $variables
     * @return WebsiteYextBio[]
     */
    public function list_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT b.*, l.name as location FROM website_yext_bio b LEFT JOIN website_yext_location l ON b.website_yext_location_id = l.website_yext_location_id WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextBio' );
    }

    /**
     * Count All
     * @param $variables
     * @return int
     */
    public function count_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT COUNT(*) FROM website_yext_bio b WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /**
     * Insert Bulk
     * @param $bios
     * @param $location_id
     * @param $account_id
     * @throws ModelException
     */
    public function insert_bulk( $bios, $location_id, $account_id ) {
        if ( empty( $bios ) )
            return;

        $rows = [];
        foreach ( $bios as $bio ) {
            $rows[] = "( '{$bio->id}', $account_id, '$location_id', '$bio->name' )";
        }

        $this->query( "INSERT INTO website_yext_bio( website_yext_bio_id, website_id, `website_yext_location_id`, `name` ) VALUES" . implode( ',', $rows ) );
    }

    /**
     * Remove By Account ID
     * @param $account_id
     * @throws ModelException
     */
    public function remove_by_account_id( $account_id ) {
        $this->query( "DELETE FROM website_yext_bio WHERE website_id = {$account_id}" );
    }

}
