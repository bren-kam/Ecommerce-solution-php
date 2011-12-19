<?php

/**
 * Handles all the checklists
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Checklists extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get all information of the checklists
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_checklists( $where, $order_by, $limit ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND c.`company_id` = ' . $user['company_id'] : $where . ' AND c.`company_id` = ' . $user['company_id'];
		
		// Get the checklists
		$checklists = $this->db->get_results( "SELECT a.`checklist_id`, a.`type`, a.`date_created`, b.`title`, d.`contact_name` AS 'online_specialist', DATEDIFF( DATE_ADD( a.`date_created`, INTERVAL 30 DAY ), NOW() ) AS 'days_left' FROM `checklists` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) INNER JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) LEFT JOIN `users` AS d ON ( b.`os_user_id` = d.`user_id` ) WHERE b.`status` = 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list checklists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $checklists;
	}
	
	/**
	 * Count all the checklists
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_checklists( $where ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND c.`company_id` = ' . $user['company_id'] : $where . ' AND c.`company_id` = ' . $user['company_id'];
		
		// Get the checklist count
		$checklist_count = $this->db->get_var( "SELECT COUNT( a.`checklist_id` ) FROM `checklists` AS a LEFT JOIN `websites` AS b ON a.`website_id` = b.`website_id` INNER JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) WHERE b.`status` = 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count checklists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $checklist_count;
	}
	
	/**
	 * incomplete_checklists
	 *
	 * Returns a list of websites that have incomplete checklists
	 *
	 * @return array
	 */
	public function incomplete_checklists() {
		$website_ids = $this->db->get_results( 'SELECT a.`checklist_id`, a.`website_id` FROM `checklists` AS a LEFT JOIN `checklist_website_items` AS b ON ( a.`checklist_id` = b.`checklist_id` ) WHERE b.`checked` = 0 GROUP BY `website_id`', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get incomplete checklists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ar::assign_key( $website_ids, 'website_id', true );
	}
	
	/**
	 * Gets a specific checklist
	 *
	 * @param int $checklist_id
	 * @return array
	 */
	public function get( $checklist_id ) {
		$checklist_id = (int) $checklist_id;
		
		$checklist = $this->db->get_row( "SELECT a.`checklist_id`, a.`website_id`, a.`type`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, b.`title`, DATEDIFF( DATE_ADD( a.`date_created`, INTERVAL 30 DAY), NOW() ) AS 'days_left' FROM `checklists` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`checklist_id` = $checklist_id ORDER BY days_left ASC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get checklist.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $checklist;
	}
	
	/**
	 * Get Items List for Checklist
	 *
	 * @param int $checklist_id
	 * @return array
	 */
	public function get_checklist_items( $checklist_id ) {
		$sections = $this->db->get_results( 'SELECT `checklist_item_id`, `section` FROM `checklist_items` GROUP BY `section` ORDER BY `sequence` ASC', ARRAY_A);
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get sections.', __LINE__, __METHOD__ );
			return false;
		}
		
		foreach ( $sections as $s ) {
			$arr[$s['section']] = $this->db->prepare( 'SELECT a.`checklist_item_id`, a.`name`, a.`assigned_to`, a.`sequence`, b.`checked`, b.`checklist_website_item_id`, c.`rcount` AS notes_count FROM `checklist_items` AS a INNER JOIN `checklist_website_items` AS b ON( a.`checklist_item_id` = b.`checklist_item_id` ) LEFT JOIN ( SELECT COUNT(*) AS rcount, `checklist_website_item_id` FROM `checklist_website_item_notes` GROUP BY `checklist_website_item_id` ) AS c ON( b.`checklist_website_item_id` = c.`checklist_website_item_id` ) WHERE b.`checklist_id` = ? AND a.`section` = ? ORDER BY a.`sequence` ASC', 'is', $checklist_id, $s['section'] )->get_results( '', ARRAY_A );
		}
		
		return $arr;
	}
	
	/**
	 * Adds a note to a checklist website item
	 *
	 * @param int $checklist_website_item_id
	 * @param string $note
	 * @return bool
	 */
	public function add_note( $checklist_website_item_id, $note ) {
		global $user;
		
		$this->db->insert( 'checklist_website_item_notes', array( 'checklist_website_item_id' => $checklist_website_item_id, 'user_id' => $user['user_id'], 'note' => $note, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add checklist item.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update note
	 *
	 * @param int $checklist_website_item_note_id
	 * @param string $note
	 * @return bool
	 */
	public function update_note( $checklist_website_item_note_id, $note ){
		$this->db->update( 'checklist_website_item_notes', array( 'note' => $note ), array( 'checklist_website_item_note_id' => $checklist_website_item_note_id ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website item note.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update checklist item
	 *
	 * @param int $checklist_website_item_id
	 * @param bool $state whether checked or not
	 * @return bool
	 */
	public function update_item( $checklist_website_item_id, $state ){		
		$state = ( $state == 'true' ) ? 1 : 0;
		$this->db->query( sprintf( "UPDATE `checklist_website_items` SET `checked` = %d, `date_checked` = NOW() WHERE `checklist_website_item_id` = %d", $state, $checklist_website_item_id ) );

		return ( mysql_errno() ) ? false : true;
	}
	
	/**
	 * Get's all the notes relating to a item_id
	 *
	 * @param int $item_id 
	 * @return array
	 */
	public function get_notes( $item_id ) {
		$notes = $this->db->get_results( "SELECT a.`checklist_website_item_note_id`, a.`note`, b.`contact_name`, a.`date_created` FROM `checklist_website_item_notes` AS a INNER JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`checklist_website_item_id` = $item_id ORDER BY `date_created` DESC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get notes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $notes;
	}
	
	/**
	 * Delete a note from the checklist item
	 * 
	 * @param int $checklist_website_item_note_id
	 * @return bool
	 */
	public function delete_note( $checklist_website_item_note_id ){
		$this->db->query( 'DELETE FROM `checklist_website_item_notes` WHERE `checklist_website_item_note_id` = ' . (int) $checklist_website_item_note_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete checklist note.', __LINE__, __METHOD__ );
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