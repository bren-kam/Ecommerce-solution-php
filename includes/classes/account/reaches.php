<?php
/**
 * Sends Reaches to the system
 *
 * @package Real Statistics
 * @since 1.0
 */

class Reaches extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Create Reach
	 *
	 * @param string $summary
	 * @param string $message
	 * @return int
	 */
	public function create( $message ) {
		global $user, $u;
		
		$result = $this->db->insert( 'reaches', array( 'user_id' => $user['user_id'], 'assigned_to_user_id' => 493, 'website_id' => 0, 'message' => nl2br( htmlentities( stripslashes( $message ) ) ), 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiiss' ); 
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create reach.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the assigned to user
		$assigned_to_user = $u->get_user( 493 );
		
		// Send an email
		// return fn::mail( $assigned_to_user['email'], 'New ' . stripslashes( $user['website']['title'] ) . ' Reach - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\nSummary: $summary\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/reaches/reach/?tid=" . $this->db->insert_id );
	}
	
	/**
	 * Create an empty Reach
	 *
	 * @return int
	 */
	public function create_empty() {
		global $user, $u;
		
		$result = $this->db->insert( 'reaches', array( 'status' => -1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'is' ); 
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create empty reach.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update Reach
	 *
	 * @param int $reach_id
	 * @param string $summary
	 * @param string $message
	 * @param array $images
	 * @return bool
	 */
	public function update( $reach_id, $summary, $message ) {
		global $user, $u;
		
		// Type Juggling
		$reach_id = (int) $reach_id;
		
		// User is "Technical", "technical@greysuitretail.com"
		$result = $this->db->update( 'reaches', array( 'user_id' => $user['user_id'], 'assigned_to_user_id' => 493, 'website_id' => 0, 'summary' => stripslashes( $summary ), 'message' => htmlentities( nl2br( $message ) ), 'browser_name' => $this->b['name'], 'browser_version' => $this->b['version'], 'browser_platform' => $this->b['platform'], 'browser_user_agent' => $this->b['user_agent'], 'status' => 0 ), array( 'reach_id' => $reach_id ), 'iiissssssi', 'i' ); 
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update reach.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add images
		$values = '';
		
		/* foreach ( $images as $i ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $reach_id, " . (int) $i . ')';
		} 
		
		if ( !empty( $values ) ) {
			// Add image links
			$this->db->query( "INSERT INTO `reach_links` ( `website_reach_id`, `reach_upload_id` ) VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to create reach links.', __LINE__, __METHOD__ );
				return false;
			}
		} */
		
		// Get the assigned to user
		$assigned_to_user = $u->get_user( 493 );
		
		// Send an email
		//return fn::mail( $assigned_to_user['email'], 'New ' . stripslashes( $user['website']['title'] ) . ' Reach - ' . $summary, "Name: " . $user['contact_name'] . "\nEmail: " . $user['email'] . "\n\n" . $message . "\n\nhttp://admin." . DOMAIN . "/reaches/reach/?tid=" . $this->db->insert_id );
	}
	
	/**
	 * Update reach priority
	 *
	 * @since 1.0.0
	 *
	 * @param int $reach_id
	 * @param int $priority
	 * @return bool
	 */
	public function update_priority( $reach_id, $priority ) {
		global $user;
		
		$this->db->update( 'website_reaches', array( 'priority' => $priority ), array( 'website_reach_id' => $reach_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update reach priority.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update reach status
	 *
	 * @since 1.0.0
	 *
	 * @param int $reach_id
	 * @param int $status
	 * @return bool
	 */
	public function update_status( $reach_id, $status ) {
		global $user;
		
		$this->db->update( 'website_reaches', array( 'status' => $status ), array( 'website_reach_id' => $reach_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update reach status.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update reach assigned_to
	 *
	 * @since 1.0.0
	 *
	 * @param int $reach_id
	 * @param int $assigned_to_user_id
	 * @return bool
	 */
	public function update_assigned_to( $reach_id, $assigned_to_user_id ) {
		$this->db->update( 'website_reaches', array( 'assigned_to_user_id' => $assigned_to_user_id, 'assigned_to_date' => dt::date('Y-m-d H:i:s') ), array( 'website_reach_id' => $reach_id ), 'is', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update reach assigned_to_user_id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $assigned_to_user_id;
	}
	
	/**
	 * Update reach waiting
	 *
	 * @param int $reach_id
	 * @param int $waiting
	 * @return bool
	 */
	public function update_waiting( $reach_id, $waiting ) {
		//Type cast
		$waiting = (int) $waiting;
		
		$this->db->update( 'website_reaches', array( 'waiting' => $waiting ), array( 'website_reach_id' => $reach_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update reach assigned_to_user_id.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}

	/**
	 * Update date due
	 *
	 * @since 1.0.0
	 *
	 * @param int $reach_id
	 * @param string $date_due
	 * @return bool
	 */
	public function update_date_due( $reach_id, $date_due ) {
		global $user;
		
		$this->db->update( 'reaches', array( 'date_due' => $date_due ), array( 'reach_id' => $reach_id ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update reach date_due.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}

	/**
	 * Returns reach
	 *
	 * @param int $reach_id
     * @param bool $meta [optional]
	 * @return array
	 */
	public function get( $reach_id, $meta = false ) {
		$reach = $this->db->get_row( "SELECT a.`website_reach_id`, a.`website_user_id`, a.`assigned_to_user_id`, a.`message`, a.`priority`, a.`status`, a.`assigned_to_date`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, CONCAT( b.`billing_first_name`, ' ', IF( b.`billing_last_name`,  b.`billing_last_name`, '' ) ) AS name, b.`email`, c.`website_id`, c.`title` AS website, c.`subdomain`, c.`domain`, COALESCE( d.`role`, 7 ) AS role FROM `website_reaches` AS a LEFT JOIN `website_users` AS b ON ( a.`website_user_id` = b.`website_user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( a.`assigned_to_user_id` = d.`user_id` ) WHERE a.`website_reach_id` = " . (int) $reach_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get reach.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get meta if there is any
		if( $meta ) {
			// This will be expanded in the future as the meta is further developed
			$reach['meta'] = $this->db->get_results( "SELECT a.`key`, a.`value` FROM `website_reach_meta` AS a WHERE a.`website_reach_id` = {$reach['website_reach_id']};", ARRAY_A );
			$reach['meta'] = ar::assign_key( $reach['meta'], 'key', true);
			
			// If there was an error
			if ( $this->db->errno() ) {
				$this->_err( "Failed to get reach meta.", __LINE__, __METHOD__ );
				return false;
			}
		}

		return $reach;
	}
	
	/**
	 * Returns reaches for listing
	 *
	 * @param string $limit
	 * @param string $where
	 * @param string $order_by
	 * @return array
	 */
	public function list_reaches( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$order_by = ( !$order_by ) ? "ORDER BY website_reach_id DESC" : $order_by;
		
        // Get linked reaches
        $reaches = $this->db->get_results( "SELECT a.`website_reach_id`, IF( 0 = a.`assigned_to_user_id`, 'Unassigned', c.`contact_name` ) AS assigned_to, a.`status`, a.`priority`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, CONCAT( b.`billing_first_name`, ' ', IF( b.`billing_last_name`,  b.`billing_last_name`, '' ) ) AS name, b.`email`, IF( 1 = a.`status` OR d.`website_reach_comment_id` IS NOT NULL AND d.`user_id` = a.`assigned_to_user_id`, 0, 1 ) AS waiting, e.`title` AS website FROM `website_reaches` AS a LEFT JOIN `website_users` AS b ON ( a.`website_user_id` = b.`website_user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN ( SELECT `website_reach_comment_id`, `website_reach_id`, `user_id` FROM `website_reach_comments` ORDER BY `website_reach_comment_id` DESC ) AS d ON ( a.`website_reach_id` = d.`website_reach_id` ) LEFT JOIN `websites` AS e ON ( a.`website_id` = e.`website_id` ) WHERE 1" . $where . " GROUP BY a.`website_reach_id` $order_by, d.`website_reach_comment_id` DESC LIMIT $limit", ARRAY_A );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to list reaches.', __LINE__, __METHOD__ );
            return false;
        }

        return $reaches;
	}
	
	/**
	 * Returns the number of reaches for listing
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_reaches( $where ) {
        // Get the reach count
        $count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`website_reach_id`) FROM `website_reaches` AS a LEFT JOIN `users` AS b ON ( a.`website_user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN ( SELECT `website_reach_comment_id`, `website_reach_id`, `user_id` FROM `website_reach_comments` ORDER BY `website_reach_comment_id` DESC ) AS d ON ( a.`website_reach_id` = d.`website_reach_id` ) LEFT JOIN `websites` AS e ON ( a.`website_id` = e.`website_id` ) WHERE 1" . $where );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to count reaches.', __LINE__, __METHOD__ );
            return false;
        }

		return $count;
	}
	
	/**
	 * Convenience methods
	 * 
	 * Formats data for templates.  Contains some minor templating logic.
	 * 
	 */
	
	/**
	 * Returns a reach type meta in a human readable format
	 */
	public function _get_friendly_type( $type ) {
		$out = null;
		switch( $type ) {
			case 'quote':
				$out = _('Quote');
				break;
			default:
				$out = _('Reach');
				break;
		}
		
		return $out;
	}
	
	/**
	 * Returns all reach meta in a useful format
	 */
	public function _get_friendly_info( $meta ) {
		$out = array();
		
		switch( $meta['type'] ) {
			case 'quote':
				$link = $meta['product-link'];
				$out['Product'] = '<a href="' . $link . '" title="' . $meta['product-name'] . '">' . $meta['product-name'] . '</a>';
				$out['SKU'] = '<a href="' . $link . '" title="' . $meta['product-sku'] . '">' . $meta['product-sku'] . '</a>';
				break;
			default:
				break;
		}
		
		return $out;
	}
	
	
	/**
	 * Email overdue reaches
	 *
	 * @return bool
	 */
	public function email_overdue_reaches() {
		/*$overdue_reaches = $this->db->get_results( "SELECT a.`email`, b.`website_reach_id`, b.`summary`, c.`name`, c.`domain` FROM `users` AS a LEFT JOIN `website_reaches` AS b ON ( a.`user_id` = b.`assigned_to_user_id` ) LEFT JOIN `companies` AS c ON ( a.`company_id` = c.`company_id` ) WHERE a.`status` = 0 AND a.`date_due` <> '0000-00-00 00:00:00' AND a.`date_due` < DATE( NOW() )", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get emails to email for overdue reaches.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Email each over the overdue persons
		if ( is_array( $overdue_reaches ) )
		foreach ( $overdue_reaches as $ot ) {
			fn::mail( $ot['email'], html_entity_decode( $ot['name'] ) . ' Overdue Reach: #' . $ot['reach_id'] . ' - ' . $ot['summary'], "Summary:\n" . $ot['summary'] . "\n\nClick here to view the reach:\nhttp://admin." . $ot['domain'] . "/reaches/reach/?tid=" . $ot['reach_id'] );
		}
		
		return true;*/
	}
	
	/**
	 * Clean Uploads (remove all the ones that aren't needed)
	 *
	 * @return bool
	 */
	public function clean_uploads() {
		/*$f = new Files;
		
		// Get attachments if there are any
		$attachments = $this->db->get_col( 'SELECT a.`key` FROM `reach_uploads` AS a LEFT JOIN `reach_links` AS b ON ( a.`reach_upload_id` = b.`reach_upload_id` ) LEFT JOIN `website_reaches` AS c ON ( b.`website_reach_id` = c.`website_reach_id` ) WHERE c.`status` = -1 AND c.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get attachments.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Remove them all
		$f->remove_uploads( $attachments );
		
		// Delete reach uploads, reach links and reaches themselves (this is awesome!)
		$this->db->query( 'DELETE a.*, b.*, c.* FROM `reach_uploads` AS a LEFT JOIN `reach_links` AS b ON ( a.`reach_upload_id` = b.`reach_upload_id` ) LEFT JOIN `website_reaches` AS c ON ( b.`website_reach_id` = c.`website_reach_id` ) WHERE c.`status` = -1 AND c.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete reach upload.', __LINE__, __METHOD__ );
			return false;
		}*/
		
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