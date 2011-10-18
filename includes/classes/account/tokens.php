<?php
/**
 * Handles all the tokens
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Tokens extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Create Token
	 *
	 * @param int $user_id
	 * @param string $token_type
	 * @param int $expires (optional|72) How many hours from now before it expires
	 * @return bool
	 */
    public function create( $user_id, $token_type, $expires = 72 ) {
		// Create the link ID
        $token = md5( time() . mt_rand( 0, 10000 ) );
		
		// Create a token
		$this->db->insert( 'tokens', array( 'user_id' => $user_id, 'key' => $token, 'token_type' => $token_type, 'date_valid' => date( 'Y-m-d H:i:s', time() + $expires * 3600 ) ), 'isss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create token.', __LINE__, __METHOD__ );
			return false;
		}
		
        return $token;
    }
	
	/**
	 * Check if the token is valid
	 *
	 * @param string $token
	 * @param string $token_type
	 * @return bool
	 */
    public function check( $token, $token_type ) {
        $user_id = $this->db->prepare( 'SELECT `user_id` FROM `tokens` WHERE `key` = ? AND `token_type` = ? AND `date_valid` > NOW()', 'ss', $token, $token_type )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check token.', __LINE__, __METHOD__ );
			return false;
		}
		
        return $user_id;
    }
	
	/**
	 * Delete Token
	 *
	 * @param string $token
	 * @return bool
	 */
    public function delete( $token ) {
		// Delete the token
		$this->db->prepare( 'DELETE FROM `tokens` WHERE `key` = ?', 's', $token )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete token.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
    }
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}