<?php
class TicketComment extends ActiveRecordBase {
    const VISIBILITY_PRIVATE = 1;
    const VISIBILITY_PUBLIC = 0;

    // The columns we will have access to
    public $id, $ticket_comment_id, $ticket_id, $user_id, $to_address, $cc_address, $bcc_address, $comment, $private, $jira_id, $date_created;

    // Columns from other tables
    public $name, $email;

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
		$this->prepare( 'SELECT `ticket_comment_id`, `user_id`, `comment`, `private`, `date_created`, `jira_id`, `ticket_id` FROM `ticket_comments` WHERE `ticket_comment_id` = :ticket_comment_id'
            , 'i'
            , array( ':ticket_comment_id' => $ticket_comment_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->ticket_comment_id;
	}

    /**
	 * Get Comments
	 *
	 * @param int $ticket_id
	 * @return TicketComment[]
	 */
	public function get_by_ticket( $ticket_id ) {
		return $this->prepare( 'SELECT a.`ticket_comment_id`, a.`user_id`, a.`comment`, a.`private`, a.`date_created`, b.`contact_name` AS name, b.`email` AS email, a.`jira_id`, a.`ticket_id`, a.`to_address`, a.`cc_address`, a.`bcc_address` FROM `ticket_comments` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`ticket_id` = :ticket_id ORDER BY a.`date_created` DESC'
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
            , 'to_address' => $this->to_address
            , 'cc_address' => $this->cc_address
            , 'bcc_address' => $this->bcc_address
            , 'comment' => strip_tags($this->comment, '<br><a>')
            , 'private' => $this->private
            , 'jira_id' => $this->jira_id
            , 'date_created' => $this->date_created
        ), 'iisis' );

        $this->id = $this->ticket_comment_id = $this->get_insert_id();
    }

    public function save() {
        parent::update(
            [ 'jira_id' => $this->jira_id ]
            , [ 'ticket_comment_id' => $this->id ]
            , 'i'
            , 'i'
        );
    }

    /**
     * Delete the ticket comment
     */
    public function remove() {
        parent::delete( array( 'ticket_comment_id' => $this->id ), 'i' );
    }

    public function create_jira_comment() {
        library('jira');
        $jira = new Jira();

        $ticket = new Ticket();
        $ticket->get( $this->ticket_id );

        if ( !$ticket->jira_id ) {
            return false;
        }

        $comment_text  = "By *{$this->name}* on " . (new DateTime( $this->date_created ))->format( 'F j, Y g:ia' );
        $comment_text .= "\n\n" . strip_tags($this->comment);

        $ticket_upload = new TicketUpload();
        $uploads = $ticket_upload->get_by_comment( $this->id );
        if ( $uploads ) {
            $comment_text .= "\n\n--\n\n*Attachments*\n\n";
            foreach( $uploads as $upload ) {
                $comment_text .= "http://s3.amazonaws.com/retailcatalog.us/attachments/{$upload->key}\n";
            }
        }

        $comment_response = $jira->create_comment( $ticket->jira_id, [
            'body' => $comment_text
        ]);

        if ( $comment_response->id ) {
            $this->jira_id = $comment_response->id;
            $this->save();
            return true;
        }

        return false;
    }
}
