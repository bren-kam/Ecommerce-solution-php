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

if( !is_object( $s98_cache ) ) {
	global $s98_cache;
	$s98_cache = new Base_Cache();
}

class nonce extends security {
	/**
	 * Creates a nonce
	 *
	 * @since 1.0.0
	 * @uses security:hash
	 *
	 * @param string $action (Optional) the action that the nonce is for
	 * @param int $user_id (Optional) the user id
	 * @returns string
	 */
	static public function create( $action = '' , $user_id = 0 ) {
		$i = ceil( time() / ( NONCE_DURATION / 2 ) );
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
	 * @returns string
	 */
	public function verify( $nonce, $action = '' , $user_id = 0 ) {
		global $s98_cache;
		
		$verified = $s98_cache->get( $action, 'nonce' );
		
		if( !$verified ) {
			$verified = ( $nonce == self::create( $action, $user_id ) );
			$s98_cache->add( $action, $verified, 'nonce' );
		}
		
		return $verified;
	}
	
	/**
	 * Creates a nonce field
	 *
	 * @since 1.0.0
	 * @uses nonce::create
	 *
	 * @param string $action Optional. Action name.
	 * @param string $name Optional. Nonce name.
	 * @param bool $referer Optional, default true. Whether to set the referer field for validation.
	 * @param bool $echo Optional, default true. Whether to display or return hidden form field.
	 * @return string Nonce field.
	 */
	function field( $action = '', $name = "_nonce", $referer = true , $echo = true ) {
		$nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . self::create( $action ) . '" />';
		if ( $echo )
			echo $nonce_field;
	
		if ( $referer ) {
			$ref = $_SERVER['REQUEST_URI'];
			echo '<input type="hidden" name="_http_referer" value="', $ref, '" />';
		}
		
		return $nonce_field;
	}

}