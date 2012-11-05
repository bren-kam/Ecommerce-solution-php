<?php
/**
 * Nonce class - creates and validates nonces
 *
 * Functions:
 * string create( [ string $action [, int $user_id = 0 ]] ) - creates a nonce
 * bool verify( string $nonce, [ string $action [, int $user_id = 0 ]] ) - verifies a nonce
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class nonce extends security {
	/**
	 * Create a nonce tick (number to indicate epoch)
	 *
	 * @return int
	 */
	private static function _tick() {
		return ceil( time() / ( NONCE_DURATION / 2 ) );
	}
	
	/**
	 * Creates a nonce
	 *
	 * @since 1.0.0
	 * @uses security::hash
	 *
	 * @param string $action [optional] the action that the nonce is for
	 * @param int $user_id [optional] the user id
	 * @return string
	 */
	public static function create( $action = '', $user_id = 0 ) {
		$i = self::_tick();
		return substr( parent::hash( $i . $action . $user_id, 'nonce', NONCE_KEY ), -12, 10 );
	}
	
	/**
	 * Verifies a nonce
	 *
	 * @since 1.0.0
	 * @uses nonce::create
	 *
	 * @param string $nonce the nonce to check it with
	 * @param string $action (Optional) the action that the nonce is for
	 * @param int $user_id (Optional) the user id
	 * @return string
	 */
	public static function verify( $nonce, $action = '' , $user_id = 0 ) {
		$i = self::_tick();
		
		// Nonce generated 0-6 hours ago
		if ( $nonce == substr( parent::hash( $i . $action . $user_id, 'nonce', NONCE_KEY ), -12, 10 ) )
			return 1;
		
		// Nonce generated 6-12 hours ago
		if ( $nonce == substr( parent::hash( $i - 1 . $action . $user_id, 'nonce', NONCE_KEY ), -12, 10 ) )
			return 2;
		
		// Invalid nonce
		return false;
	}
	
	/**
	 * Creates a nonce field
	 *
	 * @since 1.0.0
	 * @uses nonce::create
	 *
	 * @param string $action Optional. Action name.
	 * @param string $name Optional. Nonce name.
	 * @param bool $echo Optional, default true. Whether to display or return hidden form field.
	 * @return string Nonce field.
	 */
	public static function field( $action = '', $name = "_nonce", $echo = true ) {
		$nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . self::create( $action ) . '" />';
		
		if ( $echo )
			echo $nonce_field;

		return $nonce_field;
	}
	
	/**
	 * Retrieve URL with nonce added to URL query.
	 *
	 * @since 1.0.0
	 * @uses nonce::create
	 * @uses url::add_query_arg
	 *
	 * @param string $action_url URL to add nonce action
	 * @param string $action Optional. Nonce action name
     * @param int $uid [optional]
	 * @return string URL with nonce action added.
	 */
	public static function url( $action_url, $action = -1, $uid = 0 ) {
		$action_url = str_replace( '&amp;', '&', $action_url );
		
		return url::add_query_arg( '_nonce', self::create( $action, $uid ), $action_url );
	}

}