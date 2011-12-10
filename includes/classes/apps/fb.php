<?php
/**
 * Handles all the stuff to start FB
 *
 * @package Imagine Retailer
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
	public function __construct( $app_id, $secret, $skip = false, $parameters = false ) {
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
		
		// Get User ID
		$this->user = $this->facebook->getUser();
		
		// We may or may not have this data based on whether the user is logged in.
		//
		// If we have a $user id here, it means we know the user is logged into
		// Facebook, but we don't know if the access token is valid. An access
		// token is invalid if the user logged out of Facebook.
		if ($this->user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $this->facebook->api('/me');
			} catch (FacebookApiException $e) {
				error_log($e);
				$this->user = null;
			}
		}
		
		// Login or logout url will be needed depending on current user state.
		if (!$this->user && !$skip) {
			if ( $parameters ) {
				// If we need to get permission
				$permissions = '&' . http_build_query( $parameters );
				$redirect_uri = urlencode( "http://apps.facebook.com/op-posting/" );
			} else {
				$permissions = '';
				$redirect_uri = urlencode( "http://www.facebook.com/apps/application.php?id=$app_id" );
			}
			
			echo '<script type="text/javascript">top.location.href="http://www.facebook.com/dialog/oauth?client_id=' . $app_id . '&redirect_uri=' .  $redirect_uri . $permissions . '";</script>';
			exit;
		}
	}
	
	// Magic Method -- make it paralel the actual facebook class
	function __call( $method, $arguments ) {
		return call_user_func_array( array( $this->facebook, $method ), $arguments );
	}
}