<?php
/**
 * Handles all the Mobile Marketing
 *
 * Interact with Avid Mobile API wrapper
 * @package Grey Suit Retail
 * @since 1.0
 */
class Mobile_Marketing extends Base_Class {
    /**
     * Avid Mobile Customer ID
     */
    private $trumpia;

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

		$subscribers = $this->db->get_results( "SELECT `phone` FROM `mobile_subscribers` WHERE `website_id` = $website_id AND `status` = 1 ORDER BY `date_created` DESC LIMIT 5", ARRAY_A );
		
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
		$website_id = (int) $user['website']['website_id'];
		
		$subscriber = $this->db->get_row( "SELECT `mobile_subscriber_id`, `phone` FROM `mobile_subscribers` WHERE `mobile_subscriber_id` = $mobile_subscriber_id AND `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get subscriber.', __LINE__, __METHOD__ );
			return false;
		}

        // Get lists that this subscriber is subscribed to
		$mobile_lists = $this->db->get_results( "SELECT `mobile_list_id`, `trumpia_contact_id` FROM `mobile_associations` WHERE `mobile_subscriber_id` = $mobile_subscriber_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get mobile lists.', __LINE__, __METHOD__ );
			return false;
		}

        $subscriber['mobile_lists'] = ar::assign_key( $mobile_lists, 'mobile_list_id', true );

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

		$this->_init_trumpia();

        // First, get subscriber, update it, then update our own
        $subscriber = $this->get_subscriber( $mobile_subscriber_id );

        foreach( $subscriber['mobile_lists'] as $mobile_list_id => $trumpia_contact_id ) {
            if ( !$this->trumpia->delete_contact( $trumpia_contact_id ) )
                return false;
        }

		// Remove subscriber from lists
		$this->db->query( "DELETE a.* FROM `mobile_associations` AS a LEFT JOIN `mobile_lists` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) WHERE a.`mobile_subscriber_id` = $mobile_subscriber_id AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to remove subscriber from list.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Set the subscriber's status to "unsubscribed"
		$this->db->update( 'mobile_subscribers', array( 'status' => 0, 'date_unsubscribed' => dt::date('Y-m-d H:i:s') ), array( 'mobile_subscriber_id' => $mobile_subscriber_id ), 'is', 'i' );
		
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
     * @param array $mobile_lists
	 * @return int
	 */
	public function create_subscriber( $phone, $mobile_lists ) {
		global $user;
		
		// Update the subscriber if it already exists
		if ( $subscriber = $this->subscriber_exists( $phone ) )
			return $this->update_subscriber( $subscriber['mobile_subscriber_id'], $phone, $mobile_lists );

        // Initialize variables
        $new_lists = array();

        // Make sure it's instantiated
        $this->_init_trumpia();

         // Add new ones
        foreach ( $mobile_lists as $ml ) {
            // @Fix should find a way to do this better
            $mobile_list = $this->get_mobile_list( $ml );

            // Update the trumpia data
            $trumpia_contact_id = $this->trumpia->add_contact( 'Unknown', $this->_format_mobile_list_name( $mobile_list['name'] ), '', '', 1, $phone );

            if ( !$trumpia_contact_id )
                return false;

            $new_lists[$ml] = $trumpia_contact_id;
        }

        // Create the mobile subscriber
		$this->db->insert( 'mobile_subscribers', array( 'website_id' => $user['website']['website_id'], 'phone' => $phone, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create subscriber.', __LINE__, __METHOD__ );
			return false;
		}

        // Get the inserted variable
        $mobile_subscriber_id = $this->db->insert_id;

        // Update the mobile lists
        if ( !$this->update_mobile_lists_subscription( $mobile_subscriber_id, $new_lists ) )
            return false;
		
		return $mobile_subscriber_id;
	}
	
	/**
	 * Update a mobile subscription
	 *
	 * @param int $mobile_subscriber_id
	 * @param string $phone
     * @param array $mobile_lists
	 * @return bool
	 */
	public function update_subscriber( $mobile_subscriber_id, $phone, $mobile_lists ) {
		global $user;
		
        // First, get subscriber, update it, then update our own
        $subscriber = $this->get_subscriber( $mobile_subscriber_id, true );
        $old_lists = $new_lists = array();

		// Make sure it's instantiated
        $this->_init_trumpia();

        // Update/delete all trumpia list information
        foreach ( $subscriber['mobile_lists'] as $mobile_list_id => $trumpia_contact_id ) {
            if ( in_array( $mobile_list_id, $mobile_lists ) ) {
                 // Update the trumpia data
                if ( !$this->trumpia->update_contact_data( $trumpia_contact_id, 'Unknown', '', '', 1, $phone ) )
                    return false;
            } else {
                if ( !$this->trumpia->delete_contact( $trumpia_contact_id ) )
                    return false;

                $old_lists[] = $mobile_list_id;
            }
        }

        // Add new ones
        foreach ( $mobile_lists as $ml ) {
            if ( !array_key_exists( $ml, $subscriber['mobile_lists'] ) ) {
                // @Fix should find a way to do this better
                $mobile_list = $this->get_mobile_list( $ml );

                // Update the trumpia data
                $trumpia_contact_id = $this->trumpia->add_contact( $this->_format_mobile_list_name( $mobile_list['name'] ), '', '', '', 1, $phone );

                if ( !$trumpia_contact_id )
                    return false;

                $new_lists[$ml] = $trumpia_contact_id;
            }
        }
		
		$this->db->update( 'mobile_subscribers', array( 'phone' => $phone ), array( 'mobile_subscriber_id' => $mobile_subscriber_id, 'website_id' => $user['website']['website_id'] ), 's', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update subscriber.', __LINE__, __METHOD__ );
			return false;
		}

        // Update the mobile lists
        if ( !$this->update_mobile_lists_subscription( $mobile_subscriber_id, $new_lists, $old_lists ) )
            return false;
		
		return true;
	}
	
	/**
	 * Update mobile list subscriptions
	 * 
	 * @param int $mobile_subscriber_id
	 * @param array $new_lists [optional]
     * @param array $old_lists [optional]
	 * @return bool
	 */
	public function update_mobile_lists_subscription( $mobile_subscriber_id, $new_lists = NULL, $old_lists = NULL ) {
		// Type Juggling
		$mobile_subscriber_id = (int) $mobile_subscriber_id;

        // Make sure there is something to do
        if ( !is_array( $new_lists )  && !is_array( $old_lists ) )
            return false;

        // Delete old lists
        if ( is_array( $old_lists ) && count( $old_lists ) > 0 ) {
            foreach ( $old_lists as &$mlid ) {
                $mlid = (int) $mlid;
            }

            $this->db->query( "DELETE FROM `mobile_associations` WHERE `mobile_subscriber_id` = $mobile_subscriber_id AND `mobile_list_id` IN(" . implode( ',', $old_lists ) . ')' );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->err( 'Failed to delete mobile associations.', __LINE__, __METHOD__ );
                return false;
            }
        }
		
		// Add new values if they exist
		if ( is_array( $new_lists ) && count( $new_lists ) > 0 ) {
            $values = '';

			foreach ( $new_lists as $mobile_list_id => $trumpia_contact_id ){
				if ( !empty( $values ) )
					$values .= ',';

                // Type Juggling
                $mobile_list_id = (int) $mobile_list_id;
                $trumpia_contact_id = (int) $trumpia_contact_id;
				
				$values .= "( $mobile_subscriber_id, $mobile_list_id, $trumpia_contact_id )";
            }

			$this->db->query( "INSERT INTO `mobile_associations` ( `mobile_subscriber_id`, `mobile_list_id`, `trumpia_contact_id` )  VALUES $values" );
			
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

        $keywords = $this->db->get_results( "SELECT `mobile_keyword_id`, `keyword`, `response`, `date_created` FROM `mobile_keywords` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );

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
     * @param string $keyword
     * @param string $response
     * @param array $mobile_list_ids
     * @return int
     */
    public function create_keyword( $keyword, $response, $mobile_list_ids ) {
        global $user;

        // Get mobile lists
        $mobile_lists = $this->get_mobile_lists( false, $mobile_list_ids );

        // If there are no mobile lists there is nothing for us to do
        if ( !is_array( $mobile_lists ) )
            return false;

        // Initialize array
        $mobile_list_names = $mobile_list_ids = array();

        foreach ( $mobile_lists as $ml ) {
            $mobile_list_names[] = $this->_format_mobile_list_name( $ml['name'] );
            $mobile_list_ids[] = (int) $ml['mobile_list_id'];
        }

        // Make sure it's instantiated
        if ( !$this->_init_trumpia() )
            return false;

        // Create the keyword
        if ( !$this->trumpia->create_keyword( $keyword, implode( ',', $mobile_list_names ), TRUE, $response ) )
            return false;

        // Add the keyword to our database
        $this->db->insert( 'mobile_keywords', array(
            'website_id' => $user['website']['website_id']
            , 'keyword' => $keyword
            , 'response' => $response
            , 'date_created' => dt::date('Y-m-d H:i:s')
        ), 'isss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to create keyword.', __LINE__, __METHOD__ );
            return false;
        }

        // Get the keyword ID
        $mobile_keyword_id = $this->db->insert_id;

        // Now we need to connect the lists on our side
        if ( !$this->connect_mobile_keyword_to_lists( $mobile_keyword_id, $mobile_list_ids ) )
            return false;

        return $mobile_keyword_id;
    }

    /**
     * Update keyword
     *
     * @param int $mobile_keyword_id
     * @param string $response
     * @param array $mobile_list_ids
     * @return array
     */
    public function update_keyword( $mobile_keyword_id, $response, $mobile_list_ids ) {
        global $user;

        // Get mobile lists
        $mobile_lists = $this->get_mobile_lists( false, $mobile_list_ids );
        $keyword = $this->get_keyword( $mobile_keyword_id );

        // If there are no mobile lists there is nothing for us to do
        if ( !is_array( $mobile_lists ) )
            return false;

        // Initialize array
        $mobile_list_names = $mobile_list_ids = array();

        foreach ( $mobile_lists as $ml ) {
            $mobile_list_names[] = $this->_format_mobile_list_name( $ml['name'] );
            $mobile_list_ids[] = (int) $ml['mobile_list_id'];
        }

        // Make sure it's instantiated
        if ( !$this->_init_trumpia() )
            return false;

        // Create the keyword
        if ( !$this->trumpia->update_keyword( $keyword['keyword'], implode( ',', $mobile_list_names ), TRUE, $response ) )
            return false;

        // Add the keyword to our database
        $this->db->update( 'mobile_keywords', array(
            'response' => $response
        ), array(
              'mobile_keyword_id' => $mobile_keyword_id
            , 'website_id' => $user['website']['website_id']
        ), 's', 'ii' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to update keyword.', __LINE__, __METHOD__ );
            return false;
        }

        // Lets remove all connections then add them
        if ( !$this->remove_mobile_keyword_to_lists( $mobile_keyword_id ) )
            return false;

        // Now we need to connect the lists on our side
        if ( !$this->connect_mobile_keyword_to_lists( $mobile_keyword_id, $mobile_list_ids ) )
            return false;

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

        $keyword = $this->db->get_row( "SELECT `mobile_keyword_id`, `keyword`, `response` FROM `mobile_keywords` WHERE `mobile_keyword_id` = $mobile_keyword_id AND `website_id` = $website_id", ARRAY_A );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to get keyword.', __LINE__, __METHOD__ );
            return false;
        }

        // Get the lists
        $keyword['mobile_lists'] = $this->db->get_col( "SELECT `mobile_list_id` FROM `mobile_keyword_lists` WHERE `mobile_keyword_id` = $mobile_keyword_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to get mobile keyword lists.', __LINE__, __METHOD__ );
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

        // Get Variables
        $keyword = $this->get_keyword( $mobile_keyword_id );

         // Make sure it's instantiated
        if ( !$this->_init_trumpia() )
            return false;

        // Create the keyword
        if ( !$this->trumpia->delete_keyword( $keyword['keyword'] ) )
            return false;

        // Remove keyword
        $this->db->query( "DELETE FROM `mobile_keywords` WHERE `mobile_keyword_id` = $mobile_keyword_id AND `website_id` = $website_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to delete keyword.', __LINE__, __METHOD__ );
            return false;
        }

        // Now remove all the mobile lists that keyword was connected to
        if ( !$this->remove_mobile_keyword_to_lists( $mobile_keyword_id ) )
            return false;

        return true;
    }

    /**
     * Check keyword availability
     *
     * @param string $keyword
     * @return bool
     */
    public function check_keyword_availability( $keyword ) {
        // Make sure it's instantiated
        $this->_init_trumpia();

		// See if its available
		return $this->trumpia->check_keyword( $keyword );
    }

    /**
     * Connet Mobile Lists to Mobile Keyword
     *
     * @param int $mobile_keyword_id
     * @param array $mobile_list_ids
     * @return bool
     */
    public function connect_mobile_keyword_to_lists( $mobile_keyword_id, array $mobile_list_ids ) {
        // Variable initaliation
        $values = '';

        // Type Juggling
        $mobile_keyword_id = (int) $mobile_keyword_id;

        foreach ( $mobile_list_ids as $mlid ) {
            // Make sure it's comma separated
            if ( !empty( $values ) )
                $values .= ',';

            // Type Juggling
            $mlid = (int) $mlid;

            $values .= "( $mobile_keyword_id, $mlid )";
        }

        // Insert the connections
        $this->db->query( "INSERT INTO `mobile_keyword_lists` ( `mobile_keyword_id`, `mobile_list_id` ) VALUES $values" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to connect mobile keyword to lists.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
    }

    /**
     * Remove Mobile Keyword to List Connections
     *
     * @param int $mobile_keyword_id
     * @return bool
     */
    public function remove_mobile_keyword_to_lists( $mobile_keyword_id ) {
        // Type Juggling
        $mobile_keyword_id = (int) $mobile_keyword_id;

        // Delete the connections
        $this->db->query( "DELETE FROM `mobile_keyword_lists` WHERE `mobile_keyword_id` = $mobile_keyword_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to delete mobile keyword to lists connections.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
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

		$mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, a.`name`, a.`frequency`, a.`description`, a.`date_created`, COUNT( DISTINCT b.`mobile_subscriber_id`) AS count FROM `mobile_lists` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) LEFT JOIN `mobile_subscribers` AS c ON ( b.`mobile_subscriber_id` = c.`mobile_subscriber_id` ) WHERE ( c.`status` = 1 OR c.`status` IS NULL ) $where GROUP BY a.`mobile_list_id` $order_by LIMIT $limit", ARRAY_A );

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

		$mobile_list = $this->db->get_row( "SELECT `mobile_list_id`, `name`, `frequency`, `description` FROM `mobile_lists` WHERE `mobile_list_id` = $mobile_list_id AND `website_id` = $website_id", ARRAY_A );

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
     * @param array $mobile_list_ids
	 * @return array
	 */
	public function get_mobile_lists( $count = false, $mobile_list_ids = NULL ) {
		global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        if ( !is_null( $mobile_list_ids ) && is_array( $mobile_list_ids ) ) {
            // Make sure they're all integers
            foreach( $mobile_list_ids as &$mlid ) {
                $mlid = (int) $mlid;
            }

            $where = ' AND a.`mobile_list_id` IN(' . implode( ',', $mobile_list_ids ) . ')';
        } else {
            $where = '';
        }

		if ( $count ) {
		    $mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, a.`name`, COUNT( DISTINCT b.`mobile_subscription_id` ) AS count FROM `mobile_lists` AS a LEFT JOIN `mobile_associations` AS b ON ( a.`mobile_list_id` = b.`mobile_list_id` ) LEFT JOIN `mobile_subscribers` AS c ON ( b.`mobile_subscriber_id` = c.`mobile_subscriber_id` ) WHERE a.`website_id` = $website_id AND c.`status` = 1 $where GROUP BY a.`mobile_list_id` ORDER BY a.`name`", ARRAY_A );
		} else {
			$mobile_lists = $this->db->get_results( "SELECT a.`mobile_list_id`, a.`name` FROM `mobile_lists` AS a WHERE a.`website_id` = $website_id $where ORDER BY a.`name`", ARRAY_A );
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
     * @param int $frequency
     * @param string $description
	 * @return int
	 */
	public function create_mobile_list( $name, $frequency, $description ) {
		global $user;

        // Make sure it's instantiated
        $this->_init_trumpia();

        // Create a list
        if ( !$this->trumpia->create_list( $this->_format_mobile_list_name( $name ), $name, $frequency, $description ) )
            return false;

        // Create the dynamic list on our end
        $this->db->insert( 'mobile_lists', array(  'website_id' => $user['website']['website_id'], 'name' => $name, 'frequency' => $frequency, 'description' => $description, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isiss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to create dynamic mobile list.', __LINE__, __METHOD__ );
            return false;
        }

		return $this->db->insert_id;
	}

	/**
	 * Update mobile list
	 *
	 * @param int $mobile_list_id
	 * @param string $name
	 * @param int $frequency
     * @param string $description
	 * @return bool
	 */
	public function update_mobile_list( $mobile_list_id, $name, $frequency, $description ) {
		global $user;

        // Get the mobile list
        $mobile_list = $this->get_mobile_list( $mobile_list_id );

        // Make sure it's instantiated
        $this->_init_trumpia();

        // Rename list to a secondary list
        $this->trumpia->rename_list( $this->_format_mobile_list_name( $mobile_list['name'] ), $this->_format_mobile_list_name( $name ) . '2', $name, $frequency, $description );

        // Rename back to first list (won't let a normal rename happend)
        $this->trumpia->rename_list( $this->_format_mobile_list_name( $name ) . '2', $this->_format_mobile_list_name( $name ), $name, $frequency, $description );

        // Update the list
		$this->db->update( 'mobile_lists', array( 'name' => $name, 'frequency' => $frequency, 'description' => $description ), array( 'mobile_list_id' => $mobile_list_id, 'website_id' => $user['website']['website_id'] ), 'sis', 'ii' );

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

        // Initialize trumpia
        $this->_init_trumpia();

        // Delete Avoid Mobile Group
        if ( !$this->trumpia->delete_list( $this->_format_mobile_list_name( $mobile_list['name'] ) ) )
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
	
	/**
	 * Synchronize Subscribers from Mailchimp with our subscribers
	 *
	 * @return bool
	 */
	public function sync_subscribers_by_lists() {
		$mobile_lists = $this->get_mobile_lists();
		
		if ( !$mobile_lists || 0 == count( $mobile_lists ) )
			return false;
		
		// Make sure it's instantiated
        if ( !$this->_get_am_lib('groups') )
            return false;
		
		foreach ( $mobile_lists as $ml ) {
			$subscribers = $this->am_groups->list_members( $ml['am_group_id'] );
		}
	}

    /**
     * Format Mobile List Name
     *
     * @param string $name
     * @return string
     */
    private function _format_mobile_list_name( $name ) {
        return substr( preg_replace( '/[^a-zA-Z0-9]/', '', $name ), 0, 32 );
    }

	/***** MOBILE MESSAGES *****/

	/**
	 * Add a new message
	 *
     * @param string $title
	 * @param string $message
     * @param string $date_sent
     * @param array $mobile_list_ids
     * @param bool $future
	 * @return int
	 */
	public function create_message( $title, $message, $date_sent, $mobile_list_ids, $future ) {
        global $user;

        // Initialize variables
        $lists = '';

        // @Fix shouldn't look a query
        // Form List
        foreach ( $mobile_list_ids as $mlid ) {
            $mobile_list = $this->get_mobile_list( $mlid );

            if ( !empty( $lists ) )
                $lists .= ',';

            $lists .= $this->_format_mobile_list_name( $mobile_list['name'] );
        }

        // Make sure it's instantiated
        $this->_init_trumpia();

		// Send message
        if ( !$this->trumpia->send_to_list( false, false, true, false, $title, $lists, $future, $date_sent, '', '', '', ' ' . $message ) )
            return false;

		// Create the posting post
        $this->db->insert( 'mobile_messages', array( 'website_id' => $user['website']['website_id'], 'title' => $title, 'message' => $message, 'status' => $future, 'date_sent' => $date_sent, 'date_created' => dt::date('Y-m-d H:i:s') ), 'ississ' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to create the mobile message.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the mobile message ID
		$mobile_message_id = $this->db->insert_id;

		return $this->add_message_lists( $mobile_message_id, $mobile_list_ids );
	}

	/**
	 * Update a message
	 *
	 * @param int $mobile_message_id
     * @param string $title
	 * @param string $message
	 * @param string $date_sent
	 * @param array $mobile_list_ids
     * @param bool $future
	 * @return bool
	 */
	public function update_message( $mobile_message_id, $title, $message, $date_sent, $mobile_list_ids, $future ) {
		/*global $user;
		
		// Get the mobile list
        $message = $this->get_message( $mobile_message_id );
		
        // Make sure it's instantiated
        if ( !$this->_get_am_lib('blast') )
            return false;

		// Add other date
		$this->add_message_lists( $mobile_message_id, $mobile_list_ids );

		$this->db->update( 'mobile_messages', array( 'am_blast_id' => $am_blast_id, 'title' => $title, 'message' => $message, 'status' => $future, 'date_sent' => $date_sent ), array( 'website_id' => $user['website']['website_id'], 'mobile_message_id' => $mobile_message_id ), 'issis', 'ii' );

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


		return true;*/
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

	/***** OTHER FUNCTIONS *****/

    /**
     * Initiate Trumpia
     *
     * @return bool
     */
    private function _init_trumpia( ) {
        if ( $this->trumpia )
            return true;

        // Include the library
        library('trumpia');

        // Now we need to get the site's mobile ID
        $w = new Websites();

        // Get the API Key
        $api_key = $w->get_setting( 'trumpia-api-key' );

        // Setup Trumpia
        $this->trumpia = new Trumpia( $api_key );

        return true;
    }

	/** Mobile Pages **/
	public function update_mobile_pages( $page_data ) {
		global $user;
		
		$website_id = $user['website']['website_id'];

		// Get current pages
		$pages = $this->db->get_results( "SELECT a.`slug` FROM mobile_pages AS a WHERE a.`website_id` = " . $this->db->escape( $website_id ) . ";", ARRAY_A);
		
		// Reindex by page slugs
		$pages = ar::assign_key( $pages, 'slug' );
		
		foreach( $page_data as $slug => $content ) {
			// Page exists
			if ( array_key_exists( $slug, $pages ) )
				$result = $this->db->update( 'mobile_pages', array( 'content' => $content['content'], 'title' => $content['title'], 'updated_user_id' => $user['user_id'] ), array( 'slug' => $slug, 'website_id' => $website_id ), 'ssi', 'si' );
			else // Page should be created
				$result = $this->db->insert( 'mobile_pages', array( 'slug' => $slug, 'content' => $content['content'], 'title' => $content['title'], 'date_created' => dt::date('Y-m-d H:i:s'), 'website_id' => $website_id, 'status' => 1 ), 'ssssii' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to update mobile pages', __LINE__, __METHOD__ );
				return false;
			}
			
		}
		
		return true;
		
	}

	public function get_mobile_pages( $website_id = false ) {
		global $user;
		
		if ( !$website_id )
			$website_id = $user['website']['website_id'];
		
		$website_id = (int) $website_id;
		
		// Get current pages
		$pages = $this->db->get_results( "SELECT a.`mobile_page_id`, a.`slug`, a.`title`, a.`content`, a.`meta_title`, a.`meta_description`, a.`meta_keywords`, a.`status`, a.`updated_user_id`, a.`date_created`, a.`date_updated`  FROM mobile_pages AS a WHERE a.`website_id` = " . $this->db->escape( $website_id ) . ";", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update mobile pages', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( is_array( $pages) ) ? $pages : false;
	}
	
		
	/**
	 * Create Page
	 *
	 * Adds a page to a mobile website website if the user has permissions 7 or higher
	 *
	 * @param string $slug
	 * @param string $title
	 * @return bool
	 */
	public function create_mobile_page( $slug, $title ) {
		global $user;
		
		if ( $user['role'] < 7 )
			return false;
		
		// Insert the page
		$this->db->insert( 'mobile_pages', array( 'website_id' => $user['website']['website_id'], 'slug' => $slug, 'title' => $title, 'status' => 1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'issis' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create website page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
		
	/**
	 * Updates page information
	 *
	 * @param int $website_page_id
     * @param string $slug
     * @param string $title
	 * @param string $content
	 * @param string $meta_title
	 * @param string $meta_description
	 * @param string $meta_keywords
	 * @return bool
	 */
	public function update_mobile_page( $mobile_page_id, $slug, $title, $content, $meta_title, $meta_description, $meta_keywords ) {
		global $user;
		
		
		// Update existing request
		$this->db->update( 'mobile_pages', array( 'slug' => $slug, title => $title, 'content' => stripslashes($content), 'meta_title' => $meta_title, 'meta_description' => $meta_description, 'meta_keywords' => $meta_keywords, 'updated_user_id' => $user['user_id'] ), array( 'mobile_page_id' => $mobile_page_id, 'website_id' => $user['website']['website_id'] ), 'ssssssi', 'ii' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get check if request exists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * List Pages
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_pages( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$pages = $this->db->get_results( "SELECT `mobile_page_id`, `slug`, `title`, `status`, UNIX_TIMESTAMP( `date_updated` ) AS date_updated FROM `mobile_pages` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list pages.', __LINE__, __METHOD__ );
			return false;
		}
			
		return $pages;
	}
	
	/**
	 * Count Pages
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_pages( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `mobile_page_id` ) FROM `mobile_pages` WHERE 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count pages.', __LINE__, __METHOD__ );
			return false;
		}
			
		return $count;
	}
	
		/**
	 * Gets a specific page by the page_id
	 *
	 * @param int $website_page_id
	 * @return array
	 */
	public function get_mobile_page( $mobile_page_id ) {
		// Typecast
		$mobile_page_id = (int) $mobile_page_id;
		
		// Get the page
		$page = $this->db->get_row( "SELECT `mobile_page_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords` FROM `mobile_pages` WHERE `mobile_page_id` = $mobile_page_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get page.', __LINE__, __METHOD__ );
			return false;
		}
		
		// unencrypt data
		if ( is_array( $page ) )
		foreach ( $page as $k => $v ) {
			$new_page[$k] = html_entity_decode( $v, ENT_QUOTES, 'UTF-8' );
		}
		
		return $new_page;
	}
	
	
	/**
	 * Delete
	 *
	 * @param int $mobile_page_id
	 * @return bool
	 */
	public function delete_mobile_page( $mobile_page_id ) {
		global $user;
		
		// Must have the proper role
		if ( $user['role'] < 8 )
			return false;
		
		// Delete the website page
		$this->db->prepare( 'DELETE FROM `mobile_pages` WHERE `mobile_page_id` = ? AND `website_id` = ?', 'ii', $mobile_page_id, $user['website']['website_id'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website page.', __LINE__, __METHOD__ );
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
     * @param bool $debug
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '', $debug = true ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method, $debug );
	}
}