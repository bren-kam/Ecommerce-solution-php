<?php
class ApiLog extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $api_log_id, $company_id, $type, $method, $message, $success, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'api_log' );

        // We want to make sure they match
        if ( isset( $this->api_log_id ) )
            $this->id = $this->api_log_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'company_id' => $this->company_id
            , 'type' => strip_tags($this->type)
            , 'method' => strip_tags($this->method)
            , 'message' => strip_tags($this->message)
            , 'success' => $this->success
            , 'date_created' => $this->date_created
        ), 'isssis' );

        $this->api_log_id = $this->id = $this->get_insert_id();
    }
}