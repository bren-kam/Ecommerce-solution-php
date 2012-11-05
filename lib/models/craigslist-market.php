<?php
class CraigslistMarket extends ActiveRecordBase {
    public $id, $craigslist_market_id, $cl_market_id, $parent_market_id, $state, $city, $area, $submarket, $status, $date_created;

    // For artificial field
    public $market;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'craigslist_markets' );

        // We want to make sure they match
        if ( isset( $this->craigslist_market_id ) )
            $this->id = $this->craigslist_market_id;
    }

    /**
     * Get
     *
     * @param int $craigslist_market_id
     */
    public function get( $craigslist_market_id ) {
        $this->prepare(
            'SELECT `craigslist_market_id`, `cl_market_id`, `parent_market_id`, `state`, `city`, `area`, `submarket` FROM `craigslist_markets` WHERE `craigslist_market_id` = :craigslist_market_id'
            , 'i'
            , array( ':craigslist_market_id' => $craigslist_market_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->craigslist_market_id;
    }

    /**
     * Get All
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( "SELECT `craigslist_market_id`, `cl_market_id`, `parent_market_id`, CONCAT( `city`, ', ', IF( '' <> `area`, CONCAT( `state`, ' - ', `area` ), `state` ) ) AS market, `submarket` FROM `craigslist_markets` WHERE `status` = 1 ORDER BY market ASC", PDO::FETCH_CLASS, 'CraigslistMarket' );
    }
}
