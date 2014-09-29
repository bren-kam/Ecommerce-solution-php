<?php
/**
 * Password Manager - API Wrapper
 *
 * This handles all API Calls
 * @version 1.0.0
 */
class PM_API {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'https://www.studio98.com/pmapi/';
	const DEBUG = false;
	
	/**
	 * A few variables that will determine the basic status
	 */
    private $api_key;
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
	 */
	public function __construct( $api_key ) {
		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );
		
		$this->api_key = $api_key;
	}
	
	/***************************************/
	/* Start: Password Manager API Methods */
	/***************************************/

    /**
	 * Create Group
	 *
     * @param string $title
     * @param int $parent_group_id [optional]
     * @param string $icon (optional)
	 * @return int
	 */
	public function create_group( $title, $parent_group_id = 0, $icon = 'group.png' ) {
		// Execute the command
		$response = $this->_execute( 'create_group', compact( 'title', 'parent_group_id', 'icon' ) );
		
		// Return the new group id successful
		return ( $this->success ) ? $response->group_id : false;
	}

    /**
	 * Get Groups
	 *
	 * @return array
	 */
	public function get_groups() {
		// Execute the command
		$response = $this->_execute( 'get_groups' );

		// Return the groups if successful
		return ( $this->success ) ? $response->groups : false;
	}

    /**
	 * Get Groups
	 *
     * @param int $group_id
	 * @return array
	 */
	public function get_group( $group_id ) {
		// Execute the command
		$response = $this->_execute( 'get_group', compact( 'group_id' ) );

		// Return the group if success
		return ( $this->success ) ? $response->group : false;
	}

    /**
	 * Create Password
	 *
     * @param int $group_id
     * @param string $title
     * @param string $username
     * @param string $password
     * @param string $url
     * @param string $notes
     * @param string $icon [optional]
	 * @return int
	 */
	public function create_password( $group_id, $title, $username, $password, $url = '', $notes = '', $icon = 'ftp.png' ) {
		// Execute the command
		$response = $this->_execute( 'create_password', compact( 'group_id', 'title', 'username', 'password', 'url', 'notes', 'icon' ) );

		// Return the new group id successful
		return ( $this->success ) ? $response->password_id : false;
	}

	/*************************************/
	/* END: Password Manager API Methods */
	/*************************************/

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
	private function _execute( $method, $params = array() ) {
		if( empty( $this->api_key ) ) {
			$this->error = 'Cannot send request without an API Key.';
			$this->success = false;
            return false;
		}
		
		$this->request = array_merge( array( 'auth_key' => $this->api_key, 'method' => $method ), $params );
        $this->raw_request = http_build_query( $this->request );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::URL_API );
		//curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->raw_request );
		curl_setopt( $ch, CURLOPT_POST, 1 );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        curl_close($ch);

        // Seet the response
		if( $this->response->success ) {
			$this->success = true;
        } else {
            $this->error = $this->message;
        }

		$this->message = $this->response->message;

        // If we're debugging lets give as much info as possible
        if( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", self::URL_API, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

		return $this->response;
	}
}