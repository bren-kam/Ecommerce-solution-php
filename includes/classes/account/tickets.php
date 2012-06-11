<?php
/**
 * Handles all the creation of tickets
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Tickets extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Create Ticket
	 *
	 * @param string $summary
	 * @param string $message
	 * @return int
	 */
	public function create( $summary, $message ) {
		global $user, $u;
		
		if( stristr( $summary, 'Product Request' ) ) {
			$assigned_to_user_id = 73; // Chris @ the lively merchant
		} else {
			// Techincal : Online Specialist
			$assigned_to_user_id = ( $user['role'] > 5 ) ? 493 : $user['website']['os_user_id']; 
		}

        $message = str_replace( array( '’', '‘', '”', '“' ), array( "'", "'", '"', '"' ), $message );
        $message = nl2br( format::links_to_anchors( htmlentities( $message ), true , true ) );

		$this->db->insert( 'tickets', array( 'user_id' => $user['user_id'], 'assigned_to_user_id' => $assigned_to_user_id, 'website_id' => $user['website']['website_id'], 'summary' => stripslashes( $summary ), 'message' => $message, 'browser_name' => $this->b['name'], 'browser_version' => $this->b['version'], 'browser_platform' => $this->b['platform'], 'browser_user_agent' => $this->b['user_agent'], 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiisssssss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create ticket.', __LINE__, __METHOD__ );
			return false;
		}

        $ticket_id = $this->db->insert_id;

		// Get the assigned to user
		$assigned_to_user = $u->get_user( $assigned_to_user_id );
		
		// Send an email
		fn::mail( $assigned_to_user['email'], 'New ' . stripslashes( $user['website']['title'] ) . ' Ticket - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\nSummary: $summary\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=$ticket_id" );

        return $ticket_id;
	}
	
	/**
	 * Create an empty Ticket
	 *
	 * @return int
	 */
	public function create_empty() {
		global $user, $u;
		
		$result = $this->db->insert( 'tickets', array( 'status' => -1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'is' ); 
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create empty ticket.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update Ticket
	 *
	 * @param int $ticket_id
	 * @param string $summary
	 * @param string $message
	 * @param array $images
	 * @return bool
	 */
	public function update( $ticket_id, $summary, $message, $images ) {
		global $user, $u;
		
		// Type Juggling
		$ticket_id = (int) $ticket_id;
		
		// Techincal : Online Specialist
		$assigned_to_user_id = ( $user['user']['role'] > 5 ) ? 493 : $user['website']['os_user_id']; 
		
		$result = $this->db->update( 'tickets', array( 'user_id' => $user['user_id'], 'assigned_to_user_id' => $assigned_to_user_id, 'website_id' => $user['website']['website_id'], 'summary' => stripslashes( $summary ), 'message' => htmlentities( nl2br( stripslashes( $message ) ) ), 'browser_name' => $this->b['name'], 'browser_version' => $this->b['version'], 'browser_platform' => $this->b['platform'], 'browser_user_agent' => $this->b['user_agent'], 'status' => 0 ), array( 'ticket_id' => $ticket_id ), 'iiissssssi', 'i' ); 
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update ticket.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add images
		$values = '';
		
		foreach ( $images as $i ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $ticket_id, " . (int) $i . ')';
		}
		
		if ( !empty( $values ) ) {
			// Add image links
			$this->db->query( "INSERT INTO `ticket_links` ( `ticket_id`, `ticket_upload_id` ) VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to create ticket links.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Get the assigned to user
		$assigned_to_user = $u->get_user( $assigned_to_user_id );
		
		// Send an email
		return fn::mail( $assigned_to_user['email'], 'New ' . stripslashes( $user['website']['title'] ) . ' Ticket - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=" . $this->db->insert_id );
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}