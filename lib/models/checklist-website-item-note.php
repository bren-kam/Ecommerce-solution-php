<?php
class ChecklistWebsiteItemNote extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $checklist_website_item_note_id, $checklist_website_item_id, $note, $user_id, $date_created;

    // Columns from other tables
    public $user;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'checklist_website_item_notes' );

        if ( isset( $this->checklist_website_item_note_id ) )
            $this->id = $this->checklist_website_item_note_id;
    }

    /**
     * Get
     *
     * @param int $checklist_website_item_note_id
     */
    public function get( $checklist_website_item_note_id ) {
        $this->prepare(
            'SELECT `checklist_website_item_note_id` FROM `checklist_website_item_notes` WHERE `checklist_website_item_note_id` = :checklist_website_item_note_id'
            , 'i'
            , array( ':checklist_website_item_note_id' => $checklist_website_item_note_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->checklist_website_item_note_id;
    }

    /**
     * Get by checklist website item
     *
     * @param int $checklist_website_item_id
     * @return array
     */
    public function get_by_checklist_website_item( $checklist_website_item_id ) {
		return $this->prepare(
            'SELECT cwin.`checklist_website_item_note_id`, cwin.`note`, u.`contact_name` AS user, cwin.`date_created` FROM `checklist_website_item_notes` AS cwin INNER JOIN `users` AS u ON ( cwin.`user_id` = u.`user_id` ) WHERE cwin.`checklist_website_item_id` = :checklist_website_item_id ORDER BY cwin.`date_created` DESC'
            , 'i'
            , array( ':checklist_website_item_id' => $checklist_website_item_id )
        )->get_results( PDO::FETCH_CLASS, 'ChecklistWebsiteItemNote' );
    }

    /**
     * Create note
     */
    public function create() {
        // Set date created
        $this->date_created = dt::now();

        $this->insert( array(
            'checklist_website_item_id' => $this->checklist_website_item_id
            , 'user_id' => $this->user_id
            , 'note' => $this->note
            , 'date_created' => $this->date_created
        ), 'iiss' );

        $this->id = $this->checklist_website_item_note_id = $this->get_insert_id();
    }

    /**
     * Delete
     */
    public function delete() {
        parent::delete( array( 'checklist_website_item_note_id' => $this->id ), 'i' );
    }
}
