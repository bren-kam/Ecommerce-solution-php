<?php
/**
 * Handles all ticket comments
 *
 * @package Real Statistics
 * @since 1.0
 */
class Ticket_Comments extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Add new ticket comment
	 *
	 * @param int $ticket_id
	 * @param int $user_id
	 * @param string $comment
	 * @param int $private (optional|0)
	 * @param array $attachments (optional|array)
	 * @return int
	 */
	public function add( $ticket_id, $user_id, $comment, $private = 0, $attachments = array() ) {
		$this->db->insert( 'ticket_comments', array( 'ticket_id' => $ticket_id, 'user_id' => $user_id, 'comment' => $comment, 'private' => $private, 'date_created' => dt::now() ), 'iisis' );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add ticket comment.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the inserted id
		$ticket_comment_id = $this->db->insert_id;
		
		// Link attachments
		$this->link_attachments( $ticket_comment_id, $attachments );
		
		return $ticket_comment_id;
	}
	
	/**
	 * Link Attachments to a ticket comment
	 *
	 * @param int $ticket_comment_id
	 * @param array $attachments
	 * @return bool
	 */
	public function link_attachments( $ticket_comment_id, $attachments ) {
		// Make sure we have valid attachments
		if ( !is_array( $attachments ) || 0 == count( $attachments ) )
			return;
		
		// Form the data to link
		$values = '';
		
		foreach ( $attachments as $a ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $ticket_comment_id, " . (int) $a . ')';
		}
		
		// Link it
		$this->db->query( "INSERT INTO `ticket_comment_upload_links` ( `ticket_comment_id`, `ticket_upload_id` ) VALUES $values" );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to link ticket comments to upload links.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Updates a ticket comment
	 *
	 * @since 1.0.0
	 *
	 * @param int $ticket_comment_id
	 * @param string $comment
	 * @return bool
	 */
	public function update( $ticket_comment_id, $comment ) {
		$this->db->update( 'ticket_comments', array( 'comment' => $comment ), array( 'ticket_comment_id' => $ticket_comment_id ), 's', 'i' );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update ticket comment.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Comments
	 *
	 * @param int $ticket_id
	 * @return array
	 */
	public function get( $ticket_id ) {
		$comments = $this->db->prepare( "SELECT a.`ticket_comment_id`, a.`user_id`, a.`comment`, a.`private`, UNIX_TIMESTAMP( a.`date_created` ) AS date, b.`contact_name` AS name FROM `ticket_comments` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`ticket_id` = ? ORDER BY a.`date_created` DESC", 'i', $ticket_id )->get_results( '', ARRAY_A );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( "Failed to get ticket comments.", __LINE__, __METHOD__ );
			return false;
		}
		
		// Get attachments if there are any
		$attachments = $this->db->prepare( 'SELECT a.`key`, b.`ticket_comment_id` FROM `ticket_uploads` AS a LEFT JOIN `ticket_comment_upload_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) LEFT JOIN `ticket_comments` AS c ON ( b.`ticket_comment_id` = c.`ticket_comment_id` ) WHERE c.`ticket_id` = ?', 'i', $ticket_id )->get_results( '', ARRAY_A );

		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( "Failed to get ticket attachments.", __LINE__, __METHOD__ );
			return false;
		}

		$comments = ar::assign_key( $comments, 'ticket_comment_id' );
		
		if ( is_array( $attachments ) )
		foreach ( $attachments as $a ) {
			$comments[$a['ticket_comment_id']]['attachments'][] = array( 'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $a['key'], 'name' => ucwords( str_replace( '-', ' ', f::name( $a['key'] ) ) ) );
		}
		
		return $comments;
	}
	
	/**
	 * Get a single comment
	 *
	 * @param int $ticket_comment_id
	 * @return array
	 */
	public function get_single( $ticket_comment_id ) {
		$comment = $this->db->prepare( "SELECT a.`ticket_comment_id`, a.`user_id`, a.`comment`, a.`private`, UNIX_TIMESTAMP( a.`date_created` ) AS date, b.`contact_name` AS name FROM `ticket_comments` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`ticket_comment_id` = ? ORDER BY a.`date_created` DESC", 'i', $ticket_comment_id )->get_row( '', ARRAY_A );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( "Failed to get a ticket comment.", __LINE__, __METHOD__ );
			return false;
		}
		
		return $comment;
	}
	
	/**
	 * Delete a comment
	 *
	 * @param int $ticket_comment_id
	 * @return bool
	 */
	public function delete( $ticket_comment_id ) {
		// Get attachments if there are any
		// @Fix why wont db->get_col work?
		$attachments = $this->db->prepare( 'SELECT a.`ticket_upload_id` FROM `ticket_uploads` AS a LEFT JOIN `ticket_comment_upload_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) WHERE b.`ticket_comment_id` = ?', 'i', $ticket_comment_id )->get_results( '', ARRAY_A );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( "Failed to get ticket attachments.", __LINE__, __METHOD__ );
			return false;
		}
		
		$this->db->query( 'DELETE FROM `ticket_comments` WHERE `ticket_comment_id` = ' . (int) $ticket_comment_id );
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( "Failed to delete ticket comment.", __LINE__, __METHOD__ );
			return false;
		}
		
		if ( is_array( $attachments ) ) {
			// Delete attachments
			$f = new Files;
			
			foreach ( $attachments as $a ) {
				$f->remove_upload( $a['ticket_upload_id'] );
			}
			
			// Delete links
			$this->db->query( 'DELETE FROM `ticket_comment_upload_links` WHERE `ticket_comment_id` = ' . (int) $ticket_comment_id );
			
			// If there was an error
			if ( $this->db->errno() ) {
				$this->_err( "Failed to delete ticket comment upload links.", __LINE__, __METHOD__ );
				return false;
			}
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
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}