<?php
/**
 * Handles all the stuff to start FB
 *
 * @package Grey Suit Retail
 * @since 1.0
 */

class FB extends Base_Class {
	/**
	 * Sets up everything necessary to run a facebook app
	 *
	 * @param string $app_id
	 * @param string $secret
	 * @param bool $skip (optional|false)
	 * @param array $parameters (optional|false)
	 */
	public function __construct( $app_id, $secret ) {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		library('facebook/facebook');
		
		// Create our Application instance (replace this with your appId and secret).
		$this->facebook = new Facebook(array(
			'appId' => $app_id,
			'secret' => $secret,
			'cookie' => true,
		));
	}
	
	// Magic Method -- make it paralel the actual facebook class
	function __call( $method, $arguments ) {
		return call_user_func_array( array( $this->facebook, $method ), $arguments );
	}
}