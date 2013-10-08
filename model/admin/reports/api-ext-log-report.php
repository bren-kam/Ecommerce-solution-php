<?php
class ApiExtLogReport extends CustomReport {
    /**
     * @var User
     */
    protected $user;

    /**
     * Setup
     *
     * @param User $user
     */
    public function __construct( User $user ) {
        parent::__construct();

        $this->user = $user;
    }

    /**
     * Get the All Accounts Report
     *
     * @return array
     */
    public function report() {
        $report = $this->get_results( "SELECT w.`title`, ael.`api`, ael.`method`, ael.`url`, ael.`request`, ael.`raw_request`, ael.`response`, ael.`raw_response`, ael.`date_created` FROM `api_ext_log` AS ael LEFT JOIN `websites` AS w ON ( w.`website_id` = ael.`website_id` ) WHERE ael.`method` <> 'Google Maps API' AND ael.`date_created` > DATE_SUB( NOW(), INTERVAL 30 DAY ) ORDER BY ael.`id` DESC", PDO::FETCH_ASSOC );

        array_unshift( $report, array( 'Website Title', 'API', 'Method', 'URL', 'Request', 'Raw Request', 'Response', 'Raw Response', 'Date/Time' ) );

        return $report;
    }
}
