<?php
class CraigslistMarket extends ActiveRecordBase {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $id, $craigslist_market_id, $cl_market_id, $parent_market_id, $state, $city, $area, $submarket, $status, $date_created;

    // For artificial field
    public $market;

    // Fields from tother tables
    public $market_id, $cl_category_id;

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
     * @return CraigslistMarket[]
     */
    public function get_all() {
        return $this->prepare(
            "SELECT `craigslist_market_id`, `cl_market_id`, `parent_market_id`, CONCAT( `city`, ', ', IF( '' <> `area`, CONCAT( `state`, ' - ', `area` ), `state` ) ) AS market, `submarket` FROM `craigslist_markets` WHERE `status` = :status ORDER BY market ASC"
            , 'i'
            , array( ':status' => self::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'CraigslistMarket' );
    }

    /**
     * Get By Ad
     *
     * @param int $craigslist_ad_id
     * @param int $account_id
     * @return CraigslistMarket[]
     */
    public function get_by_ad( $craigslist_ad_id, $account_id ) {
        return $this->prepare(
            "SELECT cm.`craigslist_market_id`, CONCAT( cm.`city`, ', ', IF( '' <> cm.`area`, CONCAT( cm.`state`, ' - ', cm.`area` ), cm.`state` ) ) AS market, cm.`cl_market_id`, cml.`market_id`, cml.`cl_category_id` FROM `craigslist_markets` AS cm LEFT JOIN `craigslist_ad_markets` AS cam ON ( cam.`craigslist_market_id` = cm.`craigslist_market_id` ) LEFT JOIN `craigslist_market_links` AS cml ON ( cml.`craigslist_market_id` = cam.`craigslist_market_id` ) WHERE cam.`craigslist_ad_id` = :craigslist_ad_id AND cml.`website_id` = :account_id"
            , 'ii'
            , array( ':craigslist_ad_id' => $craigslist_ad_id, ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'CraigslistMarket' );
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @return CraigslistMarket[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            "SELECT cm.`craigslist_market_id`, CONCAT( cm.`city`, ', ', IF( '' <> cm.`area`, CONCAT( cm.`state`, ' - ', cm.`area` ), cm.`state` ) ) AS market, cm.`cl_market_id`, cml.`market_id`, cml.`cl_category_id` FROM `craigslist_markets` AS cm LEFT JOIN `craigslist_market_links` AS cml ON ( cml.`craigslist_market_id` = cm.`craigslist_market_id` ) WHERE cml.`website_id` = :account_id"
            , 'ii'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'CraigslistMarket' );
    }
}
