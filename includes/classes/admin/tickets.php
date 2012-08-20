<?php
/**
 * Sends Tickets to the system
 *
 * @package Real Statistics
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

        // Lets remove any characters that might be causing a problem
        $message = str_replace( array( '’', '‘', '”', '“' ), array( "'", "'", '"', '"' ), $message );
        $message = nl2br( format::links_to_anchors( format::htmlentities( stripslashes( $message ), array('&') ), true , true ) );

		$this->db->insert( 'tickets', array( 'user_id' => $user['user_id'], 'assigned_to_user_id' => 493, 'website_id' => 0, 'summary' => stripslashes( $summary ), 'message' => $message, 'browser_name' => $this->b['name'], 'browser_version' => $this->b['version'], 'browser_platform' => $this->b['platform'], 'browser_user_agent' => $this->b['user_agent'], 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiisssssss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create ticket.', __LINE__, __METHOD__ );
			return false;
		}

        // Mark statistic for created tickets
        library('statistics-api');
        $stat = new Stat_API( config::key('rs-key') );
        $date = new DateTime();
        $stat->add_graph_value( 23451, 1, $date->format('Y-m-d') );

		// Get the assigned to user
		$assigned_to_user = $u->get_user( 493 );

		// Send an email
		return fn::mail( $assigned_to_user['email'], 'New ' . stripslashes( $user['website']['title'] ) . ' Ticket - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\nSummary: $summary\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=" . $this->db->insert_id );
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
			$this->_err( 'Failed to create empty ticket.', __LINE__, __METHOD__ );
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
		
		// User is "Technical", "technical@greysuitretail.com"
		$result = $this->db->update( 'tickets', array( 'user_id' => $user['user_id'], 'assigned_to_user_id' => 493, 'website_id' => 0, 'summary' => stripslashes( $summary ), 'message' => htmlentities( nl2br( $message ) ), 'browser_name' => $this->b['name'], 'browser_version' => $this->b['version'], 'browser_platform' => $this->b['platform'], 'browser_user_agent' => $this->b['user_agent'], 'status' => 0 ), array( 'ticket_id' => $ticket_id ), 'iiissssssi', 'i' ); 
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update ticket.', __LINE__, __METHOD__ );
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
				$this->_err( 'Failed to create ticket links.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Get the assigned to user
		$assigned_to_user = $u->get_user( 493 );
		
		// Send an email
		return fn::mail( $assigned_to_user['email'], 'New ' . stripslashes( $user['website']['title'] ) . ' Ticket - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/tickets/ticket/?tid=" . $this->db->insert_id );
	}
	
	/**
	 * Update ticket priority
	 *
	 * @since 1.0.0
	 *
	 * @param int $ticket_id
	 * @param int $priority
	 * @return bool
	 */
	public function update_priority( $ticket_id, $priority ) {
		global $user;
		
		$this->db->update( 'tickets', array( 'priority' => $priority ), array( 'ticket_id' => $ticket_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update ticket priority.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update ticket status
	 *
	 * @since 1.0.0
	 *
	 * @param int $ticket_id
	 * @param int $status
	 * @return bool
	 */
	public function update_status( $ticket_id, $status ) {
		global $user;
		
		$this->db->update( 'tickets', array( 'status' => $status ), array( 'ticket_id' => $ticket_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update ticket status.', __LINE__, __METHOD__ );
			return false;
		}

        // Mark statistic for updated tickets
        if ( 1 == $status && in_array( $user['user_id'], array( 493, 1, 814, 305, 85, 19 ) ) ) {
            library('statistics-api');
            $stat = new Stat_API( config::key('rs-key') );
            $date = new DateTime();
            $stat->add_graph_value( 23452, 1, $date->format('Y-m-d') );

            // Get the ticket
            $ticket = $this->get( $ticket_id );
            $hours = ( $date->getTimestamp() - $ticket['date_created'] ) / 3600;
            $stat->add_graph_value( 23453, round( $hours, 1 ), $date->format('Y-m-d')  );
        }

		return true;
	}
	
	/**
	 * Update ticket assigned_to
	 *
	 * @since 1.0.0
	 *
	 * @param int $ticket_id
	 * @param int $assigned_to_user_id
	 * @return bool
	 */
	public function update_assigned_to( $ticket_id, $assigned_to_user_id ) {
		global $user;
		
		$this->db->update( 'tickets', array( 'assigned_to_user_id' => $assigned_to_user_id ), array( 'ticket_id' => $ticket_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update ticket assigned_to_user_id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return (int) $assigned_to_user_id;
	}

	/**
	 * Returns ticket
	 *
	 * @param int $ticket_id
	 * @return array
	 */
	public function get( $ticket_id ) {
		// Get linked users
		$ticket = $this->db->get_row( "SELECT a.`ticket_id`, a.`user_id`, a.`assigned_to_user_id`, a.`summary`, a.`message`, a.`priority`, a.`status`, a.`browser_name`, a.`browser_version`, a.`browser_platform`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, CONCAT( b.`contact_name` ) AS name, b.`email`, c.`website_id`, c.`title` AS website, c.`subdomain`, c.`domain`, COALESCE( d.`role`, 7 ) AS role FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( a.`assigned_to_user_id` = d.`user_id` ) WHERE a.`ticket_id` = " . (int) $ticket_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get ticket.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get attachments if there are any
		$attachments = $this->db->get_col( 'SELECT a.`key` FROM `ticket_uploads` AS a LEFT JOIN `ticket_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) WHERE b.`ticket_id` = ' . (int) $ticket_id );
		
		foreach ( $attachments as $link ) {
			$ticket['attachments'][] = array( 'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $link, 'name' => ucwords( str_replace( '-', ' ', f::name( $link ) ) ) );
		}
		
		// If there was an error
		if ( $this->db->errno() ) {
			$this->_err( "Failed to get ticket attachments.", __LINE__, __METHOD__ );
			return false;
		}

		return $ticket;
	}
	
	/**
	 * Returns tickets for listing
	 *
	 * @param string $limit
	 * @param string $where
	 * @param string $order_by
	 * @return array
	 */
	public function list_tickets( $limit, $where, $order_by ) {
        global $user;

        if ( $user['role'] < 8 )
            $where .= ' AND ( c.`company_id` = ' . (int) $user['company_id'] . ' OR a.`user_id` = ' . (int) $user['user_id'] . ' )';

        // Get linked tickets
        $tickets = $this->db->get_results( "SELECT a.`ticket_id`, IF( 0 = a.`assigned_to_user_id`, 'Unassigned', c.`contact_name` ) AS assigned_to, a.`summary`, a.`status`, a.`priority`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, b.`contact_name` AS name, b.`email`, d.`title` AS website FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN `websites` AS d ON ( a.`website_id` = d.`website_id` ) WHERE 1" . $where . " GROUP BY a.`ticket_id` ORDER BY $order_by LIMIT $limit", ARRAY_A );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to list tickets.', __LINE__, __METHOD__ );
            return false;
        }

        return $tickets;
	}
	
	/**
	 * Returns the number of tickets for listing
	 *
	 * @param string $where
	 * @return int
	 */
	public function count( $where ) {
		global $user;
		
        if ( $user['role'] < 8 )
            $where .= ' AND ( c.`company_id` = ' . (int) $user['company_id'] . ' OR a.`user_id` = ' . (int) $user['user_id'] . ' )';
		
        // Get the ticket count
        $count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`ticket_id`) FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN `websites` AS e ON ( a.`website_id` = e.`website_id` ) WHERE 1" . $where );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to count tickets.', __LINE__, __METHOD__ );
            return false;
        }

		return $count;
	}
	
	/**
	 * Clean Uploads (remove all the ones that aren't needed)
	 *
	 * @return bool
	 */
	public function clean_uploads() {
		$f = new Files;
		
		// Get attachments if there are any
		$attachments = $this->db->get_col( 'SELECT a.`key` FROM `ticket_uploads` AS a LEFT JOIN `ticket_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) LEFT JOIN `tickets` AS c ON ( b.`ticket_id` = c.`ticket_id` ) WHERE c.`status` = -1 AND c.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attachments.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Remove them all
		$f->remove_uploads( $attachments );
		
		// Delete ticket uploads, ticket links and tickets themselves (this is awesome!)
		$this->db->query( 'DELETE a.*, b.*, c.* FROM `ticket_uploads` AS a LEFT JOIN `ticket_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) LEFT JOIN `tickets` AS c ON ( b.`ticket_id` = c.`ticket_id` ) WHERE c.`status` = -1 AND c.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete ticket upload.', __LINE__, __METHOD__ );
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
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}