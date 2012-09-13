<?php

/**
 * Handles all the checklists
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Checklists extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}

    /**
     * Get accounts that have no checklists
     *
     * @return array
     */
    public function get_unchecklisted_accounts() {
        $accounts = $this->db->get_results( "SELECT a.`website_id`, a.`title` FROM `websites` AS a LEFT JOIN `checklists` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`status` = 1 AND b.`website_id` IS NULL", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get unlinked craigslist accounts.', __LINE__, __METHOD__ );
			return false;
		}

        return $accounts;
    }

    /**
     * Add Checklist to a website
     *
     * @param int $website_id
     * @return int
     */
    public function add_checklist( $website_id ) {
        // Get the date for today
        $today = new DateTime();

        $this->db->insert( 'checklists', array( 'website_id' => $website_id, 'type' => 'Website Setup', 'date_created' => $today->format('Y-m-d H:i:s') ), 'iss' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add checklist to website.', __LINE__, __METHOD__ );
			return false;
		}

        // Checklist ID
        $checklist_id = $this->db->insert_id;

        // Insert all the checklist items
        $this->db->query( "INSERT INTO `checklist_website_items` ( `checklist_id`, `checklist_item_id` ) SELECT $checklist_id, `checklist_item_id` FROM `checklist_items` WHERE `status` = 1" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add website checklists items.', __LINE__, __METHOD__ );
			return false;
		}

        return $checklist_id;
    }

    /**
     * Create a section that is set as inactive
     *
     * @return bool
     */
    public function create_section() {
        $this->db->insert( 'checklist_sections', array( 'name' => '', 'sequence' => 0, 'status' => 0 ), 'sii' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create section.', __LINE__, __METHOD__ );
			return false;
		}

        return $this->db->insert_id;
    }

    /**
     * Update the sections name and sequence
     *
     * @param array $sections
     * @return bool
     */
    public function update_sections( $sections ) {
        // Prepare statement
		$statement = $this->db->prepare( "UPDATE `checklist_sections` SET `name` = ?, `sequence` = ?, `status` = 1 WHERE `checklist_section_id` = ?" );
		$statement->bind_param( 'sii', $name, $sequence, $checklist_section_id );

        $sequence = 0;

        if ( is_array( $sections ) )
		foreach ( $sections as $checklist_section_id => $name ) {
			$statement->execute();
            echo $this->db->error;
			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->_err( 'Failed to update checklist sections', __LINE__, __METHOD__ );
				return false;
			}

            // Update the sequence
            $sequence++;
		}

        return true;
    }

    /**
     * Removes a block of sections
     *
     * @param array $section_ids
     * @return bool
     */
    public function remove_sections( $section_ids ) {
        // Make sure it's an array
        if ( !is_array( $section_ids ) || 0 == count( $section_ids ) )
            return true;

        // Type juggling for the array
        foreach ( $section_ids as &$sid ) {
            $sid = (int) $sid;
        }

        $this->db->query( 'UPDATE `checklist_sections` SET `status` = 0 WHERE `checklist_section_id` IN (' . implode( ',', $section_ids ) . ')' );

         // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to remove checklist sections.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
     * Create an item that is set as inactive
     *
     * @param int $section_id
     * @return bool
     */
    public function create_item( $section_id ) {
        $this->db->insert( 'checklist_items', array( 'checklist_section_id' => $section_id, 'name' => '', 'assigned_to' => '', 'sequence' => 0, 'status' => 0 ), 'issii' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create item.', __LINE__, __METHOD__ );
			return false;
		}

        return $this->db->insert_id;
    }

    /**
     * Update the items name, assigned-to and sequence
     *
     * @param array $items
     * @return bool
     */
    public function update_items( $items ) {
        // Prepare statement
		$statement = $this->db->prepare( "UPDATE `checklist_items` SET `name` = ?, `assigned_to` = ?, `sequence` = ?, `status` = 1 WHERE `checklist_item_id` = ?" );
		$statement->bind_param( 'ssii', $name, $assigned_to, $sequence, $checklist_item_id );

        $sequence = 0;

        if ( is_array( $items ) )
		foreach ( $items as $item_array ) {
            if ( is_array( $item_array ) )
            foreach ( $item_array as $checklist_item_id => $item ) {
                $name = $item['name'];
                $assigned_to = $item['assigned_to'];

                $statement->execute();

                // Handle any error
                if ( $statement->errno ) {
                    $this->db->m->error = $statement->error;
                    $this->_err( 'Failed to update checklist items', __LINE__, __METHOD__ );
                    return false;
                }

                // Update the sequence
                $sequence++;
            }
		}

        return true;
    }

    /**
     * Removes a block of items
     *
     * @param array $item_ids
     * @return bool
     */
    public function remove_items( $item_ids ) {
        // Make sure it's an array
        if ( !is_array( $item_ids ) || 0 == count( $item_ids ) )
            return true;

        // Type juggling for the array
        foreach ( $item_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $this->db->query( 'UPDATE `checklist_items` SET `status` = 0 WHERE `checklist_item_id` IN (' . implode( ',', $item_ids ) . ')' );

         // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to remove checklist items.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

	/**
	 * Get all information of the checklists
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_checklists( $where, $order_by, $limit ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND c.`company_id` = ' . $user['company_id'] : $where . ' AND c.`company_id` = ' . $user['company_id'];
		
		// Get the checklists
		$checklists = $this->db->get_results( "SELECT a.`checklist_id`, a.`type`, a.`date_created`, b.`title`, d.`contact_name` AS 'online_specialist', DATEDIFF( DATE_ADD( a.`date_created`, INTERVAL 30 DAY ), NOW() ) AS 'days_left' FROM `checklists` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) INNER JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) LEFT JOIN `users` AS d ON ( b.`os_user_id` = d.`user_id` ) WHERE b.`status` = 1 $where ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list checklists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $checklists;
	}
	
	/**
	 * Count all the checklists
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_checklists( $where ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND c.`company_id` = ' . $user['company_id'] : $where . ' AND c.`company_id` = ' . $user['company_id'];
		
		// Get the checklist count
		$checklist_count = $this->db->get_var( "SELECT COUNT( a.`checklist_id` ) FROM `checklists` AS a LEFT JOIN `websites` AS b ON a.`website_id` = b.`website_id` INNER JOIN `users` AS c ON ( b.`user_id` = c.`user_id` ) WHERE b.`status` = 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count checklists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $checklist_count;
	}
	
	/**
	 * incomplete_checklists
	 *
	 * Returns a list of websites that have incomplete checklists
	 *
	 * @return array
	 */
	public function incomplete_checklists() {
		$website_ids = $this->db->get_results( 'SELECT a.`checklist_id`, a.`website_id` FROM `checklists` AS a LEFT JOIN `checklist_website_items` AS b ON ( a.`checklist_id` = b.`checklist_id` ) WHERE b.`checked` = 0 GROUP BY `website_id`', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get incomplete checklists.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ar::assign_key( $website_ids, 'website_id', true );
	}
	
	/**
	 * Gets a specific checklist
	 *
	 * @param int $checklist_id
	 * @return array
	 */
	public function get( $checklist_id ) {
		$checklist_id = (int) $checklist_id;
		
		$checklist = $this->db->get_row( "SELECT a.`checklist_id`, a.`website_id`, a.`type`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, b.`title`, DATEDIFF( DATE_ADD( a.`date_created`, INTERVAL 30 DAY), NOW() ) AS 'days_left' FROM `checklists` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`checklist_id` = $checklist_id ORDER BY days_left ASC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get checklist.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $checklist;
	}
	
	/**
	 * Get Items List for Checklist
	 *
	 * @param int $checklist_id
	 * @return array
	 */
	public function get_checklist_items( $checklist_id ) {
        // Type Juggling
        $checklist_id = (int) $checklist_id;

		$checklist_items_array = $this->db->get_results( "SELECT a.`checklist_item_id`, a.`name`, a.`assigned_to`, a.`sequence`, b.`checked`, b.`checklist_website_item_id`, COUNT( c.`checklist_website_item_id` ) AS notes_count, d.`name` AS section FROM `checklist_items` AS a LEFT  JOIN `checklist_website_items` AS b ON ( a.`checklist_item_id` = b.`checklist_item_id` ) LEFT JOIN `checklist_website_item_notes` AS c ON ( b.`checklist_website_item_id` = c.`checklist_website_item_id` ) LEFT JOIN `checklist_sections` AS d ON ( a.`checklist_section_id` = d.`checklist_section_id` ) WHERE a.`status` = 1 AND b.`checklist_id` = $checklist_id AND d.`status` = 1 GROUP BY a.`checklist_section_id`, b.`checklist_website_item_id` ORDER BY a.`sequence` ASC", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get checklist items.', __LINE__, __METHOD__ );
			return false;
		}

        // Put all the items in the proper place
        $checklist_items = array();

        if ( is_array( $checklist_items_array ) )
        foreach ( $checklist_items_array as $ci ) {
            $checklist_items[$ci['section']][] = $ci;
        }

		return $checklist_items;
	}

    /**
	 * Gets a specific checklist
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_checklist_items_by_website( $website_id ) {
        // Type Juggling
		$website_id = (int) $website_id;

		$checklist_items_array = $this->db->get_results( "SELECT a.`name`, b.`checked`, b.`checklist_website_item_id`, c.`name` AS section FROM `checklist_items` AS a LEFT JOIN `checklist_website_items` AS b ON ( a.`checklist_item_id` = b.`checklist_item_id` ) LEFT JOIN `checklist_sections` AS c ON ( a.`checklist_section_id` = c.`checklist_section_id` ) LEFT JOIN `checklists` AS d ON ( b.`checklist_id` = d.`checklist_id`) WHERE a.`status` = 1 AND c.`status` = 1 AND d.`website_id` = $website_id GROUP BY a.`checklist_section_id`, b.`checklist_website_item_id` ORDER BY a.`sequence` ASC", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get checklist items.', __LINE__, __METHOD__ );
			return false;
		}

        // Put all the items in the proper place
        $checklist_items = array();

        if ( is_array( $checklist_items_array ) )
        foreach ( $checklist_items_array as $ci ) {
            $checklist_items[$ci['section']][] = $ci;
        }

		return $checklist_items;
	}

    /**
     * Complete checklist items in bulk
     *
     * @param int $website_id
     * @param int $ticket_id
     * @param array $checklist_items
     * @return bool
     */
    public function complete_items( $website_id, $ticket_id, $checklist_items ) {
        if ( !is_array( $checklist_items ) )
            return true;

        // Get the user
        global $user;

        // Type Juggling
        $website_id = (int) $website_id;
        $user_id = (int) $user['user_id'];
        $ticket_id = (int) $ticket_id;

        // Declare variables
        $values = '';
        $ticket_link = $this->db->escape( '<a href="/tickets/ticket/?tid=' . $ticket_id . '" title="/tickets/?tid=' . $ticket_id . '" target="_blank">Ticket #' . $ticket_id . '</a>' );

        // Type juggle the array
        foreach ( $checklist_items as &$ci ) {
            $ci = (int) $ci;

            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $ci, '$ticket_link', $user_id, NOW() )";
        }

        $this->db->query( 'UPDATE `checklist_website_items` AS a LEFT JOIN `checklists` AS b ON ( a.`checklist_id` = b.`checklist_id` ) SET a.`checked` = 1 WHERE a.`checklist_website_item_id` IN(' . implode( ',', $checklist_items ) . ") AND b.`website_id` = $website_id" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to check checklist items.', __LINE__, __METHOD__ );
			return false;
		}

        // Add notes
        $this->db->query( "INSERT INTO `checklist_website_item_notes` ( `checklist_website_item_id`, `note`, `user_id`, `date_created` ) VALUES $values" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create checklist item notes.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
	 * Get Sections List
	 *
	 * @return array
	 */
	public function get_sections() {
		$checklist_sections = $this->db->get_results( "SELECT `checklist_section_id`, `name` FROM `checklist_sections` WHERE `status` = 1 ORDER BY `sequence` ASC", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get checklist items.', __LINE__, __METHOD__ );
			return false;
		}

		return $checklist_sections;
	}

    /**
	 * Get Items List
	 *
	 * @return array
	 */
	public function get_items() {
		$checklist_items_array = $this->db->get_results( "SELECT `checklist_item_id`, `checklist_section_id`, `name`, `assigned_to` FROM `checklist_items` WHERE `status` = 1 ORDER BY `sequence` ASC", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get checklist items.', __LINE__, __METHOD__ );
			return false;
		}

        // Put all the items in the proper place
        $checklist_items = array();
        
        if ( is_array( $checklist_items_array ) )
        foreach ( $checklist_items_array as $ci ) {
            $checklist_items[$ci['checklist_section_id']][] = $ci;
        }

		return $checklist_items;
	}
	
	/**
	 * Adds a note to a checklist website item
	 *
	 * @param int $checklist_website_item_id
	 * @param string $note
	 * @return bool
	 */
	public function add_note( $checklist_website_item_id, $note ) {
		global $user;
		
		$this->db->insert( 'checklist_website_item_notes', array( 'checklist_website_item_id' => $checklist_website_item_id, 'user_id' => $user['user_id'], 'note' => $note, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add checklist item.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update note
	 *
	 * @param int $checklist_website_item_note_id
	 * @param string $note
	 * @return bool
	 */
	public function update_note( $checklist_website_item_note_id, $note ){
		$this->db->update( 'checklist_website_item_notes', array( 'note' => $note ), array( 'checklist_website_item_note_id' => $checklist_website_item_note_id ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update website item note.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update checklist item
	 *
	 * @param int $checklist_website_item_id
	 * @param bool $state whether checked or not
	 * @return bool
	 */
	public function update_item( $checklist_website_item_id, $state ) {
		$state = ( $state == 'true' ) ? 1 : 0;

		$this->db->update( 'checklist_website_items', array( 'checked' => $state, 'date_checked' => dt::date('Y-m-d H:i:s') ), array( 'checklist_website_item_id' => $checklist_website_item_id ), 'is', 'i' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update website item.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}
	
	/**
	 * Get's all the notes relating to a item_id
	 *
	 * @param int $item_id 
	 * @return array
	 */
	public function get_notes( $item_id ) {
		$notes = $this->db->get_results( "SELECT a.`checklist_website_item_note_id`, a.`note`, b.`contact_name`, a.`date_created` FROM `checklist_website_item_notes` AS a INNER JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`checklist_website_item_id` = $item_id ORDER BY `date_created` DESC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get notes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $notes;
	}
	
	/**
	 * Delete a note from the checklist item
	 * 
	 * @param int $checklist_website_item_note_id
	 * @return bool
	 */
	public function delete_note( $checklist_website_item_note_id ){
		$this->db->query( 'DELETE FROM `checklist_website_item_notes` WHERE `checklist_website_item_note_id` = ' . (int) $checklist_website_item_note_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete checklist note.', __LINE__, __METHOD__ );
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