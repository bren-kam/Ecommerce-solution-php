<?php

class Jira {

    public static $url = 'https://greysuitretail.atlassian.net/';
    public static $user = 'gabrielbrunacci';
    public static $password = 'longlive';

    public $last_response_code;

    public function __construct() {
    }

    /**
     * Create Issue
     * @param  array $data
     * @return stdClass
     */
    public function create_issue( $data ) {
        return $this->_request( 'post', 'rest/api/2/issue', $data );
    }

    /**
     * Create Comment
     * @param  int $issue_id
     * @param  array $data
     * @return stdClass
     */
    public function create_comment( $issue_id, $data ) {
        return $this->_request( 'post', "rest/api/2/issue/{$issue_id}/comment", $data );
    }

    /**
     * Request
     *
     * @param $method
     * @param $service
     * @param $data
     * @return stdObject
     */
    private function _request( $method, $service, $data ) {

        $parameters = [];
        if ( $method == 'get') {
            $parameters = array_merge( $parameters, $data );
            $request = [];
        } else {
            $request = $data;
        }
        $url = self::$url . $service . '?' . http_build_query( $parameters );
        $json_request = json_encode($request);

        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_USERPWD, self::$user.":".self::$password);
        if ( $method == 'get' ) {
            // do nothing
        } else if ( $method == 'post' ) {
            curl_setopt( $curl, CURLOPT_POST, true );
            if ( $json_request ) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json_request);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json;charset=UTF-8']);
            }
        } else {
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, strtoupper( $method ) );
            if ( $json_request ) {
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_request );
                curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json;charset=UTF-8' ] );
            }
        }
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );

        $json_response = curl_exec( $curl );
        $response = json_decode( $json_response );

        $this->last_response_code  = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        $api_log = new ApiExtLog();
        $api_log->website_id = 0;
        $api_log->api = 'JIRA';
        $api_log->method = $method . ' ' . $service;
        $api_log->url = $url;
        $api_log->request = json_encode( $request );
        $api_log->raw_request = $json_request;
        $api_log->response = json_encode( $response );
        $api_log->raw_response = $this->last_response_code . '|' . $json_response;
        $api_log->create();

        return $response;
    }
}