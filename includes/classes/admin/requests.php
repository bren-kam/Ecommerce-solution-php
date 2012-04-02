<?php
/**
 * Handles all the requests
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Requests extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get all information of the requests
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_requests( $where, $order_by, $limit ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND c.`company_id` = ' . $user['company_id'] : $where . ' AND c.`company_id` = ' . $user['company_id'];
		
		// Get the requests
		$requests = $this->db->get_results( "SELECT a.`request_id`, a.`type`, a.`date_created`, b.`title`, b.`live`, c.`contact_name`, IF( b.`live`, DATEDIFF( a.`date_due`, CURDATE() ), DATEDIFF( DATE_ADD( b.`date_created`, INTERVAL 30 DAY ), CURDATE() ) ) AS days_left, DATE( a.`date_updated` ) AS date_updated FROM `requests` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) INNER JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) WHERE 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list requests.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $requests;
	}
	
	/**
	 * Count all the requests
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_requests( $where ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND c.`company_id` = ' . $user['company_id'] : $where . ' AND c.`company_id` = ' . $user['company_id'];
		
		// Get the request count
		$request_count = $this->db->get_var( "SELECT COUNT( a.`request_id` ) FROM `requests` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) INNER JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count requests.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $request_count;
	}
	
	/**
	 * Gets a single request
	 *
	 * @param int $request_id
	 * @return array
	 */
	public function get( $request_id, $original = true ) {
		// Typecast
		$request_id = (int) $request_id;
		
		// Get the request data
		$request = $this->db->get_row( "SELECT a.`request_id`, b.`website_id`, b.`title`, c.`contact_name`, a.`request`, a.`type`, a.`status`, a.`date_created`, DATE( a.`date_updated` ) AS date_updated FROM `requests` AS a LEFT JOIN `websites` AS b ON (a.`website_id` = b.`website_id`) LEFT JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) WHERE a.`request_id` = $request_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get primary request data.', __LINE__, __METHOD__ );
			return false;
		}
		
		switch ( $request['type'] ) {
			case 'Header Update': 
				$w = new Websites;
				$request['web'] = $w->get_website( $request['website_id'] );
			break;
			
			case 'Page Update': {
				// Get the page
				$request['page'] = $this->db->get_row( "SELECT a.`request_page_id`, a.`website_page_id`, a.`request_id`, b.`slug`, b.`title`, a.`content`, a.`meta_title`, a.`meta_description`, a.`meta_keywords` FROM `request_pages` AS a LEFT JOIN `website_pages` AS b ON (a.`website_page_id` = b.`website_page_id`) WHERE a.`request_id` = $request_id", ARRAY_A );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to get request - page.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Get the original page
				if ( $original && $request['page'] ) {
					$request['original_page'] = $this->db->get_row( "SELECT `content`, `meta_title`, `meta_description`, `meta_keywords` FROM `website_pages` WHERE `website_page_id` = " . $request['page']['website_page_id'], ARRAY_A );
				
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to get request - original page.', __LINE__, __METHOD__ );
						return false;
					}
				}
				
				if ( $request['page'] ) {
					// Get the meta data
					$meta = $this->db->get_results( "SELECT `request_pagemeta_id`, `key`, `value` FROM `request_pagemeta` WHERE `request_page_id` = " . $request['page']['request_page_id'], ARRAY_A );
					
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to get request - meta data.', __LINE__, __METHOD__ );
						return false;
					}
				}
				
				if ( is_array( $meta ) )
				foreach ( $meta as $m ) {
					$request['meta'][$m['key']] = $m;
				}
	
				// Get the original meta data
				if ( $original && $request['page'] ) {
					$original_meta = $this->db->get_results( "SELECT `key`, `value` FROM `website_pagemeta` WHERE `website_page_id` = " . $request['page']['website_page_id'], ARRAY_A );
					
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to get request - original meta data.', __LINE__, __METHOD__ );
						return false;
					}
					
					if ( is_array( $original_meta ) )
					foreach ( $original_meta as $om ) {
						$request['original_meta'][$om['key']] = $om;
					}
				}
				
				if ( $request['page'] ) {
					// Get the attachments
					$request['attachments'] = $this->db->get_results( "SELECT `request_attachment_id`, `key`, `value` FROM `request_attachments` WHERE `request_page_id` = " . $request['page']['request_page_id'], ARRAY_A );
					
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to get request attachments.', __LINE__, __METHOD__ );
						return false;
					}
					
					// Get the original attachments
					$request['original_attachments'] = $this->db->query( "SELECT `key`, `value` FROM `website_attachments` WHERE `website_page_id` = " . $request['page']['website_page_id'], ARRAY_A );
				
					// Handle any error
					if ( $this->db->errno() ) {
						$this->err( 'Failed to get request original attachments.', __LINE__, __METHOD__ );
						return false;
					}
				}
			}
			break;
		}
		
		$request['messages'] = $this->get_messages( $request_id );
		
		return $request;
	}
	
	/**
	 * Get's all the messages relating to a request_id
	 *
	 * @param int $object id either request_id or user_id
	 * @param string $type (optional) the type of object being passed
	 * @return array
	 */
	public function get_messages( $object_id, $type = 'request' ) {
		$where = ( 'request' == $type ) ? ' WHERE a.`request_id` = ' . (int) $object_id : ' WHERE a.`user_id` = ' . (int) $object_id;
		
		$messages = $this->db->get_results( "SELECT a.`message`, b.`contact_name`, DATE_FORMAT( a.`date_created`, GET_FORMAT( DATE, 'USA' ) ) AS date_created, TIME( a.`date_created` ) AS time FROM `request_messages` AS a INNER JOIN `users` AS b ON ( a.`user_id` = b.`user_id` )" . $where, ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get messages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $messages;
	}
	
	/**
	 * Approve a request
	 *
	 * @param int $request_id
	 */
	public function approve( $request_id ) {
		if ( 0 == $request_id )
			return false;
		
		// Get all the request information
		$request = $this->get( $request_id, false );
				
		switch ( $request['type'] ) {
			case 'Header Update':
				$w = new Websites;
				$web = $w->get_website( $request['website_id'] );
				
				$request_data = unserialize( html_entity_decode( $request['request'] ) );
				
				$logo = $web['logo'];
								
				if ( isset( $request_data['image'] ) && $ftp = new FTP( $request['website_id'] ) ) {
					
					// We're connected
					$url_data = parse_url( $request_data['image'] );
										
					// Get our local directory
					$local_file_path = '/home/develop4/public_html/account' . $url_data['path'];
					
					if ( is_file( $local_file_path ) ) {
						$logo = 'logo.' . format::file_extension( $local_file_path );
						
						// Get the path info
						$pathinfo = pathinfo( $local_file_path );

						// Add it their site
						if ( !$ftp->add( $local_file_path, 'images/', $logo ) )
							return false;
					}
				}				

				// Set the website data, if successful, delete the local file
				if ( $w->update_header( $logo, $request_data['phone_number'], $request['website_id'] ) ) {
					if ( isset( $local_file_path ) && is_file( $local_file_path ) )
						unlink( $local_file_path );
				}
			break;
			
			case 'Page Update':
				// Get the two models
				$p = new Pages;
				$a = new Archives;
				
				$archive_page_id = $a->add_page( $request['page']['website_page_id'], $request['request_id'], $request['page'] );
				if ( !$archive_page_id )
					return false;
				
				if ( !$p->update( $request['page'], $request['page']['website_page_id'] ) )
					return false;
				
				if ( !empty( $request['meta'] ) ) {
					if ( !$a->add_pagemeta( $archive_page_id, $request['meta'] ) )
						return false;
					
					if ( !$p->set_pagemeta( $request['meta'], $request['page']['website_page_id'] ) )
						return false;
				}
				
				if ( !empty( $request['attachments'] ) ) {
					if ( $ftp = new FTP( $request['website_id'] ) ) {
						
						// We're connected
						foreach ( $request['attachments'] as $attachment ) {
							$url_data = parse_url( $attachment['value'] );
							
							// Get our local directory
							$local_file_path = '/home/develop4/public_html/account' . $url_data['path'];
							
							if ( !is_file( $local_file_path ) )
								continue;
							
							$file_extension = format::file_extension( $attachment['value'] );
							
							switch ( $file_extension ) {
								case 'swf':
								case 'flv':
								case 'mp4':
								case 'f4v':
									$remote_directory = 'video/';
								break;
								
								case 'pdf':
									$remote_directory = 'pdf/';
								break;
								
								default:
									$remote_directory = 'images/';
								break;
							}
							
							// Get the path info
							$pathinfo = pathinfo( $local_file_path );
							
							// Add it their site
							if ( !$ftp->add( $local_file_path, $remote_directory ) )
								return false;
							
							// Set the website data, if successful, delete the local file
							if ( $p->set_page_attachment( $request['page']['website_page_id'], $attachment['key'], '/custom/uploads/' . $remote_directory . $pathinfo['basename'] ) )
								unlink( $local_file_path );
						}
					} else {
						return false;
					}
					
				}
				break;
		}
		
		$this->db->update( 'requests', array( 'status' => 1 ), array( 'request_id' => $request_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to approve request.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Disapprove a request
	 *
	 * @param int $request_id
	 * @return bool
	 */
	public function disapprove( $request_id ) {
		if ( 0 == $request_id )
			return false;

		$this->db->update( 'requests', array( 'status' => 2 ), array( 'request_id' => $request_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to disapprove request.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Send a message
	 *
	 * @param int $request_id
	 * @param string $message
	 */
	public function send_message( $request_id, $message ) {
		global $user;
		
		$to = $this->get_user_email( $request_id );
		$message = '<p>' . nl2br( $message ) . '</p>';
		
		$this->db->insert( 'request_messages', array( 'request_id' => $request_id, 'user_id' => $user['user_id'], 'message' => $message, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
    	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to send message.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Send message
		fn::mail( $to, _('Message About Your Recent Website Change Request'), $message, '', '', false );
		
		return true;
	}
	
	/**
	 * Get's a user's email
	 *
	 * @param int $request_id
	 * @return string
	 */
	public function get_user_email( $request_id ) {
		$email = $this->db->get_var( "SELECT CONCAT( a.`contact_name`, '<', a.`email`, '>' ) FROM `users` AS a LEFT JOIN `websites` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `requests` AS c ON ( b.`website_id` = c.`website_id` ) WHERE c.`request_id` = " . (int) $request_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get user email.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email;
	}
	
	/**
	 * Deletes a request and everything associated with it
	 *
	 * @param int $request_id
	 * @return bool
	 */
	public function delete( $request_id ) {
		$request_id = (int) $request_id;
		
		// Delete from request attachments
		$this->db->query( "DELETE FROM `request_attachments` WHERE `request_page_id` IN ( SELECT `request_page_id` FROM `request_pages` WHERE `request_id` = $request_id )" );
	
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete request attachments', __LINE__, __METHOD__ );
			return false;
		}

		// Delete from request pagemeta
		$this->db->query( "DELETE FROM `request_pagemeta` WHERE `request_page_id` IN ( SELECT `request_page_id` FROM `request_pages` WHERE `request_id` = $request_id )" );
	
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete request pagemeta', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete Request Pages
		$this->db->query( "DELETE FROM `request_pages` WHERE `request_id` = $request_id" );

		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete request pages', __LINE__, __METHOD__ );
			return false;
		}

		// Delete Request Messages
		$this->db->query( "DELETE FROM `request_messages` WHERE `request_id` = $request_id" );
		
		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete request messages', __LINE__, __METHOD__ );
			return false;
		}

		// Delete Request
		$this->db->query( "DELETE FROM `requests` WHERE `request_id` = $request_id" );
		
 		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to delete request', __LINE__, __METHOD__ );
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