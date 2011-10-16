<?php
/**
 * Handles AJAX response
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class AJAX {
	/**
	 * JSON Response
	 * @var array
	 */
	private $json_response = array();
	
	/**
	 * Construct initializes data
	 *
	 * @param string $nonce
	 * @param string $key
	 * @param bool $override (optional|false)
	 */
	public function __construct( $nonce, $key, $override = false ) {
		// Make sure it's a valid request
		if( !$override && !nonce::verify( $nonce, $key ) ) {
			$this->add_response( 'error', _('A verification error occurred. Please refresh the page and try again.') );
			$this->respond( false );
		}
	}
	
	/**
	 * Add Response
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function add_response( $key, $value ) {
		// Set the variable
		$this->json_response[$key] = $value;
	}
	
	/**
	 * Ok
	 * 
	 * Asserts that the value is OK, if not, exits out
	 */
	public function ok( $assertion, $error ) {
		if( $assertion )
			return;
		
		$this->add_response( 'error', $error );
		$this->respond( false );
	}
	
	/**
	 * Respond
	 *
	 * @param int $success (optional|true)
	 */
	public function respond( $success = true ) {
		$this->add_response( 'success', $success );
		
		// If it is successful, don't send data we don't need to
		if( $success )
			unset( $this->json_response['error'] );
		
		// Set the header
		header::type('json');

		// Spit out the code and exit;
		echo json_encode( $this->json_response );
		exit;
	}
}