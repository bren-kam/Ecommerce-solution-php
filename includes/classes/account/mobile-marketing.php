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
     * Avid Mobile Customer ID
     */
    private $settings;
    private $am_keywords;
    private $am_groups;

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

		$messages = $this->db->get_results( "SELECT `mobile_message_id`, `message` FROM `mobile_messages` WHERE `website_id` = $website_id AND `status` = 2 ORDER BY `date_sent` DESC LIMIT 5", ARRAY_A );
		
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

		$subscribers = $this->db->get_results( "SELECT `phone` FROM `mobile_subscribers` WHERE `website_id` = $website_id AND `status` = 1 ORDER BY `date_created` DESC LIMIT", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get dashboard subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $subscribers;
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
		
		$subscribers = $this->db->get_results( "SELECT DISTINCT a.`mobile_subscriber_id`, a.`phone`, IF( 1 = a.`status`, a.`date_created`, a.`date_unsubscribed` ) AS date FROM `mobile_subscribers` AS a WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
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
		$count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`mobile_subscriber_id` ) FROM `mobile_subscribers` AS a WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count mobile subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}
	
	/**
	 * List Subscribers by mobile list id
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_subscribers_by_mobile_list_id( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$subscribers = $this->db->get_results( "SELECT DISTINCT a.`mobile_subscriber_id`, a.`phone`, IF( 1 = a.`status`, a.`date_created`, a.`date_unsubscribed` ) AS date FROM `mobile_subscribers` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_subscriber_id` = b.`mobile_subscriber_id` ) WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $subscribers;
	}
	
	/**
	 * Count Subscribers by mobile list id
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_subscribers_by_mobile_list_id( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`mobile_subscriber_id` ) FROM `mobile_subscribers` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_subscriber_id` = b.`mobile_subscriber_id` ) WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count mobile subscribers.', __LINE__, __METHOD__ );
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
	 * Unsubscribes a single mobile_subscriber
     *
	 * @param int $mobile_subscriber_id
	 * @return bool
	 */
	public function unsubscribe( $mobile_subscriber_id ){
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_subscriber_id = (int) $mobile_subscriber_id;

		// First, get avoid mobile and unsubscribe
		// @avid

		// Remove subscriber from lists
		$this->db->query( "DELETE a.* FROM `mobile_associations` AS a LEFT JOIN `mobile_lists` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) WHERE a.`mobile_subscriber_id` = $mobile_subscriber_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to remove subscriber from list.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Set the subscriber's status to "unsubscribed"
		$this->db->update( 'mobile_subscribers', array( 'status' => 0 ), array( 'mobile_subscriber_id' => $mobile_subscriber_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to unsubscribe subscriber.', __LINE__, __METHOD__ );
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
	 * Update a mobile subscription
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

		// Create string to insert new phone numbers
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

    /***** KEYWORDS *****/

    /**
     * Get the mobile keywords  associated with a website
     *
     * @param array( $where, $order_by, $limit )
     * @return array
     */
    public function list_keywords( $variables ) {
        // Get the variables
        list( $where, $order_by, $limit ) = $variables;

        $keywords = $this->db->get_results( "SELECT `mobile_keyword_id`, `name`, `keyword`, `date_started` FROM `mobile_keywords` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to list keywords.', __LINE__, __METHOD__ );
            return false;
        }

        return $keywords;
    }

    /**
     * Count the keywords associated with a website
     *
     * @param string $where
     * @return array
     */
    public function count_keywords( $where ) {
        $count = $this->db->get_var( "SELECT COUNT(`mobile_keyword_id`) FROM `mobile_keywords` WHERE 1 $where" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to count keywords.', __LINE__, __METHOD__ );
            return false;
        }

        return $count;
    }

    /**
     * Create keyword
     *
     * @param string $name
     * @param string $keyword
     * @param string $response
     * @param string $date_started
     * @param string $timezone
     * @return array
     */
    public function create_keyword( $name, $keyword, $response, $date_started, $timezone ) {
        global $user;

        // Make sure it's instantiated
        if ( !$this->_get_am_lib('keywords') )
            return false;

        // Create the keyword
        $am_keyword_campaign_id = $this->am_keywords->create( $name, $keyword, $response, $date_started, $timezone );

        if ( !$am_keyword_campaign_id )
            return false;

        // Add the keyword to our database
        $this->db->insert( 'mobile_keywords', array(
            'am_keyword_campaign_id' => $am_keyword_campaign_id
            , 'website_id' => $user['website']['website_id']
            , 'name' => $name
            , 'keyword' => $keyword
            , 'response' => $response
            , 'date_started' => $date_started
            , 'timezone' => $timezone
            , 'date_created' => dt::date('Y-m-d H:i:s')
        ), 'iissssss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to create keyword.', __LINE__, __METHOD__ );
            return false;
        }

        // Get the keyword ID
        $mobile_keyword_id = $this->db->insert_id;

        // Now we need to create the list on ourside
        if ( !$this->create_mobile_list( $name, $mobile_keyword_id, $am_keyword_campaign_id ) )
            return false;

        return $mobile_keyword_id;
    }

    /**
     * Update keyword
     *
     * @param int $mobile_keyword_id
     * @param string $name
     * @param string $keyword
     * @param string $response
     * @param string $date_started
     * @param string $timezone
     * @return array
     */
    public function update_keyword( $mobile_keyword_id, $name, $keyword, $response, $date_started, $timezone ) {
        global $user;

        // Make sure it's instantiated
        if ( !$this->_get_am_lib('keywords') )
            return false;

        // First, get keyword, update it, then update our own
        $am_keyword_campaign_id = $this->get_am_keyword_campaign_id( $mobile_keyword_id );

        // Delete the keywords
        if( !$this->am_keywords->update( $am_keyword_campaign_id, $name, $keyword, $response, $date_started, $timezone ) )
            return false;

        // Add the keyword to our database
        $this->db->update( 'mobile_keywords', array(
            'name' => $name
            , 'keyword' => $keyword
            , 'response' => $response
            , 'date_started' => $date_started
            , 'timezone' => $timezone
        ), array(
              'mobile_keyword_id' => $mobile_keyword_id
            , 'website_id' => $user['website']['website_id']

        ), 'sssss', 'ii' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to update keyword.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
    }

    /**
     * Get a keyword
     *
     * @param int $mobile_keyword_id
     * @return array
     */
    public function get_keyword( $mobile_keyword_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_keyword_id = (int) $mobile_keyword_id;

        $keyword = $this->db->get_row( "SELECT `mobile_keyword_id`, `name`, `keyword`, `response`, `date_started`, `timezone` FROM `mobile_keywords` WHERE `mobile_keyword_id` = $mobile_keyword_id AND `website_id` = $website_id", ARRAY_A );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to get keyword.', __LINE__, __METHOD__ );
            return false;
        }

        return $keyword;
    }

    /**
     * Delete Keyword
     *
     * @param int $mobile_keyword_id
     * @return bool
     */
    public function delete_keyword( $mobile_keyword_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_keyword_id = (int) $mobile_keyword_id;

        // First, get keyword, delete it, then delete our own
        $am_keyword_campaign_id = $this->get_am_keyword_campaign_id( $mobile_keyword_id );

        if ( $am_keyword_campaign_id ) {
           // Make sure it's instantiated
            if ( !$this->_get_am_lib('keywords') )
                return false;

            // Delete the keywords
            if ( !$this->am_keywords->delete( $am_keyword_campaign_id ) )
                return false;
        }

        // Remove keyword
        $this->db->query( "DELETE FROM `mobile_keywords` WHERE `mobile_keyword_id` = $mobile_keyword_id AND `website_id` = $website_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to delete keyword.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
    }

    /**
     * Get Avid Mobile Keyword Campaign ID
     *
     * @param $mobile_keyword_id
     * @return string
     */
    public function get_am_keyword_campaign_id( $mobile_keyword_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_keyword_id = (int) $mobile_keyword_id;

        $am_keyword_campaign_id = $this->db->get_var( "SELECT `am_keyword_campaign_id` FROM `mobile_keywords` WHERE `mobile_keyword_id` = $mobile_keyword_id AND `website_id` = $website_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to am_keyword_campaign_id.', __LINE__, __METHOD__ );
            return false;
        }

        return $am_keyword_campaign_id;
    }

    /**
     * Check keyword availability
     *
     * @param string $keyword
     * @return bool
     */
    public function check_keyword_availability( $keyword ) {
        // Make sure it's instantiated
        if ( !$this->_get_am_lib('keywords') )
            return false;
		
		// See if its available
		return $this->am_keywords->available( $keyword );
    }

	/***** MOBILE LISTS *****/

	/**
	 * Get the mobile lists associated with a website
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_mobile_lists( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, a.`mobile_keyword_id`, a.`name`, a.`date_created`, COUNT( DISTINCT b.`mobile_subscriber_id`) AS count FROM `mobile_lists` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) LEFT JOIN `mobile_subscribers` AS c ON ( b.`mobile_subscriber_id` = c.`mobile_subscriber_id` ) WHERE ( c.`status` = 1 OR c.`status` IS NULL ) $where GROUP BY a.`mobile_list_id` $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list mobile messages.', __LINE__, __METHOD__ );
			return false;
		}

		return $mobile_lists;
	}

	/**
	 * Count the mobile lists associated with a website
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_mobile_lists( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`mobile_list_id` ) FROM `mobile_lists` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) LEFT JOIN `mobile_subscribers` AS c ON ( b.`mobile_subscriber_id` = c.`mobile_subscriber_id` ) WHERE ( c.`status` = 1 OR c.`status` IS NULL ) $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count mobile lists.', __LINE__, __METHOD__ );
			return false;
		}

		return $count;
	}

	/**
	 * Get an mobile list
	 *
	 * @param int $mobile_list_id
	 * @return array
	 */
	public function get_mobile_list( $mobile_list_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $mobile_list_id = (int) $mobile_list_id;

		$mobile_list = $this->db->get_row( "SELECT `mobile_list_id`, `am_group_id`, `mobile_keyword_id`, `name` FROM `mobile_lists` WHERE `mobile_list_id` = $mobile_list_id AND `website_id` = $website_id", ARRAY_A );

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

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		if ( $count ) {
			$mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, a.`am_group_id`, a.`name`, COUNT( DISTINCT b.`mobile_subscription_id` ) AS count FROM `mobile_lists` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) LEFT JOIN `mobile_subscribers` AS c ON ( b.`mobile_subscriber_id` = c.`mobile_subscriber_id` ) WHERE a.`website_id` = $website_id AND c.`status` = 1 GROUP BY a.`mobile_list_id` ORDER BY a.`name`", ARRAY_A );
		} else {
			$mobile_lists = $this->db->get_results( "SELECT `mobile_list_id`, `am_group_id`, `name` FROM `mobile_lists` WHERE `website_id` = $website_id ORDER BY `name`", ARRAY_A );
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
     * @param int $mobile_keyword_id [optional]
     * @param int $am_keyword_campaign_id [optional]
	 * @return int
	 */
	public function create_mobile_list( $name, $mobile_keyword_id = NULL, $am_keyword_campaign_id = NULL ) {
		global $user;

        // Make sure it's instantiated
        if ( !$this->_get_am_lib('groups') )
            return false;

        if ( is_null( $mobile_keyword_id ) && is_null( $am_keyword_campaign_id ) ) {
            // Creat the group and get the ID
            $am_group_id = $this->am_groups->create( $name );

            // Create the list on our end
            $this->db->insert( 'mobile_lists', array( 'name' => $name, 'website_id' => $user['website']['website_id'], 'am_group_id' => $am_group_id, 'date_created' => dt::date('Y-m-d H:i:s') ), 'siis' );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->err( 'Failed to create mobile list.', __LINE__, __METHOD__ );
                return false;
            }
        } else {
            // They have to both be here
            if ( is_null( $mobile_keyword_id ) || is_null( $am_keyword_campaign_id ) )
                return false;
			
            // Create a dynamic group
            $am_group_id = $this->am_groups->create( $name, AM_Groups::GROUP_DYNAMIC );

            // Make sure it was created properly
            if ( !$am_group_id )
                return false;

            // Link the group and the keyword
            if ( !$this->am_groups->link_dynamic_group( $am_group_id, $am_keyword_campaign_id ) )
                return false;

            // Create the dynamic list on our end
            $this->db->insert( 'mobile_lists', array( 'name' => $name, 'website_id' => $user['website']['website_id'], 'am_group_id' => $am_group_id, 'mobile_keyword_id' => $mobile_keyword_id, 'date_created' => dt::date('Y-m-d H:i:s') ), 'siiis' );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->err( 'Failed to create dynamic mobile list.', __LINE__, __METHOD__ );
                return false;
            }
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

        // Get the mobile list
        $mobile_list = $this->get_mobile_list( $mobile_list_id );

        // Make sure it's instantiated
        if ( !$this->_get_am_lib('groups') )
            return false;

        // Define the type of the group
        $type = ( 0 == $mobile_list['mobile_keyword_id'] ) ? AM_Groups::GROUP_STATIC : AM_Groups::GROUP_DYNAMIC;

        // Update the Avid Mobile group
        if ( !$this->am_groups->update_group( $mobile_list['am_group_id'], $name, $type ) )
            return false;

        // Update the list
		$this->db->update( 'mobile_lists', array( 'name' => $name ), array( 'mobile_list_id' => $mobile_list_id, 'website_id' => $user['website']['website_id'] ), 's', 'ii' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update mobile list.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}

	/**
	 * Delete Mobile list
	 *
	 * @param int $mobile_list_id
	 * @return bool
	 */
	public function delete_mobile_list( $mobile_list_id ) {
		global $user;

        // Get the mobile list
        $mobile_list = $this->get_mobile_list( $mobile_list_id );

        // Make sure it's instantiated
        if ( !$this->_get_am_lib('groups') )
            return false;

        // Delete Avoid Mobile Group
        if ( !$this->am_groups->delete( $mobile_list['am_group_id'] ) )
            return false;

        // Type Juggling
        $mobile_list_id = (int) $mobile_list_id;
        $website_id = (int) $user['website']['website_id'];

		// Delete mobile list
		$this->db->query( "DELETE FROM `mobile_lists` WHERE `mobile_list_id` = $mobile_list_id AND `website_id` = $website_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete mobile list.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}

	/***** MOBILE MESSAGES *****/

	/**
	 * Add a new message
	 *
	 * @param string $message
     * @param string $date_sent
     * @param bool $future
	 * @return int
	 */
	public function create_message( $message, $date_sent, $mobile_list_ids, $future ) {
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
	 * Update a message
	 *
	 * @param int $mobile_message_id
	 * @param string $message
	 * @param string $date_sent
	 * @param array $mobile_list_ids
     * @param bool $future
	 * @return bool
	 */
	public function update_message( $mobile_message_id, $message, $date_sent, $mobile_list_ids, $future ) {
		global $user;

        // If it's not future we need to send it now
        // @avid

		// Add other date
		$this->add_message_lists( $mobile_message_id, $mobile_list_ids );

		$this->db->update( 'mobile_messages', array( 'message' => $message, 'date_sent' => $date_sent ), array( 'website_id' => $user['website']['website_id'], 'mobile_message_id' => $mobile_message_id ), 'ss', 'ii' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update mobile message.', __LINE__, __METHOD__ );
			return false;
		}

		// Update the campaign
		$message = $this->get_message( $mobile_message_id );

		// If they don't have a campaign created -- don't do anything
		if ( 0 == $message['am_blast_id'] )
			return true;

		// @avid update

		return true;
	}

	/**
	 * Adds mobile list id associations to a message
	 *
	 * @param int $mobile_message_id
	 * @param array $mobile_list_ids
	 * @return bool
	 */
	private function add_message_lists( $mobile_message_id, $mobile_list_ids ) {
		global $user;

		// Type Juggling
		$mobile_message_id = (int) $mobile_message_id;
        $website_id = (int) $user['website']['website_id'];

		// Delete any existing ones
		$this->db->query( "DELETE a.* FROM `mobile_message_associations` AS a LEFT JOIN `mobile_messages` AS b ON ( a.`mobile_message_id` = b.`mobile_message_id` ) WHERE a.`mobile_message_id` = $mobile_message_id AND b.`website_id` = $website_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete message associations.', __LINE__, __METHOD__ );
			return false;
		}

		$values = '';

		if ( is_array( $mobile_list_ids ) )
		foreach ( $mobile_list_ids as $ml_id ) {
			if ( !empty( $values ) )
				$values .= ',';

			$values .= "( $mobile_message_id," . (int) $ml_id . ' )';
		}

		$this->db->query( "INSERT INTO `mobile_message_associations` ( `mobile_message_id`, `mobile_list_id` ) VALUES $values" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create mobile message associations.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}

	/**
	 * List Mobile Messages
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_messages( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$messages = $this->db->get_results( "SELECT `mobile_message_id`, `summary`, `status`, `date_sent` FROM `mobile_messages` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list mobile messages.', __LINE__, __METHOD__ );
			return false;
		}

		return $messages;
	}

	/**
	 * Count Mobile Messages
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_messages( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `mobile_message_id` ) FROM `mobile_messages` WHERE 1 $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count mobile messages.', __LINE__, __METHOD__ );
			return false;
		}

		return $count;
	}

	/**
	 * Get a message
	 *
	 * @param int $mobile_message_id
	 * @param bool $extra (optional|true)
	 * @return array
	 */
	public function get_message( $mobile_message_id, $extra = true ) {
		global $user;

		// Type Juggling
		$mobile_message_id = (int) $mobile_message_id;
		$website_id = (int) $user['website']['website_id'];

		$message = $this->db->get_row( "SELECT `mobile_message_id`, `am_blast_id`, `message`, `status`, `date_sent` FROM `mobile_messages` WHERE `mobile_message_id` = $mobile_message_id AND `website_id` = $website_id", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile message.', __LINE__, __METHOD__ );
			return false;
		}

		if ( $extra && $message ) {
			// Get the mobile lists
			$mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, b.`name` FROM `mobile_message_associations` AS a INNER JOIN `mobile_lists` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) WHERE a.`mobile_message_id` = $mobile_message_id AND b.`website_id` = $website_id", ARRAY_A );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get mobile lists.', __LINE__, __METHOD__ );
				return false;
			}

			// Give it to the message
			$message['mobile_lists'] = ar::assign_key( $mobile_lists, 'mobile_list_id', true );
		}

		return $message;
	}

	/**
	 * Deletes a message
	 *
	 * @param int $mobile_message_id
	 * @return bool
	 */
	public function delete_message( $mobile_message_id ) {
		global $user;

		// Type Juggling
		$mobile_message_id = (int) $mobile_message_id;
        $website_id = (int) $user['website']['website_id'];

		// Get the message
		$message = $this->get_message( $mobile_message_id, false );

        // @Avid
		if ( 0 != $message['am_blast_id'] ) {
		}

		// Delete the mobile message
		$this->db->query( "DELETE FROM `mobile_messages` WHERE `mobile_message_id` = $mobile_message_id AND `website_id` = $website_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete mobile message.', __LINE__, __METHOD__ );
			return false;
		}

		// If a message did not get deleted
		if ( !$this->db->rows_affected )
			return true;

		// Delete mobile message associations
		$this->db->query( "DELETE FROM `mobile_message_associations` WHERE `mobile_message_id` = $mobile_message_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete mobile message associations.', __LINE__, __METHOD__ );
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

		$autoresponders = $this->db->get_results( "SELECT `mobile_autoresponder_id`, `name`, `default`, `date_created` FROM `mobile_autoresponders` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );

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
		$count = $this->db->get_var( "SELECT COUNT( `mobile_autoresponder_id` ) FROM `mobile_autoresponders` WHERE 1 $where" );

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
	 * @param int $mobile_autoresponder_id
	 * @return array
	 */
	public function get_autoresponder( $mobile_autoresponder_id ) {
		global $user;

        // Type Juggling
        $mobile_autoresponder_id = (int) $mobile_autoresponder_id;
        $website_id = (int) $user['website']['website_id'];

		$autoresponder = $this->db->get_row( "SELECT a.`mobile_autoresponder_id`, a.`mobile_list_id`, a.`name`, a.`message`, a.`default`, b.`name` AS mobile_list FROM `mobile_autoresponders` AS a LEFT JOIN `mobile_lists` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) WHERE a.`mobile_autoresponder_id` = $mobile_autoresponder_id AND a.`website_id` = $website_id", ARRAY_A );

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
	 * Get mobile settings
	 *
	 * @return array
	 */
	public function get_settings() {
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		// Get settings
		$settings = $this->db->get_results( "SELECT `key`, `value` FROM `mobile_settings` WHERE `website_id` = $website_id", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile settings.', __LINE__, __METHOD__ );
			return false;
		}

		return ( $settings ) ? ar::assign_key( $settings, 'key', true ) : array();
	}

	/**
	 * Get Mobile Setting
	 *
	 * @param string $key
	 * @return string
	 */
	public function get_setting( $key ) {
		global $user;

		$value = $this->db->prepare( 'SELECT `value` FROM `mobile_settings` WHERE `key` = ? AND `website_id` = ?', 'si', $key, $user['website']['website_id'] )->get_var('');

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile setting.', __LINE__, __METHOD__ );
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
     * Get the Avid Mobile Customer ID
     *
     * @param string $library
     * @return object
     */
    private function _get_am_lib( $library ) {
        if ( !$this->settings ) {
            // Now we need to get the site's mobile ID
            $w = new Websites();

            $this->settings = $w->get_settings( 'avid-mobile-customer-id', 'avid-mobile-username', 'avid-mobile-password' );
            $this->settings['avid-mobile-username'] = security::decrypt( base64_decode( $this->settings['avid-mobile-username'] ), ENCRYPTION_KEY );
            $this->settings['avid-mobile-password'] = security::decrypt( base64_decode( $this->settings['avid-mobile-password'] ), ENCRYPTION_KEY );
        }

        // We need this to go on
        if ( !$this->settings['avid-mobile-customer-id'] )
            return false;

        switch ( $library ) {
            case 'groups':
                library('avid-mobile/groups');

                $this->am_groups = new AM_Groups( $this->settings['avid-mobile-customer-id'], $this->settings['avid-mobile-username'], $this->settings['avid-mobile-password'] );

                return true;
            break;

            case 'keywords':
                library('avid-mobile/keywords');

                $this->am_keywords = new AM_Keywords( $this->settings['avid-mobile-customer-id'], $this->settings['avid-mobile-username'], $this->settings['avid-mobile-password'] );

                return true;
            break;

            default:
                return array( $this->settings['avid-mobile-customer-id'], $this->settings['avid-mobile-username'], $this->settings['avid-mobile-password'] );
            break;
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
     * @param bool $debug
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '', $debug = true ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method, $debug );
	}
}