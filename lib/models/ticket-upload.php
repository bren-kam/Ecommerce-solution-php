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
     * Get ticket uploads
     *
     * @param int $ticket_id
     * @return array
     */
    public function get_for_ticket( $ticket_id ) {
		return $this->prepare(
            'SELECT a.`key` FROM `ticket_uploads` AS a LEFT JOIN `ticket_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) WHERE b.`ticket_id` = :ticket_id'
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
    public function get_for_comments( $ticket_id ) {
        return $this->prepare(
            'SELECT a.`key`, b.`ticket_comment_id` FROM `ticket_uploads` AS a LEFT JOIN `ticket_comment_upload_links` AS b ON ( a.`ticket_upload_id` = b.`ticket_upload_id` ) LEFT JOIN `ticket_comments` AS c ON ( b.`ticket_comment_id` = c.`ticket_comment_id` ) WHERE c.`ticket_id` = :ticket_id'
            , 'i'
            , array( ':ticket_id' => $ticket_id )
        )->get_results( PDO::FETCH_CLASS, 'TicketUpload' );
    }
}
