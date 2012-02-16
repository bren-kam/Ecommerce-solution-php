<?php
/**
 * Imagine Retailer - Feeds Class
 *
 * This handles all API Requests
 * @version 1.0.0
 */
class Feed_API extends Base_Class {
	/**
	 * Constant paths to include files
	 */
	const DEBUG = false;

	/**
	 * Set of messages used throughout the script for easy access
	 * @var array $messages
	 */
	private $messages = array(
		'error' => 'An unknown error has occured. This has been reported to the Database Administrator. Please try again later.',
		'failed-to-get-feed' => 'Failed to get feed. Please report this to a system administrator.',
		'no-authentication-key' => 'Authentication failed. No Authorization Key was sent.',
		'ssl-required' => 'You must make the call to the secured version of our website.',
		'success-add-order-item' => 'Add Order Item succeeded!',
	);
	
	/**
	 * Set of valid methods
	 * @var array $messages
	 */
	private $methods = array(
		'get_feed'
	);
	
	/**
	 * Pieces of data accrued throughout processing
	 */
	private $company_id = 0;
	private $method = '';
	private $error_message = '';
	private $response = array();
	
	/**
	 * Statuses of different stages of processing
	 */
	private $statuses = array( 
		'init' => false,
		'auth' => false,
		'method_called' => false
	);
	private $logged = false;
	private $error = false;
	
	/**
	 * Construct class will initiate and run everything
	 *
	 * This class simply needs to be initiated for it run to the data on $_POST variables
	 */
	public function __construct() {
		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );
		
		// Load everything that needs to be loaded
		$this->statuses['init'] = true;
		
		// Authenticate & load company id
		$this->_authenticate();
		
		// Parse method
		$this->_parse();
	}
	
	/**
	 * This authenticates the request and loads the company data
	 *
	 * @access private
	 */
	private function _authenticate() {
		// They didn't send an authorization key
		if( !isset( $_POST['auth_key'] ) ) {
			$this->_add_response( array( 'success' => false, 'message' => 'no-authentication-key' ) );
			
			$this->error = true;
			$this->error_message = 'There was no authentication key';
			exit;
		}
			
		$this->company_id = $this->db->get_var( $this->db->prepare( "SELECT a.`company_id` FROM `api_keys` AS a LEFT JOIN `api_settings` AS b ON ( a.`api_key_id` = b.`api_key_id` ) WHERE a.`status` = 1 AND a.`key` = %s AND b.`key` = 'feed' AND b.`value` = 1", $_POST['auth_key'] ) );
		
		// If there was a MySQL error
		if( mysql_errno() ) {
			$this->_err( 'Failed to process authentication', 'Could not retrieve company id', __LINE__, __METHOD__ );
			$this->_add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			exit;
		}
		
		// If failed to grab any company id
		if( !$this->company_id ) {
			$this->_add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			
			$this->error = true;
			$this->error_message = 'There was no company to match API key';
			exit;
		}
		
		$this->statuses['auth'] = true;
	}
	
	/**
	 * This parses the request and calls the correct functions
	 *
	 * @access private
	 */
	private function _parse() {
		if( in_array( $_POST['method'], $this->methods ) ) {
			$this->method = $_POST['method'];
			$this->statuses['method_called'] = true;
			
			$class_name = 'IRR';
			call_user_func( array( 'IRR', $_POST['method'] ) );
		} else {
			$this->_add_response( array( 'success' => false, 'message' => 'The method, "' . $_POST['method'] . '", is not a valid method.' ) );
			
			$this->error = true;
			$this->error_message = 'The method, "' . $_POST['method'] . '", is not a valid method.';
			exit;
		}
	}
	
	/******************************/
	/* START: IR Feed API Methods */
	/******************************/

	/**
	 * Get Feed
	 *
	 * @return bool
	 */
	private function get_feed() {
		$products = $this->db->get_results( "SELECT `product_id`, `brand_id`, `industry_id`, `slug`, `description`, `status`, `sku`, `weight`, `volume`, `product_specifications`, `publish_visibility`, `publish_date`, `date_created`, `timestamp` FROM `products` WHERE `publish_visibility` <> 'deleted' ", ARRAY_A );

		// If there was a MySQL error
		if( mysql_errno() ) {
			$this->_err( 'Failed to Get Feed', "Failed to get feed", __LINE__, __METHOD__ );
			$this->_add_response( array( 'success' => false, 'message' => 'failed-to-get-feed' ) );
			exit;
		}

		$this->_add_response( array( 'success' => true, 'message' => 'success-update-user-arb-subscription' ) );
		$this->_log( 'method', 'The method "' . $this->method . '" has been successfully called. User ID: ' . $user_id, true );
	}
	
	/******************************/
	/* START: IR Feed API Methods */
	/******************************/

	/**
	 * Add a response to be sent
	 *
	 * Adds data to the response that will be sent back to the client
	 *
	 * @param string|array $key this can contain the key OR an array of key => value pairs
	 * @param string $value (optional) $value of the $key. Only optional if $key is an array
	 */
	private function _add_response( $key, $value = '' ) {
		if( empty( $value ) && !is_array( $key ) ) {
			$this->_add_response( array( 'success' => false, 'message' => 'error' ) );
			
			$this->_err( 'Tried to add a response without a valid key and value', "Key: \n----------\n" . fn::info( $key, false ) . "\n----------\n" . $value, __LINE__, __METHOD__ );
		}
		
		// Set the response
		if( is_array( $key ) ) {
			foreach( $key as $k => $v ) {
				// Makes sure there isn't a premade message
				$this->response[$k] = ( is_string( $v ) || is_int( $v ) && array_key_exists( $v, $this->messages ) ) ? $this->messages[$v] : $v;
			}
		} else {
			// Makes sure there isn't a premade message
			$this->response[$key] = ( !is_array( $v ) && array_key_exists( $v, $this->messages ) ) ? $this->messages[$v] : $v;
		}
	}
	
	/**
	 * Gets parameters from the post variable and returns and associative array with those values
	 *
	 * @param mixed $args the args that contain the parameters to get
	 * @return array $parameters
	 */
	private function _get_parameters() {
		$args = func_get_args();
		
		// Make sure the arguments are correct
		if( !is_array( $args ) ) {
			$this->_add_response( array( 'success' => false, 'message' => 'error' ) );
			$this->_err( 'Call to get_parameters with incorrect arguments', "Arguments:\n" . fn::info( $args ), __LINE__, __METHOD__ );
			exit;
		}
		
		// Go through each argument
		foreach( $args as $a ) {
			// Make sure the argument is set
			if( !isset( $_POST[$a] ) ) {
				$message = 'Required parameter "' . $a . '" was not set for the method "' . $this->method . '".';
				$this->_add_response( array( 'success' => false, 'message' => $message ) );
				
				$this->error = true;
				$this->error_message = $message;
				exit;
			}
			
			$parameters[$a] = $_POST[$a];
		}
		
		// Return arguments
		return $parameters;
	}
	
	/**
	 * Adds an log entry to the API log table
	 *
	 * @param string $type the type of log entry
	 * @param string $message message to be put into the log
 	 * @param bool $success whether the call was successful
	 * @param bool $set_logged (optional) whether to set the logged variable as true
	 */
	private function _log( $type, $message, $success, $set_logged = true ) { 
		// Set before hand so that a loop isn't caught in the destructor
		if( $set_logged )
			$this->logged = true;
		
		// If it fails to insert, send an email with the information
		if( !$this->db->insert( 'api_log', array( 'company_id' => $this->company_id, 'type' => $type, 'method' => $this->method, 'message' => $message, 'success' => $success, 'date_created' => date_time::date('Y-m-d H:i:s') ), array( '%d', '%s', '%s', '%s', '%d', '%s' ) ) ) {
			$this->_err( 'Failed to add entry to log', "Type: $type\nMessage:\n$message", __LINE__, __METHOD__ );
			
			// Let the client know that something broke
			$this->_add_response( array( 'success' => false, 'message' => 'error' ) );
		}
	}
	
	/**
	 * Adds an error to the error table
	 *
	 * Grab as much information as possible
	 *
	 * @param string $subject the problem that occurred
	 * @param string $message the error message and details
 	 * @param int $line (optional) the line number of the file
	 * @param string $method (optional) the class name
	 */
	private function _err( $subject, $message, $line = 0, $method = '' ) {
		$query_string = ( isset( $_SERVER['QUERY_STRING'] ) && !empty( $_SERVER['QUERY_STRING'] ) ) ? '?' . $_SERVER['QUERY_STRING'] : '';
		
		$last_query = $this->db->last_query();
		$last_error = MySQL_error();
		$page = 'http://wwww.imagineretailer.com' . $_SERVER['REQUEST_URI'] . '?' . $query_string;
		$referer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : '';
		$message = $subject . "\n\n" . $message;

		list( $source, $subject, $message, $last_query, $last_error, $page, $referer, $file, $dir, $function, $class, $method, $b['name'], $b['version'], $b['platform'], $b['user_agent'] ) = format::sql_safe_deep( array( 'API', $subject, $message, $last_query, $last_error, $page, $referer, __FILE__, dirname(__FILE__), '', __CLASS__, $method, '', '', '', '' ) );
		
		// If it fails to insert, send an email with the information
		if( !$this->db->query( sprintf( "INSERT INTO `errors` ( `user_id`, `website_id`, `source`, `subject`, `message`, `sql`, `sql_error`, `page`, `referer`, `line`, `file`, `dir`, `function`, `class`, `method`, `browser_name`, `browser_version`, `browser_platform`, `browser_user_agent`, `date_created` ) VALUES( %d, %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', NOW() )", $_SESSION['user']['user_id'], $_SESSION['website']['website_id'], $source, $subject, $message, $last_query, $last_error, $page, $referer, $line, $file, $dir, $function, $class, $method, $b['name'], $b['version'], $b['platform'], $b['user_agent'] ) ) )
			mail( 'serveradmin@imagineretailer.com', 'IR API: ' . $subject, "Source: $source\nMessage:\n$message" );
		
		// Send the email off to the system admin
		mail( 'serveradmin@imagineretailer.com', 'IR API: ' . $subject, $message );
		
		$this->error = true;
		$this->error_message = $subject;
	}
	
	/**
	 * Destructor which creates the log and any information that we should know about it
	 */
	public function __destruct() {
		// Make sure we haven't already logged something
		if( !$this->logged )
		if( $this->error ) {
			foreach( $this->statuses as $status => $value ) {
				// Set the message status name
				$message_status = ucwords( str_replace( '_', ' ', $status ) );
				
				$message .= ( $this->statuses[$status] ) ? "$message_status: True" : "$message_status: False";
				$message .= "\n";
			}
			
			$this->_log( 'error', 'Error: ' . $this->error_message . "\n\n" . rtrim( $message, "\n" ), false );
		} else {
			$this->_log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
		}
		
		// Respond in JSON
		echo json_encode( $this->response );
	}
}