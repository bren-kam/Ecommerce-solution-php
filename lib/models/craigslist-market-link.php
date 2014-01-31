<?php
class CraigslistMarketLink extends ActiveRecordBase {
    public $website_id, $craigslist_market_id, $market_id, $cl_category_id;

    // Other tables
    public $cl_market_id;

    // Artificial field
    public $market;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'craigslist_market_links' );
    }

    /**
     * Get Links by account
     *
     * @param int $account_id
     * @return CraigslistMarketLink[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            "SELECT cml.`craigslist_market_id`, CONCAT( cm.`city`, ', ', IF( '' <> cm.`area`, CONCAT( cm.`state`, ' - ', cm.`area` ), cm.`state` ) ) AS market, cml.`market_id`, cml.`cl_category_id`, cm.`cl_market_id` FROM `craigslist_market_links` AS cml LEFT JOIN `craigslist_markets` AS cm ON ( cml.`craigslist_market_id` = cm.`craigslist_market_id` ) WHERE cml.`website_id` = :account_id AND cm.`status` = :status"
            , 'ii'
            , array( ':account_id' => $account_id, ':status' => CraigslistMarket::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'CraigslistMarketLink' );
    }

    /**
     * Get cl_category_ids by account
     *
     * @param int $account_id
     * @param int $cl_market_id
     * @return array
     */
    public function get_cl_category_ids_by_account( $account_id, $cl_market_id ) {
        return $this->prepare(
            'SELECT a.`cl_category_id` FROM `craigslist_market_links` AS a LEFT JOIN `craigslist_markets` AS b ON ( a.`craigslist_market_id` = b.`craigslist_market_id` ) WHERE a.`website_id` = :account_id AND b.`cl_market_id` = :cl_market_id'
            , 'ii'
            , array( ':account_id' => $account_id, ':cl_market_id' => $cl_market_id )
        )->get_col();
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'craigslist_market_id' => $this->craigslist_market_id
            , 'market_id' => $this->market_id
            , 'cl_category_id' => $this->cl_category_id
        ), 'iiii' );
    }
}
