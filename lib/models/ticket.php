<?php
class Ticket extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $ticket_id, $user_id, $assigned_to_user_id, $summary, $message, $name, $website, $assigned_to
        , $status, $priority, $browser_name, $browser_version, $browser_platform, $browser_user_agent, $date_created;

    // Fields from other tables
    public $role, $website_id, $domain, $email;

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
     * Get ticket
     */
    public function get( $ticket_id ) {
		$this->prepare( 'SELECT a.`ticket_id`, a.`user_id`, a.`assigned_to_user_id`, a.`summary`, a.`message`, a.`priority`, a.`status`, a.`browser_name`, a.`browser_version`, a.`browser_platform`, a.`date_created`, CONCAT( b.`contact_name` ) AS name, b.`email`, c.`website_id`, c.`title` AS website, c.`domain`, COALESCE( d.`role`, 7 ) AS role FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( a.`assigned_to_user_id` = d.`user_id` ) WHERE a.`ticket_id` = :ticket_id'
            , 'i'
            , array( ':ticket_id' => $ticket_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->ticket_id;
    }

    /**
     * Create a ticket
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'status' => $this->status
            , 'date_created' => $this->date_created
        ), 'is' );

        $this->id = $this->ticket_id = $this->get_insert_id();
    }

    /**
     * Add Links
     *
     * @param array $ticket_upload_ids
     */
    public function add_links( array $ticket_upload_ids ) {
        $values = '';

        foreach ( $ticket_upload_ids as &$tuid ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= '(' . $this->id . ',' . (int) $tuid . ')';
        }

        $this->query( "INSERT INTO `ticket_links` ( `ticket_id`, `ticket_upload_id` ) VALUES $values" );
    }

    /**
     * Update a ticket
     */
    public function update() {
        parent::update(
            array(
                'user_id' => $this->user_id
                , 'assigned_to_user_id' => $this->assigned_to_user_id
                , 'website_id' => $this->website_id
                , 'summary' => $this->summary
                , 'message' => $this->message
                , 'browser_name' => $this->browser_name
                , 'browser_version' => $this->browser_version
                , 'browser_platform' => $this->browser_platform
                , 'browser_user_agent' => $this->browser_user_agent
                , 'priority' => $this->priority
                , 'status' => $this->status
            )
            , array( 'ticket_id' => $this->id )
            , 'iiissssssis'
            , 'i'
        );
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

        return $this->prepare( "SELECT a.`ticket_id`, IF( 0 = a.`assigned_to_user_id`, 'Unassigned', c.`contact_name` ) AS assigned_to, a.`summary`, a.`status`, a.`priority`, a.`date_created`, b.`contact_name` AS name, b.`email`, d.`title` AS website FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN `websites` AS d ON ( a.`website_id` = d.`website_id` ) WHERE 1" . $where . " GROUP BY a.`ticket_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Ticket' );
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
        return $this->prepare( "SELECT COUNT( DISTINCT a.`ticket_id` ) FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` ) LEFT JOIN `websites` AS d ON ( a.`website_id` = d.`website_id` ) WHERE 1" . $where
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
     * Delete uncreated tickets (and dependencies)
     */
    public function deleted_uncreated_tickets() {
        // Delete ticket uploads, ticket links and tickets themselves (this is awesome!)
		$this->query( 'DELETE tu.*, tl.*, t.* FROM `ticket_uploads` AS tu LEFT JOIN `ticket_links` AS tl ON ( tl.`ticket_upload_id` = tu.`ticket_upload_id` ) LEFT JOIN `tickets` AS t ON ( t.`ticket_id` = tl.`ticket_id` ) WHERE t.`status` = -1 AND t.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )');
    }
}
