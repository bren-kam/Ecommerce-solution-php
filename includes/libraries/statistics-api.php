<?php
/**
 * Statistics - API Class
 *
 * This handles all API Calls
 *
 * @version 1.0.0
 */
class Stat_API {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'http://api.realstatistics.com/requests/';
	const DEBUG = false;
	
	/**
	 * A few variables that will determine the basic status
	 */
	public $message = NULL;
	public $success = false;
	public $response = NULL;
	
	/**
	 * Construct class will initiate and run everything
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {
		// Do we need to debug
		if ( self::DEBUG )
			error_reporting( E_ALL );
		
		$this->api_key = $api_key;
	}
	
	/*********************************/
	/* Start: Statistics API Methods */
	/*********************************/	
	
	/**
	 * Add Graph Value
	 *
	 * This adds a single point to a particular graph.
	 *
	 * NOTE: You can find what graph_id you are looking for by using 'list_graphs' below
	 *
	 * @param int $graph_id
	 * @param float $value
	 * @param string $date
	 * @param bool $week_ending_hour (optional|false)
	 * @return bool
	 */
	public function add_graph_value( $graph_id, $value, $date, $week_ending_hour = false ) {
		// Set it to a string with 0 and 1
		$week_ending_hour = ( $week_ending_hour ) ? '1' : '0';
		
		// Execute the command
		$this->execute( 'add_graph_value', compact( 'graph_id', 'value', 'date', 'week_ending_hour' ) );
		
		return ( $this->success ) ? true : false;
	}
	
	/**
	 * List Graphs
	 *
	 * Returns an array of graphs including their graph_ids, names, sparklines 
	 * and whether they are an advanced graph or not
	 *
	 * @param int $company_id
	 * @return bool
	 */
	public function list_graphs( $company_id ) {
		// Execute the command
		$response = $this->execute( 'list_graphs', compact( 'company_id' ) );
		
		// Return the graphs if successful
		return ( $this->success ) ? $response->graphs : false;
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