<?php
/**
 * Google GoogleMaps API
 *
 */

class GoogleMapsAPI {
    /**
     * Constant paths to include files
     */
    const DEBUG = false;
    const API_OUTPUT = 'json';
    const URL = 'http://maps.googleapis.com/maps/api/';

    /**
     * @var Account
     */
    protected $account;

    /**
     * A few variables that will determine the basic status
     */
    protected $response_message = NULL;
    protected $success = false;
    protected $raw_request = NULL;
    protected $request = NULL;
    protected $raw_response = NULL;
    protected $response = NULL;
    protected $error = NULL;
    protected $params = array();

    /**
     * Construct class will initiate and run everything
     *
     * @param Account $account This is for logging
     */
    public function __construct( Account $account ) {
        $this->account = $account;
    }

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->response_message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success() {
        return $this->success;
    }

    /**
     * Get private raw_request variable
     *
     * @return string
     */
    public function raw_request() {
        return $this->raw_request();
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request() {
        return $this->request;
    }

    /**
     * Get private raw_response variable
     *
     * @return string
     */
    public function raw_response() {
        return $this->raw_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response() {
        return $this->response;
    }

    /**
     * Get private error variable
     *
     * @return string
     */
    public function error() {
        return $this->error;
    }

    /**
     * Geocode an address
     *
     * @param string $address
     * @return object( lat, lng )
     */
    public function geocode( $address ) {
        $response = $this->execute( 'geocode', array(
            'address' => $address
            , 'sensor' => 'false'
        ) );

        return (object) array(
            'lat' => $response->results[0]->geometry->location->lat
            , 'lng' => $response->results[0]->geometry->location->lng
        );
    }

    /**
     * This sends sends the actual call to the API Server and parses the response
     *
     * @param string $method The method being called
     * @param array $params an array of the parameters to be sent
     * @return stdClass object
     */
    public function execute( $method, $params = array() ) {
        // Set Request Parameters
        $this->request = $params;

        $this->raw_request = http_build_query( $this->request );

        // Set URL
        $url = self::URL . $method . '/' . self::API_OUTPUT . '?' . $this->raw_request;

        // Initialize cURL and set options
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

        curl_setopt( $ch, CURLOPT_URL, $url );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        // Close cURL
        curl_close($ch);

        // Set the response
        $this->success = 'OK' == $this->response->status;
        $this->response_message = $this->response->status;

        $this->error = ( $this->success ) ? NULL : true;

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", $url, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

        $api_log = new ApiExtLog();
        $api_log->website_id = $this->account->id;
        $api_log->api = 'Google Maps API';
        $api_log->method = $method;
        $api_log->url = $url;
        $api_log->request = json_encode( $this->request );
        $api_log->raw_request = $this->raw_request;
        $api_log->response = json_encode( $this->response );
        $api_log->raw_response = $this->raw_response;
        $api_log->create();

        return $this->response;
    }
}