<?php
/**
 * Trumpia - API Library
 */
class TrumpiaV1 {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'http://api.greysuitmobile.com/rest/v1/';
	const DEBUG = true;
	
	/**
	 * A few variables that will determine the basic status
	 */
    private $api_key;
	private $user_name;
	private $message = NULL;
	private $success = false;
	private $raw_request = NULL;
	private $request = NULL;
	private $raw_response = NULL;
	private $response = NULL;
    private $error = NULL;

	/**
	 * Construct class will initiate and run everything
	 *
	 * @param string $api_key
	 * @param string $user_name
	 */
	public function __construct( $api_key, $user_name ) {
		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );
		
		$this->api_key = $api_key;
		$this->user_name = $user_name;
	}
	
	/******************************/
	/* Start: Trumpia API Methods */
	/******************************/
	
    /**
	 * Get Subscriptions
	 *
	 * @param int $user_name
	 * @return int
	 */
    public function get_subscriptions() {
		// Execute the command
		$response = $this->_execute( 'subscription', array(), array( 'row_size' => 100 ) );
		
		// Return the subscriptions
		return ( $this->success() ) ? $response : false;
	}

	/****************************/
	/* END: Trumpia API Methods */
	/****************************/

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
     * Format Boolean values
     *
     * @param array $arguments
     * @return void
     */
    private function _format_bools( $arguments ) {
        foreach ( $arguments as &$a ) {
            $a = ( true === $a ) ? 'TRUE' : 'FALSE';
        }
    }

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @param string $method The method being called
	 * @param array $params [optional] an array of the parameters to be sent
	 * @param array $get_params [optional] an array of get_parameters to be sent
     * @return stdClass object
	 */
	private function _execute( $method, $params = array(), $get_params = array() ) {
		if( empty( $this->api_key ) ) {
			$this->error = 'Cannot send request without an API Key.';
			$this->success = false;
            return false;
		}

        // Set the API Key
		$header = array(
			"X-Apikey: " . $this->api_key
			, "Content-Type: " . header::$content_types['json']
		);


		$this->request = $params;
        $this->raw_request = http_build_query( $this->request );
		
		$url = self::URL_API . $this->user_name . "/{$method}";
		
		if ( !empty( $get_params ) )
			$url .= '?' . http_build_query( $get_params );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->raw_request );
		curl_setopt( $ch, CURLOPT_POST, 1 );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        curl_close($ch);

        // Set the response
        $this->success = true;
        $this->error = NULL;
		
        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>$url</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

		return $this->response;
	}
}