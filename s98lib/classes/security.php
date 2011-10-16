<?php
/**
 * Security class - handles everything related to security
 *
 * @Functions:
 * none ssl( bool $enabled = true ) - redirects to a secure or not secure section
 * string encrypt( string $string, string $hash_key ) - encrypts data
 * string decrypt( string $string, string $hash_key ) - decrypts data
 * string salt( string $method [, string $secret_key = '' ] ) - returns a salt for encryption
 * string hash( string $data, string $string [, string $secret_key = '' ] ) - returns a hash of data
 * string generate_password( [ int $length = 12 ] )
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class security extends Base_Class {
	/**
	 * Redirects to a secure page or not secure page
	 *
	 * @since 1.0
	 *
	 * @param bool $enabled whether to redirect to enabled SSL or disabled
	 * @return bool
	 */
	public function ssl( $enabled = true ) {
		if ( $enabled ) {
			if ( !self::is_ssl() )
				url::redirect( 'https://'  . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 401 );
		} else {
			if ( self::is_ssl() )
				url::redirect( 'http://'  . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 401 );
		}
	}
	
	/**
	 * Determines whether SSL is enabled
	 *
	 * @since 1.0
	 *
	 * @param bool $enabled whether to redirect to enabled SSL or disabled
	 * @return bool
	 */
	public function is_ssl() {
		return ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) || ( !isset( $_SERVER['HTTPS'] ) && 443 == $_SERVER['SERVER_PORT'] ) ) ? true : false;
	}
	
	/**
	 * Encrypts a string (256 bit)
	 *
	 * @uses config defined keys
	 * @since 1.0.0
	 *
	 * @param string the string to be encrypted
	 * @param hash_key a second hash key
	 * @return string
	 */
	public function encrypt( $string, $hash_key ) {
		
		if ( !defined( 'ENCRYPTION_KEY' ) )
			return false;
		
		$td = mcrypt_module_open( 'rijndael-256', '', 'ofb', '' );
		$ks = mcrypt_enc_get_key_size( $td );
		$iv = substr( md5( ENCRYPTION_KEY ), 0, $ks );
		$key = substr( md5( $hash_key ), 0, $ks );
		
		mcrypt_generic_init( $td, $key, $iv );
		
		$encrypted = mcrypt_generic( $td, str_replace( "&", '\046', $string ) );
		
		mcrypt_generic_deinit( $td );
		mcrypt_module_close( $td );
		
		return $encrypted;
	}
	
	/**
	 * Decrypts a string (256 bit)
	 *
	 * @since 1.0.0
	 *
	 * @param string the string to be decrypted
	 * @param hash_key a second hash key
	 * @param string $encryption_key (optional) will override encryption key within
	 * @return string
	 */
	public function decrypt( $string, $hash_key, $encryption_key = '' ) {
		$ek = ( empty( $encryption_key ) && defined( 'ENCRYPTION_KEY' ) ) ? ENCRYPTION_KEY : $encryption_key;
		
		if ( empty( $ek ) )
			return false;
		
		$td = mcrypt_module_open( 'rijndael-256', '', 'ofb', '' );
		$ks = mcrypt_enc_get_key_size( $td );
		$iv = substr( md5( $ek ), 0, $ks );
		$key = substr( md5( $hash_key ), 0, $ks );
	
		mcrypt_generic_init( $td, $key, $iv );
	
		$decrypted = str_replace( '\046', '&', @mdecrypt_generic( $td, $string ) );
	
		mcrypt_generic_deinit( $td );
		mcrypt_module_close( $td );
	
		return trim( $decrypted );
	}
	
	/**
	 * Creates a salt
	 *
	 * @since 1.0
	 *
	 * @param string $method the method it's being used for (i.e., 'nonce')
	 * @param string $secret_key (Optional) the secret key
	 * @return string salt
	 */
	static public function salt( $method, $secret_key = '' ) {
		if ( empty( $secret_key ) )
			$secret_key = SECRET_KEY;
		
		return $secret_key . hash_hmac( 'md5', $method, $secret_key );
	}
	
	/**
	 * Get hash of given string.
	 *
	 * @since 1.0
	 * @uses security::salt()
	 *
	 * @param string $data Plain text to hash
	 * @param string $method the method it's being used for (i.e., 'nonce')
	 * @param string $secret_key the secret key
	 * @return string Hash of $data
	 */
	static public function hash( $data, $method, $secret_key = '' ) {
		$salt = self::salt( $method, $secret_key );
		
		return hash_hmac( 'md5', $data, $salt );
	}
	
	/**
	 * Creates a random password
	 *
	 * @since 1.0
	 * @uses security::salt() salt
	 *
	 * @param int $length the length of the password
	 * @return string password
	 */
	public function generate_password( $length = 12 ) {
		$possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~!@#$%^&*()_-+=|}]{[":;,.?/';
		
		for( $i = 1; $i <= $length; $i++ ):
			$char = $possible[mt_rand( 0, strlen( $possible ) - 1 )];
			$string .= $char;
		endfor;
		
		return $string;
	}
	
	/**
	 * Encrypts an email
	 *
	 * @since 1.0
	 * @uses format::string_to_entity
	 *
	 * @param string $email
	 * @param string $title (optional) the title of the anchor
	 * @param bool $anchor (optional) whether to return as an anchor or just the email address
	 * @return string 
	 */
	public function encrypt_email( $email, $title = '', $anchor = true ) {
		if ( !empty( $title ) )
			$title = " title='$title'";
		
		list( $user, $domain ) = explode( '@', $email );
		
		preg_match( '/^([\w-\.]*)(\.[A-Za-z]{2,4})$/', $domain, $matches );
		
		$tld = format::string_to_entity( $matches[2] );
		
		return ( $anchor ) ? "<a href='&#109;&#097;&#105;&#108;&#116;&#111;&#058;$user&#64;" . $matches[1] . "$tld'{$title}>{$user}&#64;" . $matches[1] . "$tld</a>" : $user . '&#64;' . $matches[1] . $tld;
	}
}