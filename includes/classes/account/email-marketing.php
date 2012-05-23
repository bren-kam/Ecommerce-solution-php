<?php
/**
 * Handles all the Email Marketing
 *
 * @Fix need to upgrade MailChimp API from 1.2 (deprecated) > 1.3
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Email_Marketing extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/***** DASHBOARD *****/
	/**
	 * Dashboard Messages
	 * 
	 * @param int $website_id
	 * @return array
	 */
	public function dashboard_messages( $website_id ) {
		$emails = $this->db->prepare( 'SELECT `mc_campaign_id`, `subject` FROM `email_messages` WHERE `website_id` = ? AND `status` = 2 ORDER BY `date_sent` DESC LIMIT 5', 'i', $website_id )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get dashboard messages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $emails;
	}
	
	/**
	 * Dashboard Subscribers
	 * 
	 * @param int $website_id
	 * @return array
	 */
	public function dashboard_subscribers( $website_id ) {
		$subscribers = $this->db->prepare( 'SELECT `email` FROM `emails` WHERE `website_id` = ? AND `status` = 1 ORDER BY `date_created` DESC LIMIT 5', 'i', $website_id )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get dashboard subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $subscribers;
	}
	
	/**
	 * Bar Chart data
	 *
	 * @param array $email
	 * @return string (json encoded)
	 */
	public function bar_chart( $email ) {
		$max = max( array(
			(int) $email['emails_sent'],
			(int) $email['opens'],
			(int) $email['clicks'],
			(int) $email['forwards'],
			$email['soft_bounces'] + $email['hard_bounces'],
			(int) $email['unsubscribes']
		) );
		
		// Create the bar chart
		$bar_chart = array(
			'elements' => array( 
				array(
					'type' => 'bar_glass',
					'colour' => '#FFA900',
					'on-show' => array( 
						'type' => 'grow-up',
						'cascade' => 1,
						'delay' => 0.5
					),
					'values' => array(
						array( 
							'top' => (int) $email['emails_sent'],
							'tip' => '#val# Emails Sent'
						),
						array( 
							  'top' => (int) $email['opens'],
							  'tip' => '#val# Opens'
						),
						array( 
							  'top' => (int) $email['clicks'],
							  'tip' => '#val# Clicks'
						),
						array( 
							  'top' => (int) $email['forwards'],
							  'tip' => '#val# Forwards'
						),
						array(
							  'top' => $email['soft_bounces'] + $email['hard_bounces'],
							  'tip' => '#val# Bounces'
						),
						array(
							  'top' => (int) $email['unsubscribes'],
							  'tip' => '#val# Unsubscribes'
						)
					),
					'tip' => '#val#'
				)
			),
			'x_axis' => array(
				'labels' => array( 
					'labels' => array(
						'Emails Sent',
						'Opens',
						'Clicks',
						'Forwards',
						'Bounces',
						'Unsubscribes'
					),
					'colour' => '#545454'
				),
				'colour' => '#545454',
				'grid-colour' => '#D9D9D9'
			),
			'y_axis' => array(
				'min' => 0,
				'max' => $max,
				'steps' => ceil( $max / 6 ),
				'colour' => '#545454',
				'grid-colour' => '#D9D9D9'
			),
			'bg_colour' => '#FFFFFF'
		);
		
		return json_encode( $bar_chart );
	}
	
	/***** SUBSCRIBERS *****/
	
	/**
	 * List Subscribers
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_subscribers( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$subscribers = $this->db->get_results( "SELECT DISTINCT a.`email_id`, a.`name`, a.`email`, a.`phone`, IF( 1 = a.`status`, UNIX_TIMESTAMP( a.`date_created` ), UNIX_TIMESTAMP( a.`timestamp` ) ) AS date FROM `emails` AS a WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $subscribers;
	}
	
	/**
	 * Count Subscribers
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_subscribers( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`email_id` ) FROM `emails` AS a WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count email subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * List Subscribers by email list id
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_subscribers_by_email_list_id( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$subscribers = $this->db->get_results( "SELECT DISTINCT a.`email_id`, a.`name`, a.`email`, a.`phone`, IF( 1 = a.`status`, UNIX_TIMESTAMP( a.`date_created` ), UNIX_TIMESTAMP( a.`timestamp` ) ) AS date FROM `emails` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $subscribers;
	}
	
	/**
	 * Count Subscribers by email list id
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_subscribers_by_email_list_id( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`email_id` ) FROM `emails` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count email subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}

    /**
	 * Export subscribers
	 *
	 * @param int $email_list_id
	 * @return array
	 */
	public function export_subscribers( $email_list_id = 0 ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $email_list_id = (int) $email_list_id;

        if ( 0 == $email_list_id ) {
            // Grab all subscribers
		    $subscribers = $this->db->get_results( "SELECT `name`, `email`, `phone` FROM `emails` WHERE `website_id` = $website_id AND `status` = 1 ORDER BY `email` ASC", ARRAY_A );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->_err( 'Failed to export subscribers.', __LINE__, __METHOD__ );
                return false;
            }
        } else {
            // Grab the subscribers for a specific email list
		    $subscribers = $this->db->get_results( "SELECT a.`name`, a.`email`, a.`phone` FROM `emails` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) WHERE a.`website_id` = $website_id AND a.`status` = 1 AND b.`email_list_id` = $email_list_id GROUP BY a.`email_id` ORDER BY a.`email` ASC", ARRAY_A );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->_err( 'Failed to export subscribers.', __LINE__, __METHOD__ );
                return false;
            }
        }

		return $subscribers;
	}
	
	/**
	 * Get Email
	 *
	 * @param int $email_id
	 * @return array
	 */
	public function get_email( $email_id ) {
		global $user;
		
		// Typecast
		$email_id = (int) $email_id;
		
		$email = $this->db->get_row( 'SELECT `name`, `email`, `phone` FROM `emails` WHERE `email_id` = ' . $email_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email.', __LINE__, __METHOD__ );
			return false;
		}
		
		$email['email_lists'] = $this->db->get_col( 'SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = ' . $email_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email;
	}
	
	/**
	 * Checks an email already exits
	 *
	 * @param string $email
	 * @return array
	 */
	public function email_exists( $email ){
		$email = $this->db->prepare( 'SELECT a.`email_id`, a.`status` FROM `emails` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) RIGHT JOIN `email_lists` AS c ON ( b.`email_list_id` = c.`email_list_id` ) WHERE a.`email` = ? AND c.`website_id` = ?', 'si', $email, $user['website']['website_id'] )->get_var('');

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to check if email exists.', __LINE__, __METHOD__ );
			return false;
		}

		return $email;
	}
	
	/**
	 * Unsubscribes a single email_id
	 * @param int $email_id
	 * @param string $email
	 * @return bool
	 */
	public function unsubscribe( $email_id, $email ){
		// Make sure email is safe
		if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) 
			return false;
		
		global $user;
		
		// First, get mailchimp and unsubscribe the email - that way, emails that fail to unsubscribe they aren't deleted from our DB
		$mc = $this->mailchimp_instance();
		$success = $mc->listUnsubscribe( $user['website']['mc_list_id'], $email );
		
		// @Fix - we are assuming that they are not on the list if it failed. If we start getting people complaining saying that they 
		// unsubscribed but it didn't work, then we need to fix this.
			// @Fix - A check needs to be done to see if it was added
			//if( !$success ) 
				//return false;

        // @Fix this isn't being used
		// Get list_id for this email
		$email_lists = $this->db->get_col( "SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = " . (int) $email_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Remove email from lists
		$this->db->query( 'DELETE FROM `email_associations` WHERE `email_id` = ' . (int) $email_id . ' AND `email_list_id` IN ( SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = ' . (int) $user['website']['website_id'] . ' )' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to remove email from list.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Set the email's status to "unsubscribed"
		$this->db->update( 'emails', array( 'status' => 0 ), array( 'email_id' => $email_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to unsubscribe email.', __LINE__, __METHOD__ );
			return false;
		}
				
		return true;
	}
	
	/**
	 * Create
	 *
	 * @param string $email
	 * @param string $name
	 * @param string $phone
	 * @return int
	 */
	public function create_email( $email, $name, $phone ) {
		global $user;
		
		$this->db->insert( 'emails', array( 'website_id' => $user['website']['website_id'], 'name' => $name, 'email' => $email, 'phone' => $phone, 'status' => 1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isssis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create email.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update an email subscription
	 *
	 * @param int $email_id
	 * @param string $email
	 * @param string $name
	 * @param string $phone
	 * @return bool
	 */
	public function update_email( $email_id, $email, $name, $phone ) {
		$this->db->update( 'emails', array( 'name' => $name, 'email' => $email, 'phone' => $phone ), array( 'email_id' => $email_id ), 'sss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update email.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update email list subscriptions
	 * 
	 * @param int $email_id
	 * @param array $email_lists 
	 * @return bool
	 */
	public function update_email_lists_subscription( $email_id, $email_lists ) {
		// Typecast
		$email_id = (int) $email_id;
		
		$this->db->query( "DELETE FROM `email_associations` WHERE `email_id` = $email_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete email associations.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add new values if they exist
		if ( is_array( $email_lists ) ) {
			$values = '';
			
			foreach ( $email_lists as $el ){
				if ( !empty( $values ) )
					$values .= ',';
				
				$values .= "( $email_id, " . (int) $el . ')';
			}
			
			$this->db->query( "INSERT INTO `email_associations` ( `email_id`, `email_list_id` )  VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to insert email associations.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Import emails based on an array
	 * 
	 * @param array $email_array
	 * @return bool
	 */
	public function import_emails( $email_array ) {
		global $user;
		
		// Typecast
		$user['website']['website_id'] = (int) $user['website']['website_id'];
		
		// Delete already imported emails
		$this->delete_imported_emails();
		
		// Select all the unsubscribed emails they already have
		$unsubscribed_emails = $this->db->get_col( 'SELECT `email` FROM `emails` WHERE `status` = 0 AND `website_id` = ' . (int) $user['website']['website_id'], ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get unsubscribed emails.', __LINE__, __METHOD__ );
			return false;
		}
				
		// Create string to insert new emails
		foreach ( $email_array as $email ) {
			// Make sure they haven't been unsubscribed
			if ( in_array( $email['email'], $unsubscribed_emails ) )
				continue;
			
			$values[] = '( ' . $user['website']['website_id'] . ", '" . $this->db->escape( $email['email'] ) . "', '" . $this->db->escape( $email['name'] ) . "', NOW() )";
		}
		
		// Insert 500 at a time
		for ( $i = 0; $i < count( $values ); $i += 500 ) {
			// Get the last 500
			$email_array_slice = array_slice( $values, $i, 500 );
			
			// Insert 500
			$this->db->query( "INSERT INTO `email_import_emails` ( `website_id`, `email`, `name`, `date_created` ) VALUES" . implode( ',', $email_array_slice ) );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to import emails.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Copies imported emails to email lists
	 * 
	 * @param array $email_lists
	 * @return bool
	 */
	public function complete_import( $email_lists ) {
		global $user;
		
		// Typecast
		$user['website']['website_id'] = (int) $user['website']['website_id'];
		
		// @Fix remove the subquery
		// @Fix need a way to remove these subscribers
		// Transfer new emails to emails table
		$this->db->query( 'INSERT INTO `emails` ( `website_id`, `email`, `name`, `date_created` ) SELECT `website_id`, `email`, `name`, NOW() FROM `email_import_emails` WHERE `website_id` = ' . $user['website']['website_id'] . ' AND `email` NOT IN ( SELECT `email` FROM `emails` WHERE `website_id` = ' . $user['website']['website_id'] . ' )' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to transfer imported subscribers to new table of subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		
		// Add the associations for each list
		foreach ( $email_lists as $el_id ) {
			$this->db->query( 'INSERT INTO `email_associations` ( `email_id`, `email_list_id` ) SELECT a.`email_id`, ' . (int) $el_id . ' FROM `emails` AS a INNER JOIN `email_import_emails` AS b ON ( a.`email` = b.`email` ) WHERE a.`website_id` = ' . $user['website']['website_id'] );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to add email assocations.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Delete the imported emails
		$this->delete_imported_emails();
		
		return true;
	}
	
	/**
	 * Delete imported emails
	 *
	 * @return bool
	 */
	private function delete_imported_emails() {
		global $user;
		
		// Delete all previous emails in the table for this website
		$this->db->query( 'DELETE FROM `email_import_emails` WHERE `website_id` = ' . (int) $user['website']['website_id'] );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete imported emails.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets all the templates based on a website_id
	 *
	 * @param string $type
	 * @return array
	 */
	public function get_templates( $type ) {
		global $user;
		
		switch ( $type ) {
			case 'offer':
				// Type juggling
				$company_id = (int) $user['company_id'];
				
				$where = "b.`object_id` = $company_id AND b.`type` = 'offer' AND a.`type` = 'offer'";
			break;
			
			case 'product':
				// Type juggling
				$website_id = (int) $user['website']['website_id'];
				
				$where = "b.`object_id` = $website_id AND b.`type` = 'website' AND a.`type` = 'product'";
			break;
			
			case 'custom':
				// Type juggling
				$website_id = (int) $user['website']['website_id'];
				
				$where = "b.`object_id` = $website_id AND b.`type` = 'website' AND ( a.`type` = 'custom' OR a.`type` = 'default' )";
			break;
		}
		
		$templates = $this->db->get_results( "SELECT a.`email_template_id`, a.`name`, REPLACE( a.`thumbnail`, 'media/', '' ) AS thumbnail, REPLACE( a.`image`, 'media/', '' ) AS image FROM `email_templates` AS a LEFT JOIN `email_template_associations` AS b ON ( a.`email_template_id` = b.`email_template_id` ) WHERE $where ORDER BY a.`name`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get templates.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $templates;
	}
	
	/**
	 * Sends a test email message (using MailChimp)
	 *
	 * @param string $email
	 * @param int $email_message_id
	 * @return Response
	 */
	public function test_message( $email, $email_message_id ) {
		// Get mailchimp
		$mc = $this->mailchimp_instance();
		
		// Get the email message
		$em = $this->get_email_message( $email_message_id );
		
		// If needed, create a new campaign
		$response = ( 0 == $em['mc_campaign_id'] ) ? $this->mc_create_campaign( $em ) : $em['mc_campaign_id'];
		
		if ( !$response->success() )
			return $response;


        $mc_campaign_id = $response->get('mc_campaign_id');
		
		// Send a test
		$mc->campaignSendTest( $mc_campaign_id, array( $email ) );
		
		// Handle errors
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to send Campaign Test email\n\nCampaign ID: " . $mc_campaign_id . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return new Response( false, $this->_mc_message( $mc->errorCode, $mc->errorMessage ) );
		}
		
		return new Response( true );
	}
	
	/**
	 * Creates a MailChimp Campaign Email
	 *
	 * @param array $email_message
	 * @return Response
	 */
	public function mc_create_campaign( $email_message ) {
		global $user;
		
		// Get the mailchimp instance
		$mc = $this->mailchimp_instance();
		
		// Make the email lists work
		$this->synchronize_email_list();
		
		// Check to make sure all the interest groups exist
		$interest_groups = $mc->listInterestGroups( $user['website']['mc_list_id'] );
		
		// Handle Errors
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to get Interest Groups\n\nList ID: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return new Response( false, $this->_mc_message( $mc->errorCode, $mc->errorMessage ) );
		}
		
		foreach ( $email_message['email_lists'] as $el ) {
			if ( in_array( $el, $interest_groups['groups'] ) )
				continue;
			
			$mc->listInterestGroupAdd( $user['website']['mc_list_id'], $el );
			
			if ( $mc->errorCode ) {
				$this->_err( "MailChimp: Unable to add Interest Groups\n\nList ID: " . $user['website']['mc_list_id'] . "\nInterest Group: " . $el . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
				return new Response( false, $this->_mc_message( $mc->errorCode, $mc->errorMessage ) );
			}
		}
		
		$segmentation_options = array( 
			'match' => 'any', 
			'conditions' => array( 
				array( 
					  'field' => 'interests', 
					  'op' => 'one', 
					  'value' => implode( ',', $email_message['email_lists'] ) 
				)
			)
		);
		
		// Handle Errors
		if ( !$mc->campaignSegmentTest( $user['website']['mc_list_id'], $segmentation_options ) ) {
			$this->_err( "MailChimp: Unable to Segment Campaign\n\nList ID: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return new Response( false, $this->_mc_message( $mc->errorCode, $mc->errorMessage ) );
		}
		
		$settings = $this->get_settings();
		
		// Determine from email
		$from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $user['website']['domain'] : $settings['from_email'];
		$from_name = ( empty( $settings['from_name'] ) ) ? $user['website']['title'] : $settings['from_name'];
		
		$options = array(
			'list_id' => $user['website']['mc_list_id'],
			'subject' => $email_message['subject'],
			'from_email' => $from_email,
			'from_name' => $from_name,
			'to_email' => $user['website']['title'] . ' Subscribers',
			'tracking' => array( 
					'opens' => true,
					'html_clicks' => true,
					'text_clicks' => true
				),
			'analytics' => array( 'google' => $user['website']['ga_tracking_key'] ),
			'generate_text' => true
		);
		
		// Put the message in the template
		$message = $this->get_template( $email_message['subject'], $email_message['message'], $email_message['email_template_id'], $email_message['meta'] );
		
		$content = array(
			'html' => $message
		);
		
		$mc_campaign_id = $mc->campaignCreate( 'regular', $options, $content, $segmentation_options );
		
		// Handle Errors
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to Create New Campaign\n\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return new Response( false, $this->_mc_message( $mc->errorCode, $mc->errorMessage ) );
		}
		
		// Update our lists
		$this->db->update( 'email_messages', array( 'mc_campaign_id' => $mc_campaign_id ), array( 'email_message_id' => $email_message['email_message_id'], 'website_id' => $user['website']['website_id'] ), 's', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update email messages with campaign id.', __LINE__, __METHOD__ );
			return new Response( false, $this->_mc_message( $mc->errorCode, $mc->errorMessage ) );
		}
		
		$response = new Response( true );
		$response->add( 'mc_campaign_id', $mc_campaign_id );
		
		return $response;
	}
	
	/**
	 * Schedules an email to be sent (via MailChimp)
	 *
	 * @param int $email_message_id
	 * @return bool
	 */
	public function schedule_email( $email_message_id ) {
		global $user;
		
		// Typecast
		$email_message_id = (int) $email_message_id;
		
		// Synchronize this website's email list
		$this->synchronize_email_list();
		
		// Get mailchimp
		$mc = $this->mailchimp_instance();
		
		// Get the mailchimp
		$em = $this->get_email_message( $email_message_id );
		
		// No campaign, try to create
		if ( 0 == $em['mc_campaign_id'] ) {
			$em['mc_campaign_id'] = $this->mc_create_campaign( $em );
			
			// Still failing, now report an error
			if ( 0 == $em['mc_campaign_id'] ) {
				$this->_err( "Failed to schedule email, no Campaign ID has been assigned.", __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Are we scheduling it or are we sending it
		if ( strtotime( $em['date_sent'] ) > time() ) {
			$mc->campaignSchedule( $em['mc_campaign_id'], dt::date( 'Y-m-d H:i:s', strtotime( $em['date_sent'] ) + 18000 ) );
			
			// Handle errors
			if ( $mc->errorCode ) {
				$this->_err( "MailChimp: Unable to Schedule Campaign\n\nCampaign ID: " . $em['mc_campaign_id'] . "\nDate and Time: " . $em['date_sent'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
				return false;
			}
			
			// Update email messages
			$this->db->update( 'email_messages', array( 'status' => 1 ), array( 'email_message_id' => $email_message_id, 'website_id' => $user['website']['website_id'] ), 'i', 'ii' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to update email messages status to scheduled.', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			// Send campaign now
			$mc->campaignSendNow( $em['mc_campaign_id'] );
			
			// Handle errors
			if ( $mc->errorCode ) {
				$this->_err( "MailChimp: Unable to Send Campaign Now\n\nCampaign ID: " . $em['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
				return false;
			}
			
			// Update email messages
			$this->db->update( 'email_messages', array( 'status' => 2 ), array( 'email_message_id' => $email_message_id, 'website_id' => $user['website']['website_id'] ), 'i', 'ii' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to update email messages status to sent.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/***** EMAIL LISTS *****/
	
	/**
	 * Get the email lists associated with a website
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_email_lists( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$email_lists = $this->db->get_results( "SELECT a.`email_list_id`, a.`name`, a.`description`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, COUNT( DISTINCT b.`email_id`) AS count FROM `email_lists` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_list_id` = b.`email_list_id` ) LEFT JOIN `emails` AS c ON ( b.`email_id` = c.`email_id`) WHERE ( c.`status` = 1 OR c.`status` IS NULL ) $where GROUP BY a.`email_list_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list email messages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email_lists;
	}
	
	/**
	 * Count the email lists associated with a website
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_email_lists( $where ) {
		$count = $this->db->get_col( "SELECT a.`email_list_id`, a.`name`, a.`description`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, COUNT( DISTINCT b.`email_id`) AS count FROM `email_lists` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_list_id` = b.`email_list_id` ) LEFT JOIN `emails` AS c ON ( b.`email_id` = c.`email_id`) WHERE ( c.`status` = 1 OR c.`status` IS NULL ) $where GROUP BY a.`email_list_id`" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix -- shouldn't have to use PHP's count
		return count( $count );
	}
	
	/**
	 * Get an email list
	 *
	 * @param int $email_list_id
	 * @return array
	 */
	public function get_email_list( $email_list_id ) {
		$email_list = $this->db->get_row( 'SELECT `email_list_id`, `name`, `description` FROM `email_lists` WHERE `email_list_id` = ' . $email_list_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email list.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email_list;
	}
	
	/**
	 * Get email lists
	 *
	 * @param bool $count
	 * @return array
	 */
	public function get_email_lists( $count = false ) {
		global $user;
		
		if ( $count ) { 
			$email_lists = $this->db->prepare( 'SELECT a.`email_list_id`, a.`category_id`, a.`name`, COUNT( DISTINCT b.`email_id` ) AS count FROM `email_lists` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_list_id` = b.`email_list_id` ) LEFT JOIN `emails` AS c ON ( b.`email_id` = c.`email_id` ) WHERE a.`website_id` = ? AND c.`status` = 1 GROUP BY a.`email_list_id` ORDER BY a.`name`', 'i', $user['website']['website_id'] )->get_results( '', ARRAY_A );
		} else {
			$email_lists = $this->db->prepare( 'SELECT `email_list_id`, `category_id`, `name` FROM `email_lists` WHERE `website_id` = ? ORDER BY `name`', 'i', $user['website']['website_id'] )->get_results( '', ARRAY_A );
		}
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email_lists;
	}
	
	/**
	 * Create email list
	 *
	 * @param string $name
	 * @param string $description
	 * @return int
	 */
	public function create_email_list( $name, $description ) {
		global $user;
		
		$this->db->insert( 'email_lists', array( 'name' => $name, 'description' => $description, 'website_id' => $user['website']['website_id'], 'date_created' => dt::date('Y-m-d H:i:s') ), 'ssis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create email list.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add MailChimp List Interest Group
		if ( '0' != $user['website']['mc_list_id'] ) {
			$mc = $this->mailchimp_instance();
			
			$mc->listInterestGroupAdd( $user['website']['mc_list_id'], $name );
			
			// Handle any error, but don't stop
			if ( $mc->errorCode )
				$this->_err( "MailChimp: Unable to Create Email List\n\nList ID: " . $user['website']['mc_list_id'] . "\nName: $name\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update email list
	 *
	 * @param int $email_list_id
	 * @param string $name
	 * @param string $description
	 * @return int
	 */
	public function update_email_list( $email_list_id, $name, $description ) {
		global $user;
		
		// Update MailChimp List Interest Group
		if ( '0' != $user['website']['mc_list_id'] ) {
			// Get original
			$el = $this->get_email_list( $email_list_id );
			
			$mc = $this->mailchimp_instance();
			
			$mc->listInterestGroupUpdate( $user['website']['mc_list_id'], $el['name'], $name );
			
			// Handle any error, but don't stop
			if ( $mc->errorCode )
				$this->_err( "MailChimp: Unable to Update Email List\n\nList ID: " . $user['website']['mc_list_id'] . "\nOld Name: " . $el['name'] . "\nNew Name: $name\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
		}
		
		$this->db->update( 'email_lists', array( 'name' => $name, 'description' => $description ), array( 'email_list_id' => $email_list_id, 'website_id' => $website_id ), 'ss', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update email list.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete email list
	 *
	 * @param int $email_list_id
	 * @return bool
	 */
	public function delete_email_list( $email_list_id ) {
		global $user;
		
		// Delete MailChimp List Interest Group
		if ( '0' != $user['website']['mc_list_id'] ) {
			// Get the list
			$el = $this->get_email_list( $email_list_id );
			
			// Get the Mailchimp Instance
			$mc = $this->mailchimp_instance();
			
			// Delete list gruop
			$mc->listInterestGroupDel( $user['website']['mc_list_id'], $el['name'] );
			
			// Handle any error, but don't stop
			if ( $mc->errorCode )
				$this->_err( "MailChimp: Unable to Delete Email List\n\nList ID: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
		}
		
		// Delete email list
		$this->db->prepare( 'DELETE FROM `email_lists` WHERE `email_list_id` = ? AND `website_id` = ?', 'ii', $email_list_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete email list.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** EMAIL MESSAGES *****/
	
	/**
	 * Add a new email message
	 *
	 * @param int $email_template_id
	 * @param string $subject
	 * @param string $message
	 * @param string $type
	 * @param string $date_sent
	 * @param array $email_list_ids
	 * @param array $message_meta
	 * @return bool
	 */
	public function add_email_message( $email_template_id, $subject, $message, $type, $date_sent, $email_list_ids, $message_meta ) {
		global $user;
		
		$this->db->insert( 'email_messages', array( 'website_id' => $user['website']['website_id'], 'email_template_id' => $email_template_id, 'subject' => $subject, 'message' => $message, 'type' => $type, 'date_sent' => $date_sent, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add email message.', __LINE__, __METHOD__ );
			return false;
		}
		
		$email_message_id = $this->db->insert_id;
		
		// Add other data
		$this->add_message_email_lists( $email_message_id, $email_list_ids );
		$this->add_message_meta( $email_message_id, $message_meta );
		
		return $email_message_id;
	}
	
	/**
	 * Update an email message
	 *
	 * @Fix instead of calling all the mailchimp specifics, it should check to see which need to update, then update them
	 *
	 * @param int $email_message_id
	 * @param int $email_template_id
	 * @param string $subject
	 * @param string $message
	 * @param string $type
	 * @param string $date_sent
	 * @param array $email_list_ids
	 * @param array $message_meta
	 * @return bool
	 */
	public function update_email_message( $email_message_id, $email_template_id, $subject, $message, $type, $date_sent, $email_list_ids, $message_meta ) {
		global $user;
		
		// Add other date
		$this->add_message_email_lists( $email_message_id, $email_list_ids );
		$this->add_message_meta( $email_message_id, $message_meta );
		
		$this->db->update( 'email_messages', array( 'email_template_id' => $email_template_id, 'subject' => $subject, 'message' => $message, 'type' => $type, 'date_sent' => $date_sent ), array( 'website_id' => $user['website']['website_id'], 'email_message_id' => $email_message_id ), 'issss', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update email message.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Update the campaign
		$email_message = $this->get_email_message( $email_message_id );
		
		// If they don't have a campaign created -- don't do anything
		if ( 0 == $email_message['mc_campaign_id'] )
			return true;
		
		// Put the message in the template
		$mc = $this->mailchimp_instance();
	
		$segmentation_options = array( 
			'match' => 'any', 
			'conditions' => array( 
				array( 
					  'field' => 'interests', 
					  'op' => 'one', 
					  'value' => implode( ',', $email_message['email_lists'] ) 
				)
			)
		);
		
		// Do segment test to make sure it would work
		if ( !$mc->campaignSegmentTest( $user['website']['mc_list_id'], $segmentation_options ) ) {
			$this->_err( "MailChimp: Unable to Segment Campaign\n\nList ID: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		// Update campaign
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'segment_opts', $segmentation_options );
		
		// Handle any error
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Failed to Update Campaign - Segmentation Options\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update Subject
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'subject', $subject );
		
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to Update Campaign - Subject\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update Message
		$html_message = $this->get_template( $email_message['subject'], $email_message['message'], $email_message['email_template_id'], $email_message['meta'] );
		
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'content', array( 'html' => $html_message ) );
		
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to Update Campaign - Message\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update From Email
		$settings = $this->get_settings();
		$from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $user['website']['domain'] : $settings['from_email'];

		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'from_email', $from_email );
		
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to Update Campaign - From Email\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update From Name
		$from_name = ( empty( $settings['from_name'] ) ) ? $user['website']['title'] : $settings['from_name'];
		
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'from_name', $from_name );
		
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to Update Campaign - From Email\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Adds email list id associations to an email message
	 *
	 * @param int $email_message_id
	 * @param array $email_list_ids
	 * @return bool
	 */
	private function add_message_email_lists( $email_message_id, $email_list_ids ) {
		global $user;
		
		// Type juggling
		$email_message_id = (int) $email_message_id;
		
		// Delete any existing ones
		$this->db->query( "DELETE a.* FROM `email_message_associations` AS a LEFT JOIN `email_messages` AS b ON ( a.`email_message_id` = b.`email_message_id` ) WHERE a.`email_message_id` = $email_message_id AND b.`website_id` = " . (int) $user['website']['website_id'] );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete email message assocations.', __LINE__, __METHOD__ );
			return false;
		}
		
		$values = '';
		
		if ( is_array( $email_list_ids ) )
		foreach ( $email_list_ids as $el_id ) {
			if ( !empty( $values ) )
				$values .= ',';
		
			$values .= "( $email_message_id," . (int) $el_id . ' )';
		}
		
		$this->db->query( "INSERT INTO `email_message_associations` ( `email_message_id`, `email_list_id` ) VALUES $values" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create email message assocations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Adds email message meta to an email message
	 *
	 * @param int $email_message_id
	 * @param array $message_meta
	 * @return bool
	 */
	private function add_message_meta( $email_message_id, $message_meta ) {
		global $user;
		
		// Type Juggle
		$email_message_id = (int) $email_message_id;
		
		// @Fix why is 'type' in this table? If it wasn't there a simple INSERT ... ON DUPLICATE KEY could be done instead of both of these queries
		// Delete any existing message meta
		$this->db->query( "DELETE a.* FROM `email_message_meta` AS a LEFT JOIN `email_messages` AS b ON ( a.`email_message_id` = b.`email_message_id` ) WHERE a.`email_message_id` = $email_message_id AND b.`website_id` = " . (int) $user['website']['website_id'] );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete message meta.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Create values to insert
		$values = '';
		
		foreach ( $message_meta as $mm ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $email_message_id, '" . $this->db->escape( $mm[0] ) . "', '" . $this->db->escape( $mm[1] ) . "' )";
		}
		
		// Insert new meta
		if ( !empty( $values ) ) {
			$this->db->query( "INSERT INTO `email_message_meta` ( `email_message_id`, `type`, `value` ) VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to create message meta.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * List Email messages
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_email_messages( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$messages = $this->db->get_results( "SELECT `email_message_id`, `mc_campaign_id`, `subject`, `status`, `date_sent` FROM `email_messages` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list email messages.', __LINE__, __METHOD__ );
			return false;
		}

		// @Fix should be done in query
		// Modify the timezone
		if ( is_array( $messages ) ) {
			$timezone = $this->get_setting( 'timezone' );

			foreach ( $messages as &$m ) {
				$m['date_sent'] = strtotime( dt::adjust_timezone( $m['date_sent'], config::setting('server-timezone'), $timezone ) );
			}
		}
		
		return $messages;
	}
	
	/**
	 * Count Email messages
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_email_messages( $where ) {
		$count = $this->db->get_var( "SELECT `email_message_id`, `mc_campaign_id`, `subject`, `status`, UNIX_TIMESTAMP( `date_sent` ) - 18000 AS date_sent FROM `email_messages` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count email messages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Get Email Messages (for listing)
	 *
	 * @param int $email_message_id
	 * @param bool $extra (optional|true)
	 * @return array
	 */
	public function get_email_message( $email_message_id, $extra = true ) {
		global $user;
		
		// Type Juggling
		$email_message_id = (int) $email_message_id;
		$website_id = (int) $user['website']['website_id'];
		
		$message = $this->db->get_row( "SELECT `email_message_id`, `email_template_id`, `mc_campaign_id`, `subject`, `message`, `type`, `status`, `date_sent` FROM `email_messages` WHERE `email_message_id` = $email_message_id AND `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email message.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( $extra && $message ) {
			// Get the email lists
			$email_lists = $this->db->get_results( "SELECT a.`email_list_id`, b.`name` FROM `email_message_associations` AS a INNER JOIN `email_lists` AS b ON ( a.`email_list_id` = b.`email_list_id` ) WHERE a.`email_message_id` = $email_message_id AND b.`website_id` = $website_id", ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get email lists.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Give it to the message
			$message['email_lists'] = ar::assign_key( $email_lists, 'email_list_id', true );
			
			if ( 'product' == $message['type'] ) {
				// If it's a product email, get all the products
				$meta_data = $this->db->get_col( "SELECT `value` FROM `email_message_meta` WHERE `email_message_id` = $email_message_id" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to get product email meta data (product_ids).', __LINE__, __METHOD__ );
					return false;
				}
				
				// Start off the product ids
				$product_ids = '';
				
				if ( is_array( $meta_data ) )
				foreach ( $meta_data as $md ) {
					// Get variables
					$product_array = unserialize( html_entity_decode( $md, ENT_QUOTES, 'UTF-8' ) );
					$message['meta'][$product_array['product_id']]['price'] = $product_array['price'];
					$message['meta'][$product_array['product_id']]['order'] = $product_array['order'];
					
					if ( !empty( $product_ids ) )
						$product_ids .= ',';
					
					// Create list of product ids
					$product_ids .= $product_array['product_id'];
				}
				
				// Causes an error otherwise
				if ( empty( $product_ids ) ) {
					$message['meta'] = array();
				} else {
					$p = new Products;
					
					// Get products
					$products = $p->get_products( " AND a.`product_id` IN ($product_ids)" );
					
					// Put the data in the meta
					foreach ( $products as $product ) {
						$message['meta'][$product['product_id']] = array_merge( $message['meta'][$product['product_id']], $product );
					}
				}
			} else {
				// Get all the offer data
				$meta_data = $this->db->get_results( "SELECT `type`, `value` FROM `email_message_meta` WHERE `email_message_id` = $email_message_id", ARRAY_A );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to get offer email meta data.', __LINE__, __METHOD__ );
					return false;
				}
				
				$message['meta'] = ar::assign_key( $meta_data, 'type', true );
				
				if ( !isset( $message['meta'] ) )
					$message['meta'] = array();
			}
		}
		
		return $message;
	}
	
	/**
	 * Deletes an email message
	 *
	 * @param int $email_message_id
	 * @return bool
	 */
	public function delete_email_message( $email_message_id ) {
		global $user;
		
		// Typecast
		$email_message_id = (int) $email_message_id;
		
		// Get the message
		$message = $this->get_email_message( $email_message_id, false );
		
		if ( 0 != $message['mc_campaign_id'] ) {
			// Get Mailchimp
			$mc = $this->mailchimp_instance();
			
			// Delete the campaign
			$mc->campaignDelete( $message['mc_campaign_id'] );
			
			// Simply note the error, don't stop
			if ( $mc->errorCode )
				$this->_err( "MailChimp: Unable to Delete Campaign\n\nList ID: " . $user['website']['mc_list_id'] . "\nCampaign ID: " . $message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
		}
		
		// Delete the email message
		$this->db->prepare( 'DELETE FROM `email_messages` WHERE `email_message_id` = ? AND `website_id` = ?', 'ii', $email_message_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete email message.', __LINE__, __METHOD__ );
			return false;
		}
		
		// If a message did not get deleted
		if ( !$this->db->rows_affected )
			return true;
		
		// Delete email message associations
		$this->db->query( "DELETE FROM `email_message_associations` WHERE `email_message_id` = $email_message_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete email message associations.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete email message meat
		$this->db->query( "DELETE FROM `email_message_meta` WHERE `email_message_id` = $email_message_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete email message meta.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** AUTORESPONDERS *****/
	
	/**
	 * List autoresponders
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_autoresponders( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$autoresponders = $this->db->get_results( "SELECT `email_autoresponder_id`, `name`, `subject`, `default` FROM `email_autoresponders` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list autoresponders.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $autoresponders;
	}
	
	/**
	 * Count autoresponders
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_autoresponders( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `email_autoresponder_id` ) FROM `email_autoresponders` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count autoresponders.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Get an autoresponder
	 * 
	 * @param int $email_autoresponder_id
	 * @return array 
	 */
	public function get_autoresponder( $email_autoresponder_id ) {
		global $user;
		
		$autoresponder = $this->db->prepare( 'SELECT a.`email_autoresponder_id`, a.`email_list_id`, a.`name`, a.`subject`, a.`message`, a.`default`, a.`current_offer`, b.`name` AS email_list FROM `email_autoresponders` AS a LEFT JOIN `email_lists` AS b ON ( a.`email_list_id` = b.`email_list_id` ) WHERE a.`email_autoresponder_id` = ? AND a.`website_id` = ?', 'ii', $email_autoresponder_id, $user['website']['website_id'] )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $autoresponder;
	}
		
	/**
	 * Create autoresponder
	 *
	 * @param string $name
	 * @param string $subject
	 * @param string $message
	 * @param bool $current_offer
	 * @param int $email_list_id
	 * @return int
	 */
	public function create_autoresponder( $name, $subject, $message, $current_offer, $email_list_id ) {
		global $user;
		
		// There needs to be some value
		if ( NULL == $current_offer )
			$current_offer = 0;
		
		$this->db->insert( 'email_autoresponders', array( 'website_id' => $user['website']['website_id'], 'email_list_id' => $email_list_id, 'name' => $name, 'subject' => $subject, 'message' => $message, 'current_offer' => $current_offer, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update autoresponder
	 *
	 * @param int $email_autoresponder_id
	 * @param string $name
	 * @param string $subject
	 * @param string $message
	 * @param bool $current_offer
	 * @param int $email_list_id
	 * @return bool
	 */
	public function update_autoresponder( $email_autoresponder_id, $name, $subject, $message, $current_offer, $email_list_id ) {
		global $user;
		
		// There needs to be some value
		if ( NULL == $current_offer )
			$current_offer = 0;

		$this->db->update( 'email_autoresponders', array( 'email_list_id' => $email_list_id, 'name' => $name, 'subject' => $subject, 'message' => $message, 'current_offer' => $current_offer ), array( 'email_autoresponder_id' => $email_autoresponder_id, 'website_id' => $user['website']['website_id'] ), 'isssi', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get available autoresponder email lists
	 * 
	 * @param int $email_autoresponder_id
	 * @return array 
	 */
	public function get_autoresponder_email_lists( $email_autoresponder_id ) {
		global $user;
		
		$email_lists = $this->db->prepare( 'SELECT a.`email_list_id`, a.`category_id`, a.`name` FROM `email_lists`AS a LEFT JOIN `email_autoresponders` AS b ON ( a.`website_id` = b.`website_id` AND a.`email_list_id` = b.`email_list_id` ) WHERE a.`website_id` = ? AND a.`category_id` <> 0 AND ( b.`email_list_id` IS NULL OR b.`email_list_id` = ? ) GROUP BY a.`email_list_id` ORDER BY a.`name`', 'ii', $user['website']['website_id'], $email_autoresponder_id )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email_lists;
	}
	
	/**
	 * Delet autoresponder
	 *
	 * @param int $email_autoresponder_id
	 * @return bool
	 */
	 public function delete_autoresponder( $email_autoresponder_id ) {
		global $user;
		 
		$this->db->prepare( 'DELETE FROM `email_autoresponders` WHERE `email_autoresponder_id` = ? AND `website_id` = ?', 'ii', $email_autoresponder_id, $user['website']['website_id'] )->query('');
		 
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Sends a test autoresponder to an email
	 *
	 * @param string $email
	 * @param string $subject
	 * @param string $message
	 * @param bool $current_offer
	 * @return bool
	 */
	public function test_autoresponder( $email, $subject, $message, $current_offer ) {
		global $user;
		
		$settings = $this->get_settings();
		
		$from = ( empty( $settings['from_email'] ) ) ? $settings['from_name'] . '<noreply@' . $user['website']['domain'] . '>' : $settings['from_name'] . $settings['from_email'];
		
		// Check if we need to append the current offer
		if ( $current_offer ) {
			// Get the page id
			$w = new Websites;
			$wa = new Website_Attachments;

			$page = $w->get_page_by_slug( 'current-offer' );
			
			// Get the coupon attachment
			$coupon = $wa->get_by_name( $page['website_page_id'], 'coupon' );

			// If there is a coupon
			if ( $coupon )
				$message .= '<br /><br /><img src="http://' . $user['website']['domain'] . $coupon['value'] . '" alt="Coupon" />';
		}
		
		// Put the message in the template
		$message = $this->get_template( $subject, $message );
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: $from\r\n";
		
		return mail( $email, $subject, html_entity_decode( $message ), $headers );
	}
	
	/***** TEMPLATES *****/
	
	/**
	 * Gets a template and fills in the variables
	 *
	 * @param string $subject
	 * @param message $message
	 * @return string
	 */
	private function get_template( $subject, $message, $email_template_id = 0, $meta = '' ) {
		global $user;
		
		// Construct where
		if ( 0 != $email_template_id )
			$where = ' AND a.`email_template_id` = ' . (int) $email_template_id;
		
		// @Fix this should be grabbing by a website
		// Grab template
		$email_template = $this->db->get_row( "SELECT a.`template`, a.`type`, b.`object_id`, b.`type` AS assoc_type FROM `email_templates` AS a LEFT JOIN `email_template_associations` AS b ON ( a.`email_template_id` = b.`email_template_id` ) WHERE 1 $where LIMIT 1", ARRAY_A );
		
		// Format
		$message = format::autop( $message );
		
		// Email Variables
		$this->email_variables( &$message, &$subject );
		
		// Make sure that if it's not an offer email and its supposed to be associated
		// with a website, and it's not, that it returns false;
		if ( $email_template['type'] != 'offer' && $email_template['type'] != 'default' && $email_template['assoc_type'] == 'website' && $email_template['object_id'] <> $user['website']['website_id'] )
			return false;
		
		switch ( $email_template['type'] ) {
			case 'offer':
				// Instantiate classes
				$c = new Categories;
				$pr = new Products;
				
				$products_html = '';
				
				// Get settings
				$view_product_image = $this->get_setting( 'view-product-button' );
				$options = $this->get_template_options( $email_template_id );
				
				// Go through meta data
				foreach ( $meta as $offer_type => $m ) {
					list( $type, $value, $price ) = explode( '|', $m );
					
					if ( 'text' == $type ) {
						// Straight text -- put it in
						$$offer_type = $options['product_text'] . $value . '</p>';
					} else {
						// Get product
						$p = $pr->get_product( $value );
						
						// Get the product link
						$product_link = $c->category_url( $p['category_id'] ) . $p['slug'] . '/';
						
						// Get the image and prices
						$product_image = 'http://' . $p['industry'] . '.retailcatalog.us/products/' . $p['product_id'] . '/' . $p['image'];
						$price = ( '0' == $price ) ? '' : '<small>Price $' . number_format( $price, 2 ) . '</small>';
						
						// Figure out the size of the image
						list( $image_width, $image_height ) = @getimagesize( $product_image );
						
						if ( empty( $image_width ) )
							$image_width = 1;
						
						list( $image_width, $image_height ) = image::proportions( $image_width, $image_height, 144, 144 );
						$height_padding = ( 144 - $image_height ) / 2;
						$width_padding = ( 144 - $image_width ) / 2;
						
						// Put in the HTML
						$$offer_type = $options['product_image'] . '
							<a href="' . $product_link . '" title="' . $p['name'] . '" style="padding:0; width:144px; display:block; margin:0 auto;"><img src="' . $product_image . '" alt="' . $p['name'] . '" width="' . $image_width . '" height="' . $image_height . '" style="background:#fff; padding:' . $height_padding . 'px ' . $width_padding . 'px" border="0" /></a></div>' . $options['product_text'] . '
						<a href="' . $product_link . '" title="' . $p['name'] . '" style="font-size:16px; font-weight:bold; color:#' . $options['link_color'] . '; text-decoration:none; padding-top: 7px">' . $p['name'] . "</a><br />$price</p>";
					}
				}
				
				$html_message = str_replace( array( '[subject]', '[message]', '[offer_1]', '[offer_2]' ), array( $subject, $message, $offer_1, $offer_2 ), $email_template['template'] );
			break;
			
			case 'product':
				// Instantiate class
				$c = new Categories;
				$pr = new Products;
				
				// Get settings
				$settings = $this->get_settings();
				$view_product_image = $this->get_setting( 'view-product-button' );
				
				// Set variables
				$products_html = '';
				$i = 0;
				$open = false;
				$new_meta = array();
				
				// Set meta
				foreach ( $meta as $p ) {
					$new_meta[$p['order']] = $p;
				}
				
				// Sort by key
				ksort( $new_meta );
				
				// Get data
				foreach ( $new_meta as $p ) {
					$i++;
					
					// Every third product
					if ( 1 == $i % 3 ) {
						$products_html .= '<tr>';
						$open = true;
					}
					
					// Set default colors
					$price_color = ( empty( $settings['product-price-color'] ) ) ? '548557' : $settings['product-price-color'];
					$product_color = ( empty( $settings['product-color'] ) ) ? '78174c' : $settings['product-color'];
					
					// Get product link
					$product_link = $c->category_url( $p['category_id'] ) . $p['slug'] . '/';
					
					// Form image
					$product_image = 'http://' . $pr->get_industry( $p['product_id'] ) . '.retailcatalog.us/products/' . $p['product_id'] . '/' . $p['image'];
					$price = ( '0' == $p['price'] ) ? '' : 'Price <span style="color:#' . $product_color . '">$' . $p['price'] . '</span><br />';
					
					// Get image size
					list( $image_width, $image_height ) = @getimagesize( $product_image );
					
					if ( empty( $image_width ) )
						$image_width = 1;
					
					list( $image_width, $image_height ) = image::proportions( $image_width, $image_height, 144, 144 );
					$height_padding = ( 144 - $image_height ) / 2;
					$width_padding = ( 144 - $image_width ) / 2;
					
					// Set the products HTML
					$products_html .= '
						<td width="33%" style="text-align:center;font-size:14px; line-height:24px; font-weight:bold;" valign="top">
							<div>
								<a href="' . $product_link . '" title="' . $p['name'] . '" style="padding:0 7px 7px 0; width:144px; display:block; margin:0 auto;"><img src="' . $product_image . '" alt="' . $p['name'] . '" width="' . $image_width . '" height="' . $image_height . '" style="background:#fff; padding:' . $height_padding . 'px ' . $width_padding . 'px" border="0" /></a>
							</div>
							<a href="' . $product_link . '" title="' . $p['name'] . '" style="font-size:16px; font-weight:bold; color:#' . $price_color . '; text-decoration:none;">' . $p['name'] . '</a><br />' . $price . '
							<a href="' . $product_link . '" title="View Product"><img src="' . $view_product_image . '" alt="View Product" border="0" /></a>
						</td>';
					
					// Close every third product
					if ( 0 == $i % 3 ) {
						$products_html .= '</tr>';
						$open = false;
					}
				}
				
				// If it's still open, close it
				if ( $open )
					$products_html .= '</tr>';
				
				$html_message = str_replace( array( '[subject]', '[message]', '[products]' ), array( $subject, $message, $products_html ), $email_template['template'] );
			break;
			
			default:
				// Just do a normal message
				$html_message = str_replace( array( '[subject]', '[message]' ), array( $subject, $message ), $email_template['template'] );
			break;
		}
		
		return $html_message;
	}
	
	/**
	 * Replaces variable names in an email with actual values
	 *
	 * Receives any amount of variables and changes their values
	 */
	private function email_variables() {
		if ( func_num_args() <= 0 )
			return false;
		
		global $user;
		
		// Define list of variables
		$variables = array( '[website_title]' );
		
		$replacements = array( $user['website']['title'] );
		
		$args = func_get_args();
		
		foreach ( $args as $arg ) {
			$arg = str_replace( $variables, $replacements, $arg );
		}
	}
	
	/**
	 * Gets all the options associated with a template
	 *
	 * @param int $email_template_id
	 * @return array|bool
	 */
	private function get_template_options( $email_template_id ) {
		$options = $this->db->get_results( 'SELECT `key`, `value` FROM `email_template_options` WHERE `email_template_id` = ' . (int) $email_template_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get template options.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ar::assign_key( $options, 'key', true );
	}
	
	/***** EMAIL SETTINGS *****/
	
	/**
	 * Get email settings
	 *
	 * @return array
	 */
	public function get_settings() {
		global $user;
		
		// Get settings
		$settings = $this->db->get_results( 'SELECT `key`, `value` FROM `email_settings` WHERE `website_id` = ' . $user['website']['website_id'], ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email settings.', __LINE__, __METHOD__ );
			return false;
		}

		return ( $settings ) ? ar::assign_key( $settings, 'key', true ) : array();
	}
	
	/**
	 * Get Email Setting
	 *
	 * @param string $key
	 * @return string
	 */
	public function get_setting( $key ) {
		global $user;
		
		$value = $this->db->prepare( 'SELECT `value` FROM `email_settings` WHERE `key` = ? AND `website_id` = ?', 'si', $key, $user['website']['website_id'] )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get email setting.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $value;
	}
	
	/**
	 * Sets email settings
	 *
	 * @param array $settings
	 * @return bool
	 */
	public function set_settings( $settings ) {
		global $user;
		
		$values = '';
		
		// Make the settings safe for SQL
		foreach ( $settings as $k => $v ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= '(' . $user['website']['website_id'] . ", '" . $this->db->escape( $k ) . "', '" . $this->db->escape( $v ) . "')"; 
		}
		
		$this->db->query( "INSERT INTO `email_settings` ( `website_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update all the email settings', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** OTHER FUNCTIONS *****/
	
	/**
	 * Initiate MailChimp
	 *
	 * @return pointer
	 */
	private function mailchimp_instance() {
		// If it's set, use it
		if ( isset( $this->mc ) )
			return $this->mc;
		
		// Include the library and instantiate it
		library( 'MCAPI' );
		$this->mc = new MCAPI( config::key('mc-api') );
		
		return $this->mc;
	}
	
	/**
	 * Synchronize email lists
	 * 
	 * @return bool
	 */
	public function synchronize_email_list() {
		$this->remove_bad_emails();
		$this->update_email_lists();
	}
	
	/**
	 * Removes unsubscribes
	 *
	 * @return bool
	 */
	private function remove_bad_emails() {
		global $user;
		
		// Make sure they have a mailchimp list id to work off
		if ( !$user['website']['mc_list_id'] )
			return false;
		
		// Typecast
		$website_id = (int) $user['website']['website_id'];
		
		// Get mailchimp
		$mc = $this->mailchimp_instance();
		
		// Get the unsubscribers
		$unsubscribers = $mc->listMembers( $user['website']['mc_list_id'], 'unsubscribed', dt::date( 'Y-m-d H:i:s', time() - 86700 ), 0, 15000 );
		
		// Error Handling
		if ( $this->mc->errorCode )
			$this->_err( "MailChimp: Unable to get Unsubscribed Members\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage . "\nMembers returned: " . count( $unsubscribers ), __LINE__, __METHOD__ );
		
		$emails = '';
				
		if ( is_array( $unsubscribers ) )
		foreach ( $unsubscribers as $unsub ) {
			if ( !empty( $emails ) )
				$emails .= ',';
			
			$emails .= "'" . $this->db->escape( $unsub['email'] ) . "'";
		}
		
		// Mark the users as unsubscribed
		if ( !empty( $emails ) ) {
			$this->db->query( "UPDATE `emails` SET `status` = 0 WHERE `website_id` = $website_id AND `email` IN ($emails)" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to mark users as unsubscribed', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Get the cleaned
		$cleaned = $this->mc->listMembers( $user['website']['mc_list_id'], 'cleaned', dt::date( 'Y-m-d H:i:s', time() - 86700 ), 0, 10000 );
		
		// Error Handling
		if ( $this->mc->errorCode )
			$this->_err( "MailChimp: Unable to get Cleaned Members\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $this->mc->errorCode . "\nError Message: " . $mc->errorMessage . "\nMembers returned: " . count( $cleaned ), __LINE__, __METHOD__ );
		
		$emails = '';
		
		if ( is_array( $cleaned ) )
		foreach ( $cleaned as $clean ) {
			if ( !empty( $emails ) )
				$emails .= ',';
			
			$emails .= "'" . $this->db->escape( $clean['email'] ) . "'";
		}
		
		// Mark the users as cleaned
		if ( !empty( $emails ) ) {
			$this->db->query( "UPDATE `emails` SET `status` = 2 WHERE `website_id` = $website_id AND `email` IN ($emails)" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to mark users as cleaned', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Updates email lists
	 *
	 * @return bool
	 */
	private function update_email_lists() {
		global $user;
		
		// Typecast
		$website_id = (int) $user['website']['website_id'];
		
		$email_results = $this->db->get_results( "SELECT a.`email`, a.`email_id`, a.`name`, a.`phone`, GROUP_CONCAT( c.`name` ) AS interests FROM `emails` AS a INNER JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) INNER JOIN `email_lists` AS c ON ( b.`email_list_id` = c.`email_list_id` ) WHERE a.`website_id` = $website_id AND a.`status` = 1 AND ( a.`date_synced` = '0000-00-00 00:00:00' OR a.`timestamp` > a.`date_synced` ) AND a.`email` <> 'test@test.com' GROUP BY a.`email`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get emails that need to be syncd', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there isn't anything, continue
		if ( !$email_results || 0 == count( $email_results ) ) 
			return true;
		
		// Create array
		$email_lists = array();
		
		// We know an array exists or we would have aborted above
		foreach ( $email_results as $er ) {
			$emails[$er['email_id']] = array( 
				'EMAIL' => $er['email'],
				'EMAIL_TYPE' => 'html',
				'FNAME' => $er['name'],
				'INTERESTS' => $er['interests']
			);
			
			$email_interests = ( isset( $email_interests ) ) ? array_merge( $email_interests, explode( ',', $er['interests'] ) ) : explode( ',', $er['interests'] );
		}
		
		// Create array to hold email ids
		$synced_email_ids = array();
		
		// Get the unique interests
		$interests = array_unique( $email_interests );
		
		// Get Mailchimp
		$mc = $this->mailchimp_instance();
		
		$groups_result = $mc->listInterestGroups( $user['website']['mc_list_id'] );
		
		// Error Handling
		if ( $mc->errorCode )
			$this->_err( "MailChimp: Unable to get Interest Groups\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
		
		if ( !isset( $groups_result['groups'] ) )
			$groups_result['groups'] = array();

		foreach ( $interests as $i ) {
			if ( !in_array( $i, $groups_result['groups'] ) ) {
				$mc->listInterestGroupAdd( $user['website']['mc_list_id'], $i );
				
				// Error Handling
				if ( $mc->errorCode )
					$this->_err( "MailChimp: Unable to add Interest Group\n\nList_id: " . $user['website']['mc_list_id'] . "\nInterest Group: $i\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
			}
		}
		
		// list_id, batch of emails, require double optin, update existing users, replace interests
		$vals = $mc->listBatchSubscribe( $user['website']['mc_list_id'], $emails, false, true, true );
		
		if ( $mc->errorCode ) {
			$this->_err( "MailChimp: Unable to get Batch Subscribe\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage . "\nEmails:\n" . fn::info( $emails, false ) . fn::info( $email_results, false ), __LINE__, __METHOD__, false );
		} else {
			// Handle errors if there were any
			if ( $vals['error_count'] > 0 ) {
				$errors = '';
				
				foreach ( $vals['errors'] as $val ) {
					$errors .= "Email: " . $val['row']['EMAIL'] . "\nCode: " . $val['code'] . "\nError Message: " . $val['message'] . "\n\n";
				}
				
				$this->_err( "MailChimp: \n\nList_id: " . $user['website']['mc_list_id'] . "\n" . $vals['error_count'] . ' out of ' . $vals['error_count'] + $vals['success_count'] . ' emails were unabled to be subscribed' . "\n\n$errors", __LINE__, __METHOD__, false );
			}
			
			$synced_email_ids = array_keys( $emails );
		}
		
		// Set all the emails that were updated to say they were updated
		if ( count( $synced_email_ids ) > 1 ) {
			// Update meails to make them synced
			$this->db->query( 'UPDATE `emails` SET `date_synced` = NOW() WHERE `email_id` IN (' . implode( ',', $synced_email_ids ) . ')' );
																										  
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to sync emails', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Decodes HTML Entities in all email templates.
	 */
	public function decode_templates() {
		return;
		
		$where = "`type` = 'default'";
		$templates = $this->db->get_results( "SELECT `email_template_id`, `template` FROM `email_templates` WHERE $where", ARRAY_A );
		
		foreach ( $templates as &$t ) {
			if ( stristr( $t['template'], '&lt;!DOCTY' ) ) {
				$new_template = htmlspecialchars_decode( $t['template'] );
				$this->db->query( "UPDATE `email_templates` SET `template` = '$new_template' WHERE `email_template_id` = " . $t['email_template_id'] );
			}
		}
	}

    /**
     * Debug Mailchimp Errors
     *
     * @param int $mc_code
     * @param string, $mc_message
     * @return string
     */
    private function _mc_message( $mc_code, $mc_message ) {
        switch ( $mc_code ) {
            case 200:
                $message = _('Please see your Online Specialist about setting up or updating your email account.');
            break;
            
            case 270:
                $message = _('You have a duplicate list. Please remove the duplicate list and try again.');
            break;
            
            default:
                $message = $mc_message;
            break;
        }
        
        return $message;
    }
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @param bool $debug [optional]
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '', $debug = true ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method, $debug );
	}
}