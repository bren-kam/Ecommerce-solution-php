<?php
class Ticket extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $ticket_id, $name, $priority, $website, $assigned_to, $summary, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'tickets' );

        // We want to make sure they match
        if ( isset( $this->ticket_id ) )
            $this->id = $this->ticket_id;
    }

   /**
	 * Get all information of the tickets
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        $tickets = $this->prepare( "SELECT a.`ticket_id`, IF( 0 = a.`assigned_to_user_id`, 'Unassigned', c.`contact_name` ) AS assigned_to, a.`summary`, a.`status`, a.`priority`, a.`date_created`, b.`contact_name` AS name, b.`email`, d.`title` AS website FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN `websites` AS d ON ( a.`website_id` = d.`website_id` ) WHERE 1" . $where . " GROUP BY a.`ticket_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Ticket' );

		return $tickets;
	}

	/**
	 * Count all the tickets
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        $count = $this->prepare( "SELECT COUNT( DISTINCT a.`ticket_id` ) FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN `websites` AS d ON ( a.`website_id` = d.`website_id` ) WHERE 1" . $where
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();

		return $count;
	}
}
