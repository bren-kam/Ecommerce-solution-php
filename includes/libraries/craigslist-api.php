<?php
/**
 * Craigslist - API Class
 *
 * This handles all API Calls
 *
 * @version 1.0.0
 */
class Craigslist_API {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'http://plugcp.primusconcepts.com/greysuit/';
	const DEBUG = true;

    /**
     * A few variables that will determine the basic status
     */
    private $reseller_id = 0;
    private $key = '';
    private $start_date = '';
    private $end_date = '';
    private $request;
    private $raw_response;
    private $response;
    private $post;

	/**
	 * Construct class will initiate and run everything
     *
     * @param int $reseller_id
     * @param string $key
	 */
	public function __construct( $reseller_id, $key ) {
		// Do we need to debug
		if ( self::DEBUG )
			error_reporting( E_ALL );

        $this->reseller_id = $reseller_id;
        $this->key = $key;
	}
	
	/*********************************/
	/* Start: Craigslist API Methods */
	/*********************************/	

    /**
     * Add Customer
     *
     * @param string $name
     * @return int (customer ID)
     */
    public function add_customer( $name ) {
        // Add customer
        $response = $this->_execute( 'addcustomer', compact( 'name' ) );

        return $response->customer_id;
    }

    /**
     * Add Market
     *
     * @param int $customer_id
     * @param string $name
     * @return bool
     */
    public function add_market( $customer_id, $name ) {
        // Add customer
        $response = $this->_execute( 'addmarket', compact( 'customer_id', 'name' ) );

        return $response->customer_id;
    }

    /**
     * Set the date range
     *
     * @param string $start_date
     * @param string $end_date
     */
    public function set_date_range( $start_date, $end_date ) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * Get Last API Request
     *
     * @return bool
     */
    public function get_request() {
        return $this->request;
    }

	/**
	 * Get API Response
	 *
	 * @return bool
	 */
	public function get_raw_response() {
		return $this->raw_response;
	}

    /**
     * Get API Response
     *
     * @return bool
     */
    public function get_response() {
        return $this->response;
    }
	
	/*******************************/
	/* END: Craigslist API Methods */
	/*******************************/

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @access private
	 *
	 * @param string $method The method being called
	 * @param array $params (optional|array) an array of the parameters to be sent
     * @return mixed
	 */
	private function _execute( $method, $params = array() ) {
        // Make sure they have an API key
		if ( empty( $this->key ) )
			return false;

        $post_vars = http_build_query( array( 'data' => json_encode( $params ) ) );

        // Set variables
        $this->request = self::URL_API . $method . '/reseller_id/' . $this->reseller_id . '/apikey/' . $this->key . '/';
        $this->post = $post_vars;

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $this->request );
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->post );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        // Do the call and get the raw response
        $this->raw_response = curl_exec( $ch );

        // It's supposed to be in JSON, so grab that if it exists
		$this->response = json_decode( $this->raw_response );

        // Close the curl object
		curl_close($ch);

        // Debugging info
        if ( self::DEBUG ) {
            echo "<h1>Request</h1>\n" . $this->request . "\n<br />\n<h2>Post Variables</h2>\n" . var_export( $this->post, true ) . "<hr />\n";
            echo "<h1>Raw Response</h1>\n" . $this->raw_response . "\n<hr />\n";
            echo "<h1>Response</h1>\n" . var_export( $this->response, true );
        }

        // Return the JSON response
		return $this->response;
	}
}