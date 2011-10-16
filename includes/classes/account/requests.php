<?php
/**
 * Handles all the requests
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Requests extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
	}
	
	/**
	 * Sends a request to update page information
	 *
	 * @param int $website_page_id
	 * @param string $content
	 * @param string $meta_title
	 * @param string $meta_description
	 * @param string $meta_keywords
	 * @return bool
	 */
	public function update_page( $website_page_id, $content, $meta_title, $meta_description, $meta_keywords ) {
		// See if a request already exists
		$request_page_id = $this->exists( $website_page_id );
		
		// If we need to update it
		if( $request_page_id )
			return $this->update_request_page( $request_page_id, $content, $meta_title, $meta_description, $meta_keywords );
		
		// No request has been created yet
		$request_id = $this->create( 'A page update has been requested.', 'Page Update' );
		
		if( !$request_id )
			return false;
		
		// Create request page
		$this->db->insert( 'request_pages', array( 'request_id' => $request_id, 'website_page_id' => $website_page_id, 'content' => $content, 'meta_title' => $meta_title, 'meta_description' => $meta_description, 'meta_keywords' => $meta_keywords, 'date_created' => date_time::date('Y-m-d H:i:s') ), 'iisssss' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to create request page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Updates a page request
	 *
	 * @param int $request_page_id
	 * @param string $content
	 * @param string $meta_title
	 * @param string $meta_description
	 * @param string $meta_keywords
	 * @return bool
	 */
	public function update_request_page( $request_page_id, $content, $meta_title, $meta_description, $meta_keywords ) {
		// Update existing request
		$this->db->update( 'request_pages', array( 'content' => $content, 'meta_title' => $meta_title, 'meta_description' => $meta_description, 'meta_keywords' => $meta_keywords ), array( 'request_page_id' => $request_page_id ), 'ssss', 'i' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get check if request exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update Top Section of website
	 *
	 * @param string $phone_number (optional|)
	 * @param string $image (optional|)
	 * @return bool
	 */
	public function update_top( $phone_number = '', $image = '' ) {
		// One of the arguments is required
		if( empty( $phone_number ) && empty( $image ) )
			return false;
		
		global $user;
		
		// See if a header request exists
		$request = $this->header_request();
		
		if( $request ) {
			// For deprecated style
			$record = unserialize( html_entity_decode( $request['request'] ) );
			
			$values['phone_number'] = ( empty( $phone_number ) ) ? $record['phone_number'] : $phone_number;
			$values['image'] = ( empty( $image ) ) ? $record['image'] : $image;
			
			$this->db->update( 'requests', array( 'request' => serialize( $values ) ), array( 'request_id' => $request['request_id'] ), 's', 'i' );
			
			// Handle any error
			if( $this->db->errno() ) {
				$this->err( 'Failed to update header request.', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			$values['phone_number'] = $phone_number;
			
			if( !empty( $image ) )
				$values['image'] = $image;
			
			return $this->create( $values, 'Header Update' );
		}
		
		return true;
	}

	/**
	 * Header Request
	 *
	 * @return array
	 */
	public function header_request() {
		global $user;
		
		$request = $this->db->get_row( "SELECT `request_id`, `request` FROM `requests` WHERE `type` = 'Header Update' AND `status` = 0 AND `website_id` = " . (int) $user['website']['website_id'], ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get header request.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $request;
	}
	/**
	 * Creates a request
	 *
	 * @param string $request
	 * @param string $type
	 * @return int
	 */
	public function create( $request, $type ) {
		global $user;
		
		// Need to calculate the next day their requests will be done
		$due_time = time() + 86400;
		$due_day = date_time::date( 'l', strtotime( $user['website']['date_created'] ) );
		
		// Loop through the days of thw eek until they match
		while( date_time::date( 'l', $due_time ) !== $due_day ) {
			$due_time += 86400; // Adds 1 day
		}
		
		// Create the request
		$this->db->insert( 'requests', array( 'website_id' => $user['website']['website_id'], 'request' => $request, 'type' => $type, 'status' => 0, 'date_created' => date_time::date('Y-m-d H:i:s'), 'date_due' => date_time::date( 'Y-m-d', $due_time ) ), 'ississ' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to create request.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Finds out if a page request exists, returns id
	 *
	 * @param int $website_page_id
	 * @return int
	 */
	public function exists( $website_page_id ) {
		$request_page_id = $this->db->get_var( 'SELECT b.`request_page_id` FROM `requests` AS a INNER JOIN `request_pages` AS b ON ( a.`request_id` = b.`request_id` ) WHERE a.`status` = 0 AND b.`website_page_id` = ' . (int) $website_page_id );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get check if request exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $request_page_id;
	}
	
	/**
	  * Sets the metadata for a request_page
	  *
	  * @param int $website_page_id
	  * @param array $metadata
	  * @return bool
	  */
	public function set_pagemeta( $website_page_id, $metadata ) {
		$request_page_id = $this->exists( $website_page_id );
		
		// A request needs to exist prior to this
		if( !$request_page_id )
			return false;
		
		// Insert/update in one awesome query. Have to create the values for it first
		$values = '';
		
		foreach( $metadata as $k => $v ) {
			if( !empty( $values ) )
				$values .= ',';
			
			// Form values string
			$values .= "( $request_page_id, '" . $this->db->escape( $k ) . "', '" . $this->db->escape( $v ) . "' )";
		}
		
		// Insert the values, if they exist, update them instead
		$this->db->query( "INSERT INTO `request_pagemeta` ( `request_page_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update all the request pagemeta', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Sets the page attachment for request_page
	 *
	 * @param int $website_page_id
	 * @param string $key
	 * @param string $value
	 * @return bool
	 */
	 public function set_page_attachment( $website_page_id, $key, $value ) {
		 $request_page_id = $this->exists( $website_page_id );
		 
		 // A request needs to exist prior to this
		 if( empty( $request_page_id ) )
			return false;
		
		// @Fix drop request_attachment_id, should just be a unique key on `request_page_id` and `key`
		$this->db->prepare( 'INSERT INTO `request_attachments` ( `request_page_id`, `key`, `value` ) VALUES ( ?, ?, ? ) ON DUPLICATE KEY UPDATE `value` = ?', 'isss', $request_page_id, $key, $value, $value )->query( '' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to set page attachment', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
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
		if( $this->db->errno() ) {
			$this->err( 'Failed to get website files.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_files;
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