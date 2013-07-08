<?php
/**
 * Active Campaign - Reseller - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url https://www.activecampaign.com/partner/api/
 *
 */

class ActiveCampaignResellerAPI {
    /**
     * Constant paths to include files
     */
    const API_URL = 'https://www.activecampaign.com/api.php';
    const API_OUTPUT = 'json';
    const DEBUG = false;

    /**
   	 * Hold the api_key
   	 *
   	 * @var string
   	 */
   	protected  $_api_key;

    /**
     * A few variables that will determine the basic status
     */
    protected $message = NULL;
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
     * @param string $api_key
	 */
	public function __construct( $api_key ) {
		$this->_api_key = $api_key;
	}

	/**********************************/
	/* Start: AC Reseller API Methods */
	/**********************************/

	/**
	 * Add Subdomain
	 *
	 * @param string $rootdomain
	 * @param string $domain
	 * @param string $dir
     *
     * @return object
	 */
	public function add_subdomain( $rootdomain, $domain, $dir = NULL ) {
		if ( is_null( $dir ) )
			$dir = 'public_html/' . $domain;
		
		$response = $this->_call( 'SubDomain', 'addsubdomain', compact( 'dir', 'rootdomain', 'domain' ) );
		
		return ( $this->success() ) ? $response : false;
	}

	/********************************/
	/* END: AC Reseller API Methods */
	/********************************/

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->message;
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
     * This sends sends the actual call to the API Server and parses the response
     *
     * @param string $method The method being called
     * @param array $params an array of the parameters to be sent
     * @return stdClass object
     */
    protected function _execute( $method, $params = array() ) {
        // Set Request Parameters
        $this->request = array_merge( array(
            'api_key' => $this->_api_key
            , 'api_action' => $method
            , 'api_output' => self::API_OUTPUT
        ), $params);
        $this->raw_request = http_build_query( $this->request );

        // Set URL
        $url = self::API_URL;

        if ( count( $this->request ) > 0 )
            $url .= '?' . $this->raw_request;

        // Initialize cURL and set options
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_URL, $url );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        // Close cURL
        curl_close($ch);

        // Set the response
        $this->success = 1 == $this->response->succeeded;
        $this->message = $this->response->message;

        $this->error = ( $this->success ) ? NULL : true;

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", $url, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

        return $this->response;
    }
}