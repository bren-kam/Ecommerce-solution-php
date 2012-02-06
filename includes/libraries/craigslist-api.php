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
	const URL_API = 'http://plugcp.primusconcepts.com/greysuit/analytics/';
	const DEBUG = false;

    /**
     * A few variables that will determine the basic status
     */
    private $website_id = 0;
    private $start_date = '';
    private $end_date = '';

	/**
	 * Construct class will initiate and run everything
     *
     * @param int $website_id
	 */
	public function __construct( $website_id ) {
		// Do we need to debug
		if ( self::DEBUG )
			error_reporting( E_ALL );

        $this->website_id = $website_id;
	}
	
	/*********************************/
	/* Start: Craigslist API Methods */
	/*********************************/	

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
	 * List Companies
	 *
	 * Returns an array of companies including their company_ids, names and images
	 *
	 * @return bool
	 */
	public function list_companies() {
		// Execute the command
		$response = $this->execute( 'list_companies' );
		
		// Return the graphs if successful
		return ( $this->success ) ? $response->companies : false;
	}
	
	/*******************************/
	/* END: Statistics API Methods */
	/*******************************/

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @access private
	 *
	 * @param string $method The method being called
	 * @param array $params (optional|array) an array of the parameters to be sent
	 */
	private function execute( $method, $params = array() ) {
		if ( empty( $this->api_key ) ) {
			$this->error = 'Cannot send request without an API Key.';
			$this->success = false;
		}
		
		$post_vars = http_build_query( array_merge( array( 'auth_key' => $this->api_key, 'method' => $method ), $params ) );
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::URL_API );
		//curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_vars );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		
		$this->response = json_decode( curl_exec( $ch ) );
		curl_close($ch);
		
		if ( $this->response->success )
			$this->success = true;
		
		$this->message = $this->response->message;
		
		return $this->response;
	}
}