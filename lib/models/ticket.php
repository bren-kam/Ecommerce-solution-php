<?php
class Ticket extends ActiveRecordBase {
    const PRIORITY_NORMAL = 0;
    const PRIORITY_HIGH = 1;
    const PRIORITY_URGENT = 2;
    const STATUS_OPEN = 0;
    const STATUS_CLOSED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_UNCREATED = -1;

    // The columns we will have access to
    public $id, $ticket_id, $user_id, $assigned_to_user_id, $user_id_created, $summary, $message, $name, $website, $assigned_to
        , $status, $priority, $browser_name, $browser_version, $browser_platform, $browser_user_agent, $date_created, $jira_id, $jira_key;

    // Fields from other tables
    public $role, $website_id, $domain, $email, $last_updated_at, $last_updated_by, $os_user_id, $os_user_name, $creator_name, $user_role;

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
		$this->prepare( 'SELECT a.`ticket_id`, a.`user_id`, a.`assigned_to_user_id`, a.`user_id_created`, a.`summary`, a.`message`, a.`priority`, a.`status`, a.`browser_name`, a.`browser_version`, a.`browser_platform`, a.`date_created`, b.`contact_name` AS name, b.`role` AS user_role, b.`email`, c.`website_id`, c.`title` AS website, c.`domain`, COALESCE( d.`role`, 7 ) AS role, a.`jira_id`, a.`jira_key`, MAX(tc.`date_created`) AS last_updated_at, tcu.`contact_name` AS last_updated_by, c.os_user_id, os_user.contact_name as os_user_name, users_created.contact_name as creator_name
                  FROM `tickets` AS a
                  LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` )
                  LEFT JOIN `users` AS d ON ( a.`assigned_to_user_id` = d.`user_id` )
                  LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` )
                  LEFT JOIN `users` AS os_user ON ( c.`os_user_id` = os_user.`user_id` )
                  LEFT JOIN ( SELECT `ticket_id`, MAX(`ticket_comment_id`) AS `ticket_comment_id` FROM `ticket_comments` GROUP BY `ticket_id` ) AS `last_tc` ON ( a.`ticket_id` = last_tc.`ticket_id` )
                  LEFT JOIN `ticket_comments` AS tc ON ( last_tc.`ticket_comment_id` = tc.`ticket_comment_id` )
                  LEFT JOIN `users` AS tcu ON ( tc.`user_id` = tcu.`user_id` )
                  LEFT JOIN `users` AS users_created ON ( a.`user_id_created` = users_created.`user_id` )
                  WHERE a.`ticket_id` = :ticket_id
                  GROUP BY a.ticket_id'
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
            'user_id' => $this->user_id
            , 'assigned_to_user_id' => $this->assigned_to_user_id
            , 'user_id_created' => $this->user_id_created
            , 'website_id' => $this->website_id
            , 'summary' => strip_tags($this->summary)
            , 'message' => $this->message
            , 'status' => $this->status
            , 'priority' => $this->priority
            , 'date_created' => $this->date_created
        ), 'iiissiis' );

        $this->id = $this->ticket_id = $this->get_insert_id();
    }

    /**
     * Update a ticket
     */
    public function save() {
        parent::update(
            array(
                'user_id' => $this->user_id
                , 'assigned_to_user_id' => $this->assigned_to_user_id
                , 'website_id' => $this->website_id
                , 'user_id_created' => $this->user_id_created
                , 'summary' => strip_tags($this->summary)
                , 'message' => $this->message
                , 'browser_name' => strip_tags($this->browser_name)
                , 'browser_version' => strip_tags($this->browser_version)
                , 'browser_platform' => strip_tags($this->browser_platform)
                , 'browser_user_agent' => strip_tags($this->browser_user_agent)
                , 'priority' => $this->priority
                , 'status' => $this->status
                , 'jira_id' => $this->jira_id
                , 'jira_key' => $this->jira_key
            )
            , array( 'ticket_id' => $this->id )
            , 'iiissssssiiis'
            , 'i'
        );
    }

    /**
	 * Get all information of the tickets
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return Ticket[]
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT a.`ticket_id`
                , IF( 0 = a.`assigned_to_user_id`, 'Unassigned', c.`contact_name` ) AS assigned_to
                , a.`user_id_created`
                , a.`assigned_to_user_id`
                , a.`summary`
                , a.`status`
                , a.`priority`
                , a.`date_created`
                , b.`contact_name` AS name
                , b.`email`
                , d.`title` AS website
                , COALESCE(MAX(tc.`date_created`), a.date_created) AS last_updated_at
                , tcu.`contact_name` AS last_updated_by
                , a.`jira_id`
                , a.`jira_key`
                , a.`message`
                , users_created.contact_name as creator_name
            FROM `tickets` AS a
            LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` )
            LEFT JOIN `users` AS c ON ( a.`assigned_to_user_id` = c.`user_id` )
            LEFT JOIN `users` AS users_created ON ( a.`user_id_created` = users_created.`user_id` )
            LEFT JOIN `websites` AS d ON ( a.`website_id` = d.`website_id` )
            LEFT JOIN ( SELECT `ticket_id`, MAX(`ticket_comment_id`) AS `ticket_comment_id` FROM `ticket_comments` GROUP BY `ticket_id` ) AS `last_tc` ON ( a.`ticket_id` = last_tc.`ticket_id` )
            LEFT JOIN `ticket_comments` AS tc ON ( last_tc.`ticket_comment_id` = tc.`ticket_comment_id` )
            LEFT JOIN `users` AS tcu ON ( tc.`user_id` = tcu.`user_id` )
            WHERE 1" . $where . "
            GROUP BY a.`ticket_id`
            $order_by
            LIMIT $limit"
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
		$this->prepare(
            'DELETE t.*, tu.* FROM `tickets` AS t LEFT JOIN `ticket_uploads` AS tu ON ( tu.`ticket_id` = t.`ticket_id` ) WHERE t.`status` = :status AND t.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )'
            , 'i'
            , array( ':status' => self::STATUS_UNCREATED )
        )->query();
    }

    /**
     * Get Old
     * @return Ticket[]
     */
    public function get_old() {
        return $this->prepare( 'SELECT a.`ticket_id`, a.`user_id`, a.`assigned_to_user_id`, a.`summary`, a.`priority`, a.`status`, a.`date_created`, CONCAT( b.`contact_name` ) AS name, b.`email`, c.`website_id`, c.`title` AS website, c.`domain`, COALESCE( d.`role`, 7 ) AS role, MAX( tc.date_created ) AS last_comment FROM `tickets` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( a.`assigned_to_user_id` = d.`user_id` ) LEFT JOIN `ticket_comments` AS tc ON ( tc.`ticket_id` = a.`ticket_id` ) WHERE a.`status` = 0 GROUP BY a.`ticket_id` HAVING MAX( tc.`date_created` ) < ( NOW() - INTERVAL 50 DAY ) OR ( MAX( tc.`date_created` ) IS NULL AND a.`date_created` < ( NOW() - INTERVAL 50 DAY ) )'
            , ''
            , array()
        )->get_results( PDO::FETCH_CLASS, 'Ticket' );
    }

    /**
     * Create Jira Issue
     * @return bool
     */
    public function create_jira_issue() {
        library('jira');
        $user = new User();
        $user->get($this->user_id);
        $assigned_user = new User();
        $assigned_user->get($this->assigned_to_user_id);
        $jira = new Jira( $user->jira_username, $user->jira_password );

        $ticket_description  = "*Priority*: {$priorities[$this->priority]}";
        if($this->website_id) {
            $ticket_description .= "\n*Control Account*: http://admin.greysuitretail.com/accounts/control/?aid={$this->website_id}";
            $ticket_description .= "\n*Edit Account*: http://admin.greysuitretail.com/accounts/edit/?aid={$this->website_id}";
        }
        $ticket_description .= "\n\n--\n\n*Message*\n\n" . str_replace( '<br />', "\n", $this->message );

        $ticket_upload = new TicketUpload();
        $uploads = $ticket_upload->get_by_ticket( $this->id );
        if ( $uploads ) {
            $ticket_description .= "\n\n--\n\n*Attachments*\n\n";
            foreach( $uploads as $upload ) {
                $ticket_description .= "http://s3.amazonaws.com/retailcatalog.us/attachments/{$upload}\n";
            }
        }

        $issue_response = $jira->create_issue([
            'fields' => [
                'project' => [ 'key' => 'TIC' ]  // Project Tickets (TIC)
                , 'reporter' => [ 'name' => 'jack napier' ]  // Reporter
                , 'issuetype' => [ 'id' => 1 ]  // Bug
                , 'assignee' => $assigned_user->jira_username
                , 'summary' => "#{$this->id}: {$this->summary}"
                , 'description' => $ticket_description
            ]
        ]);

        if ( $issue_response->id ) {
            $this->jira_id = $issue_response->id;
            $this->jira_key = $issue_response->key;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Update Jira Issue
     * @return bool
     */
    public function update_jira_issue() {
        library('jira');
        $jira = new Jira();

        $issue_response = $jira->update_issue_status( $this->jira_id, 33 );  // In Progress

        return false;
    }

}
