<?php
/**
 * Handles all the website files
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Website_Files extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Add file
	 * 
	 * @param string $file_path
	 * @return int|bool
	 */
	public function add_file( $file_path ) {
		$website_file = $this->get_by_file_path( $file_path );
		
		// Already there, we don't need to add it again
		if ( !empty( $website_file ) )
			return true;
		
		global $user;
		
		$this->db->insert( 'website_files', array( 'website_id' => $user['website']['website_id'], 'file_path' => $file_path, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add website file.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Gets all the website files
	 * 
	 * @return array
	 */
	public function get_all() {
		global $user;
		
		$website_files = $this->db->prepare( "SELECT `website_file_id`, `website_id`, REPLACE( `file_path`, '[domain]', ? ) AS file_path, `date_created` FROM `website_files` WHERE `website_id` = ?", 'si', $user['website']['domain'], $user['website']['website_id'] )->get_results( '', ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website files.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_files;
	}
	
	/**
	 * Get file count
	 *
	 * @return int
	 */
	public function get_count() {
		global $user;
		
		$count = $this->db->get_var( 'SELECT COUNT( `website_file_id` ) FROM `website_files` WHERE `website_id` = ' . (int) $user['website']['website_id'] );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get file count.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Get a file by path
	 * 
	 * @param string $file_path
	 * @return array
	 */
	public function get_by_file_path( $file_path ) {
		global $user;
		
		$file = $this->db->prepare( "SELECT `website_file_id`, `website_id`, REPLACE( `file_path`, '[domain]', ? ) AS file_path, `date_created` FROM `website_files` WHERE `website_id` = ? AND file_path = ?", 'sis', $user['website']['domain'], $user['website']['website_id'], $file_path )->get_row( '', ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website file by file path.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $file;
	}
	
	/**
	 * Get a file by website_file_id
	 * 
	 * @param int $website_file_id
	 * @return array
	 */
	public function get_by_id( $website_file_id ) {
		global $user;
		
		$file = $this->db->prepare( "SELECT `website_file_id`, `website_id`, REPLACE( `file_path`, '[domain]', ? ) AS file_path, `date_created` FROM `website_files` WHERE `website_file_id` = ?", 'si', $user['website']['domain'], $website_file_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website file by id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $file;
	}
	
	/**
	 * Delete a file
	 * 
	 * @param int $website_file_id
	 * @return bool
	 */
	public function delete( $website_file_id ) {
		$this->db->query( 'DELETE FROM `website_files` WHERE `website_file_id` = ' . (int) $website_file_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website file.', __LINE__, __METHOD__ );
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