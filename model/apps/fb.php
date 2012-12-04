<?php
/**
 * Handles all the stuff to start FB
 *
 * @package Grey Suit Retail
 * @since 1.0
 */

class Fb {
    /**
     * Hold the User ID
     * @var int
     */
    public $user_id;

    /**
	 * Sets up everything necessary to run a facebook app
	 *
	 * @param string $app_id
	 * @param string $secret
	 * @param string $uri
	 * @param bool $skip [optional]
	 * @param array $parameters [optional]
	 */
	public function __construct( $app_id, $secret, $uri, $skip = false, $parameters = array() ) {
		library('facebook/facebook');
		
		// Create our Application instance (replace this with your appId and secret).
		$this->facebook = new Facebook( array(
			'appId' => $app_id,
			'secret' => $secret,
			'cookie' => true,
		) );
		
		// Get User ID
		$this->user_id = $this->facebook->getUser();
		
		// We may or may not have this data based on whether the user is logged in.
		//
		// If we have a $user id here, it means we know the user is logged into
		// Facebook, but we don't know if the access token is valid. An access
		// token is invalid if the user logged out of Facebook.
		if ( $this->user_id ) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
                $user_profile = $this->facebook->api('/me');
			} catch ( FacebookApiException $e) {
				error_log( $e );
				$this->user_id = null;
			}
		}
		
		// Login or logout url will be needed depending on current user state.
		if ( !$this->user_id && !$skip ) {
            // If we need to get permission
            $permissions = ( !empty( $parameters ) ) ? '&' . http_build_query( $parameters ) : '';
            $redirect_uri = urlencode( "http://apps.facebook.com/$uri/" );

            // Need to get permission
			die( '<script type="text/javascript">top.location.href="http://www.facebook.com/dialog/oauth?client_id=' . $app_id . '&redirect_uri=' .  $redirect_uri . $permissions . '";</script>' );
		}
	}
	
	/**
     * Magic Method -- make it parallel the actual facebook class
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
	public function __call( $method, $arguments ) {
		return call_user_func_array( array( $this->facebook, $method ), $arguments );
	}
}