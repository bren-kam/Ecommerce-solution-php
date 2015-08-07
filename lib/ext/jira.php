<?php

class Jira {

    public static $url = 'https://greysuitretail.atlassian.net/';
    public $user = 'gabrielbrunacci';
    public $password = 'longlive';
    
    public $last_response_code;

    public function __construct( $user = null, $password = null ){
        if ($user){ 
            $this->user = $user;
            $this->password = $password;
        } 

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
     * Update Issue
     * @param  int $issue_id
     * @param  array $data
     * @return stdClass
     */
    public function update_issue( $issue_id, $data ) {
        return $this->_request( 'put', "rest/api/2/issue/{$issue_id}", $data );
    }

    /**
     * Update Issue Status
     * @param  int $issue_id
     * @param  int $status_id
     * @return stdClass
     */
    public function update_issue_status( $issue_id, $status_id ) {
        return $this->_request( 'post', "rest/api/2/issue/{$issue_id}/transitions?expand=transitions.fields", [
            "transition" => [
                "id" => $status_id
            ]
        ]);
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
     * Get Issue
     * @param  int $issue_id
     * @return stdClass
     */
    public function get_issue( $issue_id ) {
        return $this->_request( 'get', "rest/api/2/issue/{$issue_id}" );
    }

    /**
     * Get Comments By Issue
     * @param  int $issue_id
     * @return stdClass
     */
    public function get_comments_by_issue( $issue_id ) {
        return $this->_request( 'get', "rest/api/2/issue/{$issue_id}/comment" );
    }

    /**
     * Request
     *
     * @param $method
     * @param $service
     * @param $data
     * @return stdObject
     */
    private function _request( $method, $service, $data = [] ) {

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
        curl_setopt( $curl, CURLOPT_USERPWD, $this->user.":".$this->password);
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