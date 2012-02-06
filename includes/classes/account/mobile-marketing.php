<?php
/**
 * Handles all the Mobile Marketing
 *
 * Interact with Avid Mobile API wrapper
 * @package Imagine Retailer
 * @since 1.0
 */
class Mobile_Marketing extends Base_Class {
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
	 * @return array
	 */
	public function dashboard_messages() {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		$messages = $this->db->prepare( "SELECT `mobile_message_id`, `message` FROM `mobile_messages` WHERE `website_id` = $website_id AND `status` = 2 ORDER BY `date_sent` DESC LIMIT 5", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get dashboard messages.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $messages;
	}
	
	/**
	 * Dashboard Subscribers
	 * 
	 * @return array
	 */
	public function dashboard_subscribers() {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		$subscribers = $this->db->prepare( "SELECT `phone` FROM `mobile_subscribers` WHERE `website_id` = $website_id AND `status` = 1 ORDER BY `date_created` DESC LIMIT", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get dashboard subscribers.', __LINE__, __METHOD__ );
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
		
		$subscribers = $this->db->get_results( "SELECT DISTINCT a.`email_id`, a.`name`, a.`email`, a.`phone`, IF( 1 = a.`status`, UNIX_TIMESTAMP( a.`date_created` ), UNIX_TIMESTAMP( a.`date_unsubscribed` ) ) AS date FROM `emails` AS a WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list subscribers.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to count email subscribers.', __LINE__, __METHOD__ );
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
		
		$subscribers = $this->db->get_results( "SELECT DISTINCT a.`email_id`, a.`name`, a.`email`, a.`phone`, IF( 1 = a.`status`, UNIX_TIMESTAMP( a.`date_created` ), UNIX_TIMESTAMP( a.`date_unsubscribed` ) ) AS date FROM `emails` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_id` = b.`email_id` ) WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list subscribers.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to count email subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * Get Subscriber
	 *
	 * @param int $mobile_subscriber_id
	 * @return array
	 */
	public function get_subscriber( $mobile_subscriber_id ) {
		global $user;
		
		// Typecast
		$mobile_subscriber_id = (int) $mobile_subscriber_id;
		
		$subscriber = $this->db->get_row( "SELECT `phone` FROM `mobile_subscribers` WHERE `mobile_subscriber_id` = $mobile_subscriber_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get subscriber.', __LINE__, __METHOD__ );
			return false;
		}

        // Get lists that this subscriber is subscribed to
		$subscriber['mobile_lists'] = $this->db->get_col( "SELECT `mobile_list_id` FROM `mobile_associations` WHERE `mobile_subscriber_id` = $mobile_subscriber_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $subscriber;
	}
	
	/**
	 * Checks if a subscriber already exits
	 * 
	 * @param string $phone
	 * @return array
	 */
	public function subscriber_exists( $phone ) {
        global $user;

		$phone = $this->db->prepare( 'SELECT a.`mobile_subscriber_id`, a.`status` FROM `mobile_subscribers` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_subscriber_id` = b.`mobile_subscriber_id` ) LEFT JOIN `mobile_lists` AS c ON ( b.`mobile_list_id` = c.`mobile_list_id` ) WHERE a.`phone` = ? AND c.`website_id` = ?', 'si', $phone, $user['website']['website_id'] )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check if phone exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $phone;
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
			$this->err( 'Failed to get email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Remove email from lists
		$this->db->query( 'DELETE FROM `email_associations` WHERE `email_id` = ' . (int) $email_id . ' AND `email_list_id` IN ( SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = ' . (int) $user['website']['website_id'] . ' )' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to remove email from list.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Set the email's status to "unsubscribed"
		$this->db->update( 'emails', array( 'status' => 0 ), array( 'email_id' => $email_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to unsubscribe email.', __LINE__, __METHOD__ );
			return false;
		}
				
		return true;
	}
	
	/**
	 * Create Subscriber
	 *
	 * @param string $phone
	 * @return int
	 */
	public function create_subscriber( $phone ) {
		global $user;
		
		$this->db->insert( 'mobile_subscribers', array( 'website_id' => $user['website']['website_id'], 'phone' => $phone, 'status' => 1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create subscriber.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update an email subscription
	 *
	 * @param int $mobile_subscriber_id
	 * @param string $phone
	 * @return bool
	 */
	public function update_subscriber( $mobile_subscriber_id, $phone ) {
		$this->db->update( 'mobile_subscribers', array( 'phone' => $phone ), array( 'mobile_subscriber_id' => $mobile_subscriber_id ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update mobile.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update mobile list subscriptions
	 * 
	 * @param int $mobile_subscriber_id
	 * @param array $mobile_lists
	 * @return bool
	 */
	public function update_mobile_lists_subscription( $mobile_subscriber_id, $mobile_lists ) {
		// Typecast
		$mobile_subscriber_id = (int) $mobile_subscriber_id;
		
		$this->db->query( "DELETE FROM `mobile_associations` WHERE `mobile_subscriber_id` = $mobile_subscriber_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete mobile associations.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Add new values if they exist
		if ( is_array( $mobile_lists ) ) {
			$values = '';
			
			foreach ( $mobile_lists as $ml ){
				if ( !empty( $values ) )
					$values .= ',';
				
				$values .= "( $mobile_subscriber_id, " . (int) $ml . ')';
			}
			
			$this->db->query( "INSERT INTO `mobile_associations` ( `mobile_subscriber_id`, `mobile_list_id` )  VALUES $values" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to insert mobile associations.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Import phone numbers based on an array
	 * 
	 * @param array $phone_numbers
	 * @return bool
	 */
	public function import( $phone_numbers ) {
		global $user;
		
		// Typecast
		$website_id = (int) $user['website']['website_id'];
		
		// Select all the unsubscribed subscribers they already have
		$unsubscribed_subscribers = $this->db->get_col( "SELECT `phone` FROM `mobile_subscribers` WHERE `status` = 0 AND `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get unsubscribed subscribers.', __LINE__, __METHOD__ );
			return false;
		}

        // Declare values
        $values = array();

		// Create string to insert new emails
		foreach ( $phone_numbers as $phone ) {
			// Make sure they haven't been unsubscribed
			if ( in_array( $phone, $unsubscribed_subscribers ) )
				continue;
			
			$values[] = "( $website_id, '" . $this->db->escape( $phone ) . "', NOW() )";
		}

        $value_chunks = array_chunk( $values, 500 );

		// Insert 500 at a time
		foreach ( $value_chunks as $values ) {
			// Insert 500
			$this->db->query( 'INSERT INTO `mobile_subscribers` ( `website_id`, `phone`, `date_created` ) VALUES ' . implode( ',', $values ) );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to import subscribers.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/***** MOBILE LISTS *****/
	
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
			$this->err( 'Failed to list email messages.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to count email lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix -- shouldn't have to use PHP's count
		return count( $count );
	}
	
	/**
	 * Get an mobile list
	 *
	 * @param int $mobile_list_id
	 * @return array
	 */
	public function get_mobile_list( $mobile_list_id ) {
        // Type Juggling
        $mobile_list_id = (int) $mobile_list_id;

		$mobile_list = $this->db->get_row( "SELECT `mobile_list_id`, `name` FROM `mobile_lists` WHERE `mobile_list_id` = $mobile_list_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile list.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $mobile_list;
	}
	
	/**
	 * Get mobile lists
	 *
	 * @param bool $count
	 * @return array
	 */
	public function get_mobile_lists( $count = false ) {
		global $user;
		
		if ( $count ) { 
			$mobile_lists = $this->db->prepare( 'SELECT a.`mobile_list_id`, a.`category_id`, a.`name`, COUNT( DISTINCT b.`email_id` ) AS count FROM `email_lists` AS a LEFT JOIN `email_associations` AS b ON ( a.`email_list_id` = b.`email_list_id` ) LEFT JOIN `emails` AS c ON ( b.`email_id` = c.`email_id` ) WHERE a.`website_id` = ? AND c.`status` = 1 GROUP BY a.`email_list_id` ORDER BY a.`name`', 'i', $user['website']['website_id'] )->get_results( '', ARRAY_A );
		} else {
			$mobile_lists = $this->db->prepare( 'SELECT `mobile_list_id`, `category_id`, `name` FROM `email_lists` WHERE `website_id` = ? ORDER BY `name`', 'i', $user['website']['website_id'] )->get_results( '', ARRAY_A );
		}
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $mobile_lists;
	}
	
	/**
	 * Create mobile list
	 *
	 * @param string $name
	 * @return int
	 */
	public function create_mobile_list( $name ) {
		global $user;

        // Get setting
        $w = new Websites;
        $customer_id = $w->get_setting( 'avid-mobile-customer-id' );

        if ( !$customer_id )
            return false;

        // Create the group on their end
		library('avid-mobile-api');
        $am_groups = Avid_Mobile_API::groups( $customer_id );

        // Creat the group and get the ID
        $am_group_id = $am_groups->create_group( $name );

        // Create the list on our end
		$this->db->insert( 'mobile_lists', array( 'name' => $name, 'website_id' => $user['website']['website_id'], 'am_group_id' => $am_group_id, 'date_created' => dt::date('Y-m-d H:i:s') ), 'siis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create mobile list.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->db->insert_id;
	}
	
	/**
	 * Update mobile list
	 *
	 * @param int $mobile_list_id
	 * @param string $name
	 * @return int
	 */
	public function update_mobile_list( $mobile_list_id, $name ) {
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        // @avid

		$this->db->update( 'mobile_lists', array( 'name' => $name, 'description' => $description ), array( 'mobile_list_id' => $mobile_list_id, 'website_id' => $website_id ), 'ss', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update email list.', __LINE__, __METHOD__ );
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
				$this->err( "MailChimp: Unable to Delete Email List\n\nList ID: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
		}
		
		// Delete email list
		$this->db->prepare( 'DELETE FROM `email_lists` WHERE `email_list_id` = ? AND `website_id` = ?', 'ii', $email_list_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete email list.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** EMAIL MESSAGES *****/
	
	/**
	 * Add a new email message
	 *
	 * @param string $message
     * @param string $date_sent
     * @param bool $future
	 * @return int
	 */
	public function create_message( $message, $date_sent, $future ) {
        global $user;

        // @avid
		// Create the posting post
        $this->db->insert( 'mobile_messages', array( 'website_id' => $user['website']['website_id'], 'message' => $message, 'date_sent' => $date_sent, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to create the mobile message.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->db->insert_id;
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
			$this->err( 'Failed to update email message.', __LINE__, __METHOD__ );
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
			$this->err( "MailChimp: Unable to Segment Campaign\n\nList ID: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		// Update campaign
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'segment_opts', $segmentation_options );
		
		// Handle any error
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Failed to Update Campaign - Segmentation Options\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update Subject
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'subject', $subject );
		
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to Update Campaign - Subject\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update Message
		$html_message = $this->get_template( $email_message['subject'], $email_message['message'], $email_message['email_template_id'], $email_message['meta'] );
		
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'content', array( 'html' => $html_message ) );
		
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to Update Campaign - Message\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update From Email
		$settings = $this->get_settings();
		$from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $user['website']['domain'] : $settings['from_email'];

		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'from_email', $from_email );
		
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to Update Campaign - From Email\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}

		// Update From Name
		$from_name = ( empty( $settings['from_name'] ) ) ? $user['website']['title'] : $settings['from_name'];
		
		$mc->campaignUpdate( $email_message['mc_campaign_id'], 'from_name', $from_name );
		
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to Update Campaign - From Email\n\nCampaign ID: " . $email_message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
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
			$this->err( 'Failed to delete email message assocations.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to create email message assocations.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to delete message meta.', __LINE__, __METHOD__ );
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
				$this->err( 'Failed to create message meta.', __LINE__, __METHOD__ );
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
		
		$messages = $this->db->get_results( "SELECT `email_message_id`, `mc_campaign_id`, `subject`, `status`, UNIX_TIMESTAMP( `date_sent` ) - 18000 AS date_sent FROM `email_messages` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list email messages.', __LINE__, __METHOD__ );
			return false;
		}
		
		// @Fix should be done in query
		// Modify the timezone
		if ( is_array( $messages ) ) {
			$timezone = $this->get_setting( 'timezone' );
			
			foreach ( $messages as &$m ) {
				$m['date_sent'] -= $timezone * 3600;
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
			$this->err( 'Failed to count email messages.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to get email message.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( $extra && $message ) {
			// Get the email lists
			$email_lists = $this->db->get_results( "SELECT a.`email_list_id`, b.`name` FROM `email_message_associations` AS a INNER JOIN `email_lists` AS b ON ( a.`email_list_id` = b.`email_list_id` ) WHERE a.`email_message_id` = $email_message_id AND b.`website_id` = $website_id", ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get email lists.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Give it to the message
			$message['email_lists'] = ar::assign_key( $email_lists, 'email_list_id', true );
			
			if ( 'product' == $message['type'] ) {
				// If it's a product email, get all the products
				$meta_data = $this->db->get_col( "SELECT `value` FROM `email_message_meta` WHERE `email_message_id` = $email_message_id" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to get product email meta data (product_ids).', __LINE__, __METHOD__ );
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
					$this->err( 'Failed to get offer email meta data.', __LINE__, __METHOD__ );
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
				$this->err( "MailChimp: Unable to Delete Campaign\n\nList ID: " . $user['website']['mc_list_id'] . "\nCampaign ID: " . $message['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
		}
		
		// Delete the email message
		$this->db->prepare( 'DELETE FROM `email_messages` WHERE `email_message_id` = ? AND `website_id` = ?', 'ii', $email_message_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete email message.', __LINE__, __METHOD__ );
			return false;
		}
		
		// If a message did not get deleted
		if ( !$this->db->rows_affected )
			return true;
		
		// Delete email message associations
		$this->db->query( "DELETE FROM `email_message_associations` WHERE `email_message_id` = $email_message_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete email message associations.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete email message meat
		$this->db->query( "DELETE FROM `email_message_meta` WHERE `email_message_id` = $email_message_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete email message meta.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to list autoresponders.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to count autoresponders.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to get autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $autoresponder;
	}
		
	/**
	 * Create autoresponder
	 *
	 * @param string $name
	 * @param string $message
	 * @param int $mobile_list_id
	 * @return int
	 */
	public function create_autoresponder( $name, $message, $mobile_list_id ) {
		global $user;
		
		$this->db->insert( 'mobile_autoresponders', array( 'website_id' => $user['website']['website_id'], 'mobile_list_id' => $mobile_list_id, 'name' => $name, 'message' => $message, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update autoresponder
	 *
	 * @param int $mobile_autoresponder_id
     * @param string $name
	 * @param string $message
	 * @param int $mobile_list_id
	 * @return bool
	 */
	public function update_autoresponder( $mobile_autoresponder_id, $name, $message, $mobile_list_id ) {
		global $user;
		
		$this->db->update( 'mobile_autoresponders', array( 'mobile_list_id' => $mobile_list_id, 'name' => $name, 'message' => $message ), array( 'mobile_autoresponder_id' => $mobile_autoresponder_id, 'website_id' => $user['website']['website_id'] ), 'iss', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get available autoresponder mobile lists
	 * 
	 * @param int $mobile_autoresponder_id
	 * @return array 
	 */
	public function get_autoresponder_mobile_lists( $mobile_autoresponder_id ) {
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_autoresponder_id = (int) $mobile_autoresponder_id;

		$mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, a.`mobile_keyword_id`, a.`name` FROM `mobile_lists`AS a LEFT JOIN `mobile_autoresponders` AS b ON ( a.`website_id` = b.`website_id` AND a.`mobile_list_id` = b.`mobile_list_id` ) WHERE a.`website_id` = $website_id AND a.`category_id` <> 0 AND ( b.`mobile_list_id` IS NULL OR b.`mobile_list_id` = $mobile_autoresponder_id ) GROUP BY a.`mobile_list_id` ORDER BY a.`name`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $mobile_lists;
	}
	
	/**
	 * Delete autoresponder
	 *
	 * @param int $mobile_autoresponder_id
	 * @return bool
	 */
	 public function delete_autoresponder( $mobile_autoresponder_id ) {
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_autoresponder_id = (int) $mobile_autoresponder_id;

		$this->db->query( "DELETE FROM `mobile_autoresponders` WHERE `mobile_autoresponder_id` = $mobile_autoresponder_id AND `website_id` = $website_id" );
		 
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete autoresponder.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
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
			$this->err( 'Failed to get email settings.', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to get email setting.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $value;
	}
	
	/**
	 * Sets mobile settings
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
			
			$values .= '(' . (int) $user['website']['website_id'] . ", '" . $this->db->escape( $k ) . "', '" . $this->db->escape( $v ) . "')";
		}
		
		$this->db->query( "INSERT INTO `mobile_settings` ( `website_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update all the mobile settings', __LINE__, __METHOD__ );
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
			$this->err( "MailChimp: Unable to get Unsubscribed Members\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage . "\nMembers returned: " . count( $unsubscribers ), __LINE__, __METHOD__ );
		
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
				$this->err( 'Failed to mark users as unsubscribed', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		// Get the cleaned
		$cleaned = $this->mc->listMembers( $user['website']['mc_list_id'], 'cleaned', dt::date( 'Y-m-d H:i:s', time() - 86700 ), 0, 10000 );
		
		// Error Handling
		if ( $this->mc->errorCode )
			$this->err( "MailChimp: Unable to get Cleaned Members\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $this->mc->errorCode . "\nError Message: " . $mc->errorMessage . "\nMembers returned: " . count( $cleaned ), __LINE__, __METHOD__ );
		
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
				$this->err( 'Failed to mark users as cleaned', __LINE__, __METHOD__ );
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
			$this->err( 'Failed to get emails that need to be syncd', __LINE__, __METHOD__ );
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
			$this->err( "MailChimp: Unable to get Interest Groups\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
		
		if ( !isset( $groups_result['groups'] ) )
			$groups_result['groups'] = array();

		foreach ( $interests as $i ) {
			if ( !in_array( $i, $groups_result['groups'] ) ) {
				$mc->listInterestGroupAdd( $user['website']['mc_list_id'], $i );
				
				// Error Handling
				if ( $mc->errorCode )
					$this->err( "MailChimp: Unable to add Interest Group\n\nList_id: " . $user['website']['mc_list_id'] . "\nInterest Group: $i\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
			}
		}
		
		// list_id, batch of emails, require double optin, update existing users, replace interests
		$vals = $mc->listBatchSubscribe( $user['website']['mc_list_id'], $emails, false, true, true );
		
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to get Batch Subscribe\n\nList_id: " . $user['website']['mc_list_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage . "\nEmails:\n" . fn::info( $emails, false ) . fn::info( $email_results, false ), __LINE__, __METHOD__, false );
		} else {
			// Handle errors if there were any
			if ( $vals['error_count'] > 0 ) {
				$errors = '';
				
				foreach ( $vals['errors'] as $val ) {
					$errors .= "Email: " . $val['row']['EMAIL'] . "\nCode: " . $val['code'] . "\nError Message: " . $val['message'] . "\n\n";
				}
				
				$this->err( "MailChimp: \n\nList_id: " . $user['website']['mc_list_id'] . "\n" . $vals['error_count'] . ' out of ' . $vals['error_count'] + $vals['success_count'] . ' emails were unabled to be subscribed' . "\n\n$errors", __LINE__, __METHOD__, false );
			}
			
			$synced_email_ids = array_keys( $emails );
		}
		
		// Set all the emails that were updated to say they were updated
		if ( count( $synced_email_ids ) > 1 ) {
			// Update meails to make them synced
			$this->db->query( 'UPDATE `emails` SET `date_synced` = NOW() WHERE `email_id` IN (' . implode( ',', $synced_email_ids ) . ')' );
																										  
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to sync emails', __LINE__, __METHOD__ );
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
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '', $debug = true ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method, $debug );
	}
}