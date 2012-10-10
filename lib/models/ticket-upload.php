<?php
class TicketUpload extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $ticket_upload_id, $key, $date_created;

    // Other tables
    public $ticket_comment_id;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'ticket_uploads' );

        // We want to make sure they match
        if ( isset( $this->ticket_upload_id ) )
            $this->id = $this->ticket_upload_id;
    }

    /**
     * Get ticket upload
     *
     * @param int $ticket_upload_id
     */
    public function get( $ticket_upload_id ) {
		$this->prepare(
            'SELECT `ticket_upload_id`, `key` FROM `ticket_uploads` WHERE `ticket_upload_id` = :ticket_upload_id'
            , 'i'
            , array( ':ticket_upload_id' => $ticket_upload_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->ticket_upload_id;
    }

    /**
     * Get ticket uploads
     *
     * @param int $ticket_id
     * @return array
     */
    public function get_by_ticket( $ticket_id ) {
		return $this->prepare(
            'SELECT tu.`key` FROM `ticket_uploads` AS tu LEFT JOIN `ticket_links` AS tl ON ( tu.`ticket_upload_id` = tl.`ticket_upload_id` ) WHERE tl.`ticket_id` = :ticket_id'
            , 'i'
            , array( ':ticket_id' => $ticket_id )
        )->get_col();
    }

    /**
     * Get ticket uploads for comments
     *
     * @param int $ticket_id
     * @return array
     */
    public function get_by_comments( $ticket_id ) {
        return $this->prepare(
            'SELECT tu.`key`, tcul.`ticket_comment_id` FROM `ticket_uploads` AS tu LEFT JOIN `ticket_comment_upload_links` AS tcul ON ( tcul.`ticket_upload_id` = tu.`ticket_upload_id` ) LEFT JOIN `ticket_comments` AS tc ON ( tc.`ticket_comment_id` = tcul.`ticket_comment_id` ) WHERE tc.`ticket_id` = :ticket_id'
            , 'i'
            , array( ':ticket_id' => $ticket_id )
        )->get_results( PDO::FETCH_CLASS, 'TicketUpload' );
    }

    /**
     * Get uploads for a comment
     *
     * @param int $ticket_comment_id
     * @return array
     */
    public function get_by_comment( $ticket_comment_id ) {
        return $this->prepare(
            'SELECT tu.`key`, tcul.`ticket_comment_id` FROM `ticket_uploads` AS tu LEFT JOIN `ticket_comment_upload_links` AS tcul ON ( tcul.`ticket_upload_id` = tu.`ticket_upload_id` ) WHERE tcul.`ticket_comment_id` = :ticket_comment_id'
            , 'i'
            , array( ':ticket_comment_id' => $ticket_comment_id )
        )->get_results( PDO::FETCH_CLASS, 'TicketUpload' );
    }

    /**
     * Get uploads for uncreated tickets
     *
     * @return array
     */
    public function get_keys_by_uncreated_tickets() {
        return $this->get_col( 'SELECT tu.`key` FROM `ticket_uploads` AS tu LEFT JOIN `ticket_links` AS tl ON ( tl.`ticket_upload_id` = tu.`ticket_upload_id` ) LEFT JOIN `tickets` AS t ON ( t.`ticket_id` = tl.`ticket_id` ) WHERE t.`status` = -1 AND t.`date_created` < DATE_SUB( CURRENT_TIMESTAMP, INTERVAL 1 HOUR )' );
    }


    /**
     * Create
     */
    public function create() {
        $this->insert(
            array(
                'key' => $this->key
                , 'date_created' => dt::now()
            )
            , 'ss'
        );

        $this->id = $this->ticket_upload_id = $this->get_insert_id();
    }

    /**
     * Delete
     */
    public function delete() {
        parent::delete( array( 'ticket_upload_id' => $this->id ), 'i' );
    }
}
