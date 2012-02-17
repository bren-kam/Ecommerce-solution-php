<?php
/**
 * Imagine Retailer - Feed API Class
 *
 * This handles all API Calls
 * @version 1.0.0
 */
class GSR_API_Feed {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'http://feed.development.imagineretailer.com/requests/';
	const DEBUG = true;
	
	/**
	 * A few variables that will determine the basic status
	 */
	public $message = NULL;
	public $success = false;
	public $raw_response = NULL;
	public $response = NULL;

	/**
	 * Construct class will initiate and run everything
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {
		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );
		
		$this->api_key = $api_key;
	}
	
	/******************************/
	/* Start: IR Feed API Methods */
	/******************************/
	
	/**
	 * Get Feed
	 *
	 * @return array
	 */
	public function get_feed() {
		// Execute the command
		$response = $this->_execute( 'get_feed' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

    /**
	 * Get Products
	 *
	 * @return array
	 */
	public function get_products() {
		// Execute the command
		$response = $this->_execute( 'get_products' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

    /**
	 * Get Categories
	 *
	 * @return array
	 */
	public function get_categories() {
		// Execute the command
		$response = $this->_execute( 'get_categories' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

	/**
	 * Get Brands
	 *
	 * @return array
	 */
	public function get_brands() {
		// Execute the command
		$response = $this->_execute( 'get_brands' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

    /**
	 * Get Industries
	 *
	 * @return array
	 */
	public function get_industries() {
		// Execute the command
		$response = $this->_execute( 'get_industries' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

    /**
	 * Get Attributes
	 *
	 * @return array
	 */
	public function get_attributes() {
		// Execute the command
		$response = $this->_execute( 'get_attributes' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

    /**
	 * Get Product Groups
	 *
	 * @return array
	 */
	public function get_product_groups() {
		// Execute the command
		$response = $this->_execute( 'get_product_groups' );

		// Return the user id successful
		return ( $this->success ) ? $response : false;
	}

	/****************************/
	/* END: IR Feed API Methods */
	/****************************/
	
	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @access private
	 *
	 * @param string $method The method being called
	 * @param array $params an array of the parameters to be sent
	 */
	private function _execute( $method, $params = array() ) {
		if( empty( $this->api_key ) ) {
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

        $this->raw_response = curl_exec( $ch );
        $this->response = json_decode( $this->raw_response );
        //echo $this->raw_response;
        if ( !is_object( $this->response ) ) {
            //echo $this->raw_response . 'here';
        } else {
            //print_r( $this->raw_response );
        }

        //echo count( $this->response->products );
        //echo $this->raw_response;
        //$this->response = @simplexml_load_string( $this->raw_response );
        curl_close($ch);

		if( $this->response->success )
			$this->success = true;

		$this->message = $this->response->message;
		
		return $this->response;
	}
}