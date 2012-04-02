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
	 * Gets a specific checklist
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get( $website_id ) {
        // Type Juggling
		$website_id = (int) $website_id;
		
		$checklist_items_array = $this->db->get_results( "SELECT a.`name`, b.`checked`, b.`checklist_website_item_id`, c.`name` AS section FROM `checklist_items` AS a LEFT JOIN `checklist_website_items` AS b ON ( a.`checklist_item_id` = b.`checklist_item_id` ) LEFT JOIN `checklist_sections` AS c ON ( a.`checklist_section_id` = c.`checklist_section_id` ) LEFT JOIN `checklists` AS d ON ( b.`checklist_id` = d.`checklist_id`) WHERE a.`status` = 1 AND c.`status` = 1 AND d.`website_id` = $website_id GROUP BY a.`checklist_section_id`, b.`checklist_website_item_id` ORDER BY a.`sequence` ASC", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get checklist items.', __LINE__, __METHOD__ );
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
     * @param int $ticket_id
     * @param array $checklist_items
     * @return bool
     */
    public function complete_items( $ticket_id, $checklist_items ) {
        if ( !is_array( $checklist_items ) )
            return true;

        // Get the user
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
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
			$this->err( 'Failed to check checklist items.', __LINE__, __METHOD__ );
			return false;
		}

        // Add notes
        $this->db->query( "INSERT INTO `checklist_website_item_notes` ( `checklist_website_item_id`, `note`, `user_id`, `date_created` ) VALUES $values" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create checklist item notes.', __LINE__, __METHOD__ );
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
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}