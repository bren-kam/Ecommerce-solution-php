<?php
/**
 * Session - special session functions
 *
 * Functions:
 * bool store_array( array $data ) - stores an array of data into a session
 *
 * @package Studio98 Framework
 * @since 1.0
 */

// Acts like a constructor
if ( empty( $_SESSION ) )
	session_start();

class session extends Base_Class {
	/**
	 * Stores arrays of data in session variables
	 * 
	 * @since 1.0
	 *
	 * @param array $data the array in a key => value pair
	 * @return bool
	 */
	function store_array( $data ) {
		if ( !is_array( $data ) )
			return false;
			
		foreach( $data as $key => $value ) {
			$_SESSION[$key] = $value;
		}
		
		return true;
	}
}