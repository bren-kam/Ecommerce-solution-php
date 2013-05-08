<?php
class TicketComment extends ActiveRecordBase {
    const VISIBILITY_PRIVATE = 1;
    const VISIBILITY_PUBLIC = 0;

    // The columns we will have access to
    public $id, $ticket_comment_id, $ticket_id, $user_id, $comment, $private, $date_created;

    // Hold the uploads
    public $uploads;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'ticket_comments' );

        // We want to make sure they match
        if ( isset( $this->ticket_comment_id ) )
            $this->id = $this->ticket_comment_id;
    }

    /**
	 * Get a Comment
	 *
	 * @param int $ticket_comment_id
	 */
	public function get( $ticket_comment_id ) {
		$this->prepare( 'SELECT `ticket_comment_id`, `user_id`, `comment`, `private`, `date_created` FROM `ticket_comments` WHERE `ticket_comment_id` = :ticket_comment_id'
            , 'i'
            , array( ':ticket_comment_id' => $ticket_comment_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->ticket_comment_id;
	}

    /**
	 * Get Comments
	 *
	 * @param int $ticket_id
	 * @return array
	 */
	public function get_by_ticket( $ticket_id ) {
		return $this->prepare( 'SELECT a.`ticket_comment_id`, a.`user_id`, a.`comment`, a.`private`, a.`date_created`, b.`contact_name` AS name FROM `ticket_comments` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`ticket_id` = :ticket_id ORDER BY a.`date_created` DESC'
            , 'i'
            , array( ':ticket_id' => $ticket_id )
        )->get_results( PDO::FETCH_CLASS, 'TicketComment' );
	}



    /**
     * Create
     */
    public function create() {
        // Set the time it was created
        $this->date_created = dt::now();

        $this->insert( array(
            'ticket_id' => $this->ticket_id
            , 'user_id' => $this->user_id
            , 'comment' => $this->comment
            , 'private' => $this->private
            , 'date_created' => $this->date_created
        ), 'iisis' );

        $this->id = $this->ticket_comment_id = $this->get_insert_id();
    }

    /**
     * Delete the ticket comment
     */
    public function delete() {
        parent::delete( array( 'ticket_comment_id' => $this->id ), 'i' );
    }
}
