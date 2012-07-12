<?php
/**
 * Handles all the Errors
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Error_Handler extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		set_error_handler( array( $this, 'handle' ), E_ALL | E_STRICT );
	}
	
	/**
	 * Handle an error
	 *
	 * @param int $number
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param array $context
	 * @return bool
	 */
	public function handle( $number, $message, $file, $line, $context ) {
		// Don't want errors from offsets
		if ( '/issues/issue/' == $_SERVER['REDIRECT_URL'] )
			return;

		switch ( $number ) {
			case E_ERROR:
				$priority = 3;
			break;

			case E_WARNING:
				$priority = 2;
			break;

			case E_PARSE:
				$priority = 3;
			break;

			case E_NOTICE:
				$priority = 1;
			break;

			case E_CORE_ERROR:
				$priority = 3;
			break;

			case E_CORE_WARNING:
				$priority = 2;
			break;

			case E_COMPILE_ERROR:
				$priority = 3;
			break;

			case E_COMPILE_WARNING:
				$priority = 2;
			break;

			case E_USER_ERROR:
				$priority = 3;
			break;

			case E_USER_WARNING:
				$priority = 2;
			break;

			case E_USER_NOTICE:
				$priority = 1;
			break;

			case E_STRICT:
				$priority = 1;
			break;

			case E_RECOVERABLE_ERROR:
				$priority = 3;
			break;

			case E_DEPRECATED:
				$priority = 2;
			break;

			case E_USER_DEPRECATED:
				$priority = 2;
			break;

			default:
				$priority = 2;
			break;
		}

		// Create the issue key
		$issue_key = md5( $message . $file . $line );

		// Create the issue
		if ( !$this->issue( $issue_key, $priority, $number, $message, $file, $line, $context, debug_backtrace() ) )
			return false;

		// Create the error
		if ( !$this->error( $issue_key ) )
			exit;


		return true;
	}
	
	/**
	 * Make sure an issue exists
	 *
	 * @param string $issue_key
	 * @param int $priority
	 * @param int $number
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @param array $context
	 * @param array $backtrace
	 * @return bool
	 */
    public function issue( $issue_key, $priority, $number, $message, $file, $line, $context, $backtrace ) {
		// @Fix
		$context = ''; // This can be huge and may not be needed
		
		// We dont' want the first item on the array because it is always the error handler
		array_shift( $backtrace );
		
		// Make sure the issue exists
		$this->db->prepare( 'INSERT INTO `issues` ( `issue_key`, `priority`, `number`, `message`, `file`, `line`, `context`, `backtrace`, `date_created` ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, NOW() ) ON DUPLICATE KEY UPDATE `occurrences` = `occurrences` + 1, `status` = 0', 'siississ', $issue_key, $priority, $number, $message, $file, $line, serialize( $context ), base64_encode( serialize( $backtrace ) ) )->query('');
		
		// Handle errors
		if ( $this->db->errno() ) {
			fn::mail( 'kerry@studio98.com', 'Failed to insert issue', "Issue Key: $issue_key\nPriority: $priority\nNumber: $number\nMessage: $message\nFile: $file\nLine: $line\nContext: " . serialize( $context ) . "\nError:\n" . $this->db->error() );
			return false;
		}
		
		return true;
    }
	
	/**
	 * Creates an error
	 *
	 * @param string $issue_key
	 * @return bool
	 */
	public function error( $issue_key ) {
		global $user;
		
		$user_id = ( isset( $user['user_id'] ) ) ? $user['user_id'] : 0;
		$website_id = ( isset( $user['website'] ) ) ? $user['website']['website_id'] : 0;
		$referer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : '';
		//$subdomain = ( isset( SUBDOMAIN ) && !empty( SUBDOMAIN ) ) ? SUBDOMAIN . '.' : '';
		$subdomain = '';
		 // Now create the error
		$this->db->insert( 'issue_errors', array(
			'issue_key' => $issue_key
			, 'user_id' => $user_id
			, 'website_id' => $website_id
			, 'sql' => ( $this->db->errno() ) ? $this->db->last_query : ''
			, 'sql_error' => $this->db->error()
			, 'page' => 'http://' . $subdomain . DOMAIN . '/' . $_SERVER['REQUEST_URI']
			, 'referer' => $referer
			, 'browser_name' => $this->b['name']
			, 'browser_version' => $this->b['version']
			, 'browser_platform' => $this->b['platform']
			, 'browser_user_agent' => $this->b['user_agent']
			, 'date_created' => dt::date('Y-m-d H:i:s')
		), 'siisssssssss' );
		
		// Handle errors
		if ( $this->db->errno() ) {
			fn::mail( 'kerry@studio98.com', 'Failed to insert error', "Issue Key: $issue_key\nError:\n" . $this->db->error() );
			return false;
		}
		
		return true;
	 }
}