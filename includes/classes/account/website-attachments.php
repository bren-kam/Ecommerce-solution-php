<?php
/**
 * Handles all the website attachments
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Website_Attachments extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Create attachment
	 * 
	 * @param int $website_page_id
	 * @param string $key
	 * @param string $value
	 * @param int $sequence (optional|0)
	 * @param int $status (optional|1)
	 * @return int
	 */
	public function create( $website_page_id, $key, $value, $sequence = 0, $status = 1 ) {
		$this->db->insert( 'website_attachments', array( 'website_page_id' => $website_page_id, 'key' => $key, 'value' => $value, 'sequence' => $sequence, 'status' => $status ), 'issii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get create website attachments.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update attachment
	 * 
	 * @param int $website_page_id
	 * @param string $key
	 * @param string $value
	 * @return int
	 */
	public function update( $website_page_id, $key, $value ) {
		$this->db->update( 'website_attachments', array( 'value' => $value ), array( 'website_page_id' => $website_page_id, 'key' => $key ), 's', 'is' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update page attachment', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets an attachment
	 * 
	 * @param int $website_attachment_id
	 * @return array
	 */
	public function get( $website_attachment_id ) {
		global $user;
		
		// Type Juggling
		$website_attachment_id = (int) $website_attachment_id;
		$website_id = (int) $user['website']['website_id'];
		
		$attachment = $this->db->get_row( "SELECT a.`website_attachment_id`, a.`key`, a.`value`, a.`extra`, a.`status` FROM `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) WHERE a.`website_attachment_id` = $website_attachment_id AND b.`website_id` = $website_id ORDER BY a.`sequence`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website page attachment.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $attachment;
	}
	
	/**
	 * Gets attachments for a specific page
	 * 
	 * @param int $website_page_id
	 * @return array
	 */
	public function get_by_page( $website_page_id ) {
		global $user;
		
		// Type Juggling
		$website_page_id = (int) $website_page_id;
		$website_id = (int) $user['website']['website_id'];
		
		$attachments = $this->db->get_results( "SELECT a.`website_attachment_id`, a.`key`, a.`value`, a.`extra`, a.`status` FROM `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) WHERE a.`website_page_id` = $website_page_id AND b.`website_id` = $website_id ORDER BY a.`sequence`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website page attachments.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $attachments;
	}
	
	/**
	 * Check weither an attachment exists of specific name;
	 * 
	 * @param int $website_page_id
	 * @param string $key
	 * @return array
	 */
	public function get_by_name( $website_page_id, $key ) {
		$attachments = $this->db->prepare( 'SELECT `website_attachment_id`, `key`, `value`, `extra` FROM `website_attachments` WHERE `key` = ? AND `website_page_id` = ?', 'si', $key, $website_page_id )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website attachments.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( 1 == count( $attachments ) ) ? $attachments[0] : $attachments;
	}
	
	/**
	 * Update attachment value
	 *
	 * @param int $website_attachment_id
	 * @param string $value
	 * @return bool
	 */
	public function update_value( $website_attachment_id, $value ) {
		global $user;
		
		$this->db->prepare( 'UPDATE `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) SET a.`value` = ? WHERE a.`website_attachment_id` = ? AND b.`website_id` = ?' , 'sii', $value, $website_attachment_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update attachment value.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update attachment extra
	 *
	 * @param int $website_attachment_id
	 * @param string $extra
	 * @return bool
	 */
	public function update_extra( $website_attachment_id, $extra ) {
		global $user;
		
		$this->db->prepare( 'UPDATE `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) SET a.`extra` = ? WHERE a.`website_attachment_id` = ? AND b.`website_id` = ?' , 'sii', $extra, $website_attachment_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update attachment extra.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
		
	/**
	 * Update attachment status
	 *
	 * @param int $website_attachment_id
	 * @return bool
	 */
	 public function update_status( $website_attachment_id, $status ) {
		global $user;
		
		$this->db->prepare( 'UPDATE `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) SET a.`status` = ? WHERE a.`website_attachment_id` = ? AND b.`website_id` = ?' , 'iii', $status, $website_attachment_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update attachment status.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	 }
	
	/**
	 * Update attachment sequence
	 *
	 * @param array $sequence
	 * @return bool
	 */
	 public function update_sequence( array $sequence ) { // Type Hinting (First one EVER)
		 global $user;
		 
		 // Type Juggle
		 $website_id = (int) $user['website']['website_id'];
		 
		 // Prepare statement
		$statement = $this->db->prepare( "UPDATE `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) SET a.`sequence` = ? WHERE a.`website_attachment_id` = ? AND b.`website_id` = $website_id" );
		$statement->bind_param( 'ii', $count, $website_attachment_id );
		
		foreach ( $sequence as $count => $website_attachment_id ) {
			$statement->execute();
			
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( 'Failed to update website attachments sequence', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Delete any attachment
	 * 
	 * @param int $website_attachment_id
	 * @return bool
	 */
	public function delete( $website_attachment_id ) {
		global $user;
		
		$this->db->prepare( 'DELETE a.* FROM `website_attachments` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) WHERE a.`website_attachment_id` = ? AND b.`website_id` = ?', 'ii', $website_attachment_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete a website attachment.', __LINE__, __METHOD__ );
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