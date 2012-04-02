<?php
/**
 * The base class that is extended by all other classes
 *
 * @package Grey Suit Retail
 * @since 1.0
 */

class Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() { 
		$this->db = new SQL( 'imaginer_admin', 'rbDxn6kkj2e4', 'imaginer_system', 'localhost' );
		$this->b = fn::browser();

		return true;
	}
	
	/**
	 * Adds an error to the error table
	 *
	 * Grab as much information as possible
	 *
	 * @param string $message the error message
 	 * @param int $line (optional) the line number of the file
	 * @param string $file (optional) the file name
	 * @param string $dir (optional) the directory of the file
	 * @param string $function (optional) the function
	 * @param string $class (optional) the class name
	 * @param string $method (optional) the class name
	 */
	public function error( $message, $line = 0, $file = '', $dir = '', $function = '', $class = '', $method = '', $debug = true ) { 
		if ( !empty( $_SERVER['QUERY_STRING'] ) )
			$query_string = '?' . $_SERVER['QUERY_STRING'];
		
		global $user;
		
		$user_id = ( isset( $user['user_id'] ) )? $user['user_id'] : $_SESSION['user_id'];
		
		if ( !$user_id )
			$user_id = 0;
		
		$website_id = ( isset( $user['website']['website_id'] ) )? $user['website']['website_id'] : $_SESSION['website']['website_id'];
		
		if ( !$website_id )
			$website_id = 0;
		
		$section = ( ADMIN ) ? 'Admin' : 'Account';
		
		$input_data = array( 
			'user_id' => $user_id,
			'website_id' => $website_id,
			'source' => "IR: {$section} Section",
			'subject' => "IR: {$section} Section Error",
			'message' => $message,
			'sql' => $this->db->last_query,
			'sql_error' => $this->db->error(),
			'page' => 'http://' . DOMAIN . $_SERVER['REQUEST_URI'] . '?' . $query_string,
			'referer' => $_SERVER['HTTP_REFERER'],
			'line' => $line,
			'file' => $file,
			'dir' => $dir,
			'function' => $function,
			'class' => $class,
			'method' => $method,
			'browser_name' => $this->b['name'],
			'browser_version' => $this->b['version'],
			'browser_platform' => $this->b['platform'],
			'browser_user_agent' => $this->b['user_agent'],
			'date_created' => dt::date('now')
		);
		
		// If it fails to insert, send an email with the information
		if ( !$this->db->insert( 'errors', $input_data, 'iisssssssissssssssss' ) )
			fn::mail( DEBUG_EMAIL, 'IR: Error while inserting error', "Message:\n$message\n\n" . implode( "\n", $input_data ));
		
		// Send the email off to the system admin
		fn::mail( DEBUG_EMAIL, "IR: {$section} Section Error", $message );
		
		// Show the error on the screen
		if ( DEBUG && $debug )
			$this->display_error( $message . fn::info( $input_data, false ) );
	}
	
	/**
	 * Displays an error message
	 *
	 * @param string $message the error message to displays
	 */
	public function display_error( $message ) {
		echo "<h2 style='color:red'>::DEBUG:: An error has occurred:</h2><p>$message</p><br /><p>To disable these messages, edit s98lib/config.php file.</p><br />";
	}
}