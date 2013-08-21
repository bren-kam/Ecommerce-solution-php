<?php
class ApiExtLog extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $api, $method, $url, $request, $raw_request, $response, $raw_response, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'api_ext_log' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->id = $this->insert( array(
            'api' => $this->api
            , 'method' => $this->method
            , 'url' => $this->url
            , 'request' => $this->request
            , 'raw_request' => $this->raw_request
            , 'response' => $this->response
            , 'raw_response' => $this->raw_response
            , 'date_created' => $this->date_created
        ), 'ssssssss' );
    }

    /**
     * Purges all records further than 30 days ago
     */
    public function purge() {
        $this->query( "DELETE FROM `api_ext_log` WHERE `date_created` < DATE_SUB( NOW(), INTERVAL 1 MONTH )" );
    }
}