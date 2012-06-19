<?php
/**
 * Handles email marketing functions
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Emails extends Base_Class {	
	/**
	 * Creates new Database instance
	 *
	 * @param bool $instantiate (optional|true) If you want mailchimp instantiated
	 * @return void
	 */
	public function __construct( $instantiate = true ) {
		// load database library into $this->db (can be omitted if not required)
		if ( !parent::__construct() ) return false;
		
		// Instantiate MailChimp API
		if ( $instantiate && !isset( $this->mc ) )
			library('MCAPI');
			$this->mc = new MCAPI( '54c6400139c4f457efb941516f903b98-us1' );
	}

	/**
	 * Update scheduled emails
	 *
	 * This function assumes MailChimp will send the email at the right time.
	 * We simply mark it as sent when it has past the date it is SUPPOSED to send
	 *
	 * @return bool
	 */
	public function update_scheduled_emails() {
		// Update status to sent
		$this->db->query( "UPDATE `email_messages` SET `status` = 2 WHERE `status` = 1 AND `date_sent` < NOW()" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list users.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Synchronize email lists
	 * 
	 * @return bool
	 */
	public function synchronize_email_lists() {
		$this->remove_bad_emails();
		$this->update_email_lists();	
	}
	
	/**
	 * Removes unsubscribed addresses
	 *
	 * @return void
	 */
	private function remove_bad_emails() {
		// Get the website lists and mc_list_ids
		$mc_list_ids = $this->db->get_results( "SELECT `website_id`, `mc_list_id` FROM `websites` WHERE `mc_list_id` <> '0' AND `email_marketing` <> 0", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get emails for removal.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Assign the key
		$mc_list_ids = ar::assign_key( $mc_list_ids, 'website_id', true );
		
		// Go through all the websites
		foreach ( $mc_list_ids as $website_id => $mc_list_id ) {
			// Get the unsubscribers since the last day
			$unsubscribers = $this->mc->listMembers( $mc_list_id, 'unsubscribed', date( 'Y-m-d H:i:s', time() - 86800 ) ); 
			
			// Error Handling
			if ( $this->mc->errorCode )
				$this->_err( "Unable to get Unsubscribed Members\n\nList_id: $mc_list_id\nCode: " . $this->mc->errorCode . "\nError Message: " . $this->mc->errorMessage . "\nMembers returned: " . count( $unsubscribers ), __LINE__, __METHOD__ );
			
			$emails = ''; 
			
			if ( is_array( $unsubscribers ) )
			foreach ( $unsubscribers as $unsub ) {
				if ( !empty( $emails ) )
					$emails .= ',';
				
				$emails .= "'" . $unsub['email'] . "'";
			}
			
			// Mark the users as unsubscribed
			if ( !empty( $emails ) ) {
				$this->db->query( "UPDATE `emails` SET `status` = 0 WHERE `website_id` = $website_id AND `email` IN ($emails)" );
			
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to remove unsubscribed emails.', __LINE__, __METHOD__ );
					return false;
				}
			}
			
			// Get the cleaned for the last day
			$cleaned = $this->mc->listMembers( $mc_list_id, 'cleaned', date( 'Y-m-d H:i:s', time() - 86700 ) );
			
			// Error Handling
			if ( $this->mc->errorCode )
				$this->_err( "Unable to get Cleaned Members\n\nList_id: $mc_list_id\nCode: " . $this->mc->errorCode . "\nError Message: " . $this->mc->errorMessage . "\nMembers returned: " . count( $cleaned ), __LINE__, __METHOD__ );
			
			$emails = '';
			
			if ( is_array( $cleaned ) )
			foreach ( $cleaned as $clean ) {
				if ( !empty( $emails ) )
					$emails .= ',';
				
				$emails .= "'" . $clean['email'] . "'";
			}
			
			// Mark the users as cleaned
			if ( !empty( $emails ) ) {
				$this->db->query( "UPDATE `emails` SET `status` = 2 WHERE `website_id` = $website_id AND `email` IN ($emails)" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to remove cleaned emails.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Update email lists
	 *
	 * @return void
	 */
	private function update_email_lists() {
		$email_results = $this->db->get_results( "SELECT d.`mc_list_id`, d.`website_id`, a.`email`, a.`email_id`, a.`name`, GROUP_CONCAT( c.`name` ) AS interests FROM `emails` AS a INNER JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) INNER JOIN `email_lists` AS c ON ( b.`email_list_id` = c.`email_list_id` ) INNER JOIN `websites` AS d ON ( c.`website_id` = d.`website_id` ) WHERE a.`status` = 1 AND ( a.`date_synced` = '0000-00-00 00:00:00' OR a.`timestamp` > a.`date_synced` ) AND d.`email_marketing` <> 0 GROUP BY c.`website_id`, a.`email`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email results.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Create array
		$email_lists = array();
		
		// We know an array exists or we would have aborted above
		foreach ( $email_results as $er ) {
			if ( !$er['mc_list_id'] ) {
				// Do error stuff
				//$this->_err( 'There was no MailChimp List ID.', __LINE__, __METHOD__ );
				continue;
			}
			
			$email_lists[$er['mc_list_id']][$er['email_id']] = array( 
				'EMAIL' => $er['email'],
				'EMAIL_TYPE' => 'html',
				'FNAME' => $er['name'],
				'INTERESTS' => $er['interests']
			);
			
			$email_interests[$er['mc_list_id']] = ( isset( $email_interests[$er['mc_list_id']] ) ) ? array_merge( $email_interests[$er['mc_list_id']], explode( ',', $er['interests'] ) ) : explode( ',', $er['interests'] );
			$email_interests[$er['mc_list_id']] = array_unique( $email_interests[$er['mc_list_id']] );
		}
		
		// Create array to hold email ids
		$synced_email_ids = array();
		
		if ( is_array( $email_lists ) )
		foreach ( $email_lists as $mc_list_id => $emails ) {
			$interests = array_unique( $email_interests[$mc_list_id] );
			
			$groups_result = $this->mc->listInterestGroups( $mc_list_id );
			
			// Error Handling
			if ( $this->mc->errorCode )
				$this->_err( "Unable to get Interest Groups\n\nList_id: $mc_list_id\nCode: " . $this->mc->errorCode . "\nError Message: " . $this->mc->errorMessage, __LINE__, __METHOD__ );
						
			foreach ( $interests as $i ) {
				if ( !in_array( $i, $groups_result['groups'] ) ) {
					$this->mc->listInterestGroupAdd( $mc_list_id, $i );
					
					// Error Handling
					if ( $this->mc->errorCode )
						$this->_err( "Unable to add Interest Group\n\nList_id: $mc_list_id\nInterest Group: $i\nCode: " . $this->mc->errorCode . "\nError Message: " . $this->mc->errorMessage, __LINE__, __METHOD__ );
				}
			}
			
			// list_id, batch of emails, require double optin, update existing users, replace interests
			$vals = $this->mc->listBatchSubscribe( $mc_list_id, $emails, false, true, true );
						
			if ( $this->mc->errorCode ) {
				$this->_err( "Unable to get Batch Subscribe\n\nList_id: $mc_list_id\nCode: " . $this->mc->errorCode . "\nError Message: " . $this->mc->errorMessage, __LINE__, __METHOD__ );
			} else {
				// Handle errors if there were any
				if ( $vals['error_count'] > 0 ) {
					$errors = '';
					
					foreach ( $vals['errors'] as $val ) {
						$errors .= "Email: " . $val['email'] . "\nCode: " . $val['code'] . "\nError Message: " . $val['message'] . "\n\n";
					}
					
					$this->_err( "List_id: $mc_list_id\n" . $vals['error_count'] . ' out of ' . $vals['error_count'] + $vals['success_count'] . " emails were unabled to be subscribed\n\n$errors", __LINE__, __METHOD__ );
				}
				
				$synced_email_ids = array_merge( $synced_email_ids, array_keys( $emails ) );
			}
		}
		
		$synced_email_count = count( $synced_email_ids );
		if ( $synced_email_count > 0 ) {
			// Mark these emails as synced
			$this->db->query( 'UPDATE `emails` SET `date_synced` = NOW() WHERE `email_id` IN(' . implode( ',', $synced_email_ids ) . ')' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to set emails as synced.', __LINE__, __METHOD__ );
				return false;
			}
		}
	}
	
	/**
	 * Adds an template
	 *
	 * @param int $website_id
	 * @param string $view_product_button
	 * @param string $product_color
	 * @param string $price_color
	 * @param string $default_template
	 * @param string $product_template
	 * @param string $template_image
	 * @param string $template_thumbnail_image
	 * @return bool
	 */
	public function add_template( $website_id, $view_product_button, $product_color, $price_color, $default_template, $product_template, $template_image, $template_thumbnail_image ) {
		// Add the settings
		$this->add_settings( array( 'view-product-button' => $view_product_button, 'product-color' => $product_color, 'product-price-color' => $price_color ), $website_id );
		
		// Update default template
		$this->db->prepare( "UPDATE `email_template_associations` AS b LEFT JOIN `email_templates` AS a ON ( a.`email_template_id` = b.`email_template_id` ) SET a.`template` = ?, a.`image` = ?, a.`thumbnail` = ? WHERE b.`object_id` = ? AND b.`type` = 'website' AND a.`type` = 'default'", 'sssi', stripslashes( $default_template ), $template_image, $template_thumbnail_image, $website_id )->query('');
		
		// Handle any error
		if ( mysql_errno() ) {
			$this->_err( 'Email Marketing', 'Failed to update default template', "Website ID: $website_id", __LINE__, __METHOD__ );
			return false;
		}
		
		// Insert new template
		$this->db->insert( 'email_templates', array( 'name' => 'Product Offer', 'template' => stripslashes( $product_template ), 'image' => $template_image, 'thumbnail' => $template_thumbnail_image, 'type' => 'product', 'date_created' => date( "Y-m-d H:i:s", time() ) ), 'ssssss' );
		
		// Assign the template_id
		$email_template_id = $this->db->insert_id;
				
		// Handle any error
		if ( mysql_errno() ) {
			$this->_err( 'Email Marketing Model', 'Failed to insert product offer email template', "Website ID: $website_id", __LINE__, __METHOD__ );
			return false;
		}

		// Insert association
		$this->db->insert( 'email_template_associations', array( 'email_template_id' => $email_template_id, 'object_id' => $website_id, 'type' => 'website' ), 'iis' );
		
		// Handle any error
		if ( mysql_errno() ) {
			$this->_err( 'Email Marketing Model', 'Failed to insert email template association', "Failed to insert email template association", __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Adds email settings
	 *
	 * @param array $settings the settings in an associative arre key => value
	 * @param int $website_id
	 * @return unknown
	 */
	public function add_settings( $settings, $website_id ) {
		$keys = array_keys( $settings );
		$sql_keys = '';
		foreach ( $keys as $key ) {
			if ( !empty( $sql_keys ) )
				$sql_keys .= ',';
			
			$sql_keys .= "'" . $key . "'";
		}
		
		// First delete the settings
		$this->db->prepare( 'DELETE FROM `email_settings` WHERE `key` IN (?) AND `website_id` = ?', 'si', $sql_keys, $websites_id );
		
		// Handle errors
		if ( mysql_errno() ) {
			$this->_err( 'Failed to delete email settings', __LINE__, __METHOD__ );
			return false;
		}
		
		$setting_values = '';
		
		// Setup SQL to insert values
		foreach ( $settings as $key => $value ) {
			$this->db->insert( 'email_settings', array( 'website_id' => $website_id, 'key' => $key, 'value' => $value ), 'iss' );
			// Handle errors
			if ( mysql_errno() ) {
				$this->_err( 'Failed to set email settings', __LINE__, __METHOD__ );
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