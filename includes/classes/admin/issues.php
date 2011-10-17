<?php
/**
 * Lists and manages Issues
 *
 * @package Imagine Retailer
 * @since 1.0
 */

class Issues extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Returns issues for listing
	 *
	 * @param string $limit
	 * @param string $where
	 * @param string $order_by
	 * @return array
	 */
	public function list_issues( $limit, $where, $order_by ) {
		// Get issues
		$issues = $this->db->get_results( "SELECT `issue_key`, `message`, `occurrences`, `priority`, UNIX_TIMESTAMP( `date_created` ) AS date_created FROM `issues` WHERE 1 {$where} ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			trigger_error( 'Failed to list issues.', E_USER_ERROR );
			return false;
		}
		
		return $issues;
	}
	
	/**
	 * Returns the number of issues for listing
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_issues( $where ) {
		// Get linked tickets
		$count = $this->db->get_var( "SELECT COUNT( `issue_key` ) FROM `issues` WHERE 1 {$where}" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			trigger_error( 'Failed to count issues.', E_USER_ERROR );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Get an Issue
	 *
	 * @param string $issue_key
	 * @return array
	 */
	public function get( $issue_key ) {
		// Get issue
		$issue = $this->db->prepare( 'SELECT `issue_key`, `priority`, `number`, `message`, `line`, `file`, `backtrace`, `occurrences`, `status`, UNIX_TIMESTAMP( `date_created` ) AS date_created FROM `issues` WHERE `issue_key` = ?', 's', $issue_key )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			trigger_error( 'Failed to get issue.', E_USER_ERROR );
			return false;
		}
		
		return $issue;
	}
	
	/**
	 * Update issue's status
	 *
	 * @since 1.0.0
	 *
	 * @param string $issue_key
	 * @param int $status
	 * @return bool
	 */
	public function update_status( $issue_key, $status ) {
		global $user;
		
		$this->db->update( 'issues', array( 'status' => $status ), array( 'issue_key' => $issue_key ), 'i', 's' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			trigger_error( "Failed to update issue's status.", E_USER_ERROR );
			return false;
		}
		
		return true;
	}
	
	/********** ISSUE ERRORS **********/
	/**
	 * Get issue errors
	 *
	 * @param string $issue_key
	 * @return array
	 */
	public function get_errors( $issue_key ) {
		$errors = $this->db->prepare( "SELECT a.`user_id`, b.`contact_name` AS user, a.`website_id`, c.`title` AS website, a.`sql`, a.`sql_error`, a.`page`, a.`referer`, CONCAT( a.`browser_name`, ' ', a.`browser_version`, ' / ', a.`browser_platform` ) AS browser, UNIX_TIMESTAMP( a.`date_created` ) AS date_created FROM `issue_errors` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) WHERE a.`issue_key` = ? ORDER BY a.`date_created` DESC", 's', $issue_key )->get_results( '', ARRAY_A );
		
		// If there was an error
		if ( $this->db->errno() ) {
			trigger_error( "Failed to get the issue errors.", E_USER_ERROR );
			return false;
		}
		
		return $errors;
	}
	
	/********** ISSUE COMMENTS **********/
	
	/**
	 * Add new issue comment
	 *
	 * @since 1.0.0
	 *
	 * @param string $issue_key
	 * @param int $user_id
	 * @param string $comment
	 * @return int
	 */
	public function add_comment( $issue_key, $user_id, $comment ) {
		$this->db->insert( 'issue_comments', array( 'issue_key' => $issue_key, 'user_id' => $user_id, 'comment' => $comment, 'date_created' => dt::date('Y-m-d H:i:s') ), 'siss' );
		
		// If there was an error
		if ( $this->db->errno() ) {
			trigger_error( 'Failed to add issue comment.', E_USER_ERROR );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Get a single comment
	 *
	 * @param int $issue_comment_id
	 * @return array
	 */
	public function get_comment( $issue_comment_id ) {
		$comment = $this->db->prepare( "SELECT a.`issue_comment_id`, a.`user_id`, a.`comment`, UNIX_TIMESTAMP( a.`date_created` ) AS date, b.`contact_name` AS name FROM `issue_comments` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`issue_comment_id` = ? ORDER BY a.`date_created` DESC", 'i', $issue_comment_id )->get_row( '', ARRAY_A );
		
		// If there was an error
		if ( $this->db->errno() ) {
			trigger_error( "Failed to get the issue comment.", E_USER_ERROR );
			return false;
		}
		
		return $comment;
	}
	
	/**
	 * Get Comments
	 *
	 * @param string $issue_key
	 * @return array
	 */
	public function get_comments( $issue_key ) {
		$comments = $this->db->prepare( "SELECT a.`issue_comment_id`, a.`user_id`, a.`comment`, UNIX_TIMESTAMP( a.`date_created` ) AS date, b.`contact_name` AS name FROM `issue_comments` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`issue_key` = ? ORDER BY a.`date_created` DESC", 's', $issue_key )->get_results( '', ARRAY_A );
		
		// If there was an error
		if ( $this->db->errno() ) {
			trigger_error( "Failed to get issue comments.", E_USER_ERROR );
			return false;
		}
		
		return $comments;
	}
	
	/**
	 * Delete a comment
	 *
	 * @param int $issue_comment_id
	 * @return bool
	 */
	public function delete_comment( $issue_comment_id ) {
		$this->db->query( 'DELETE FROM `issue_comments` WHERE `issue_comment_id` = ' . (int) $issue_comment_id );
		
		// If there was an error
		if ( $this->db->errno() ) {
			trigger_error( "Failed to delete issue comment.", E_USER_ERROR );
			return false;
		}
		
		return true;
	}
}