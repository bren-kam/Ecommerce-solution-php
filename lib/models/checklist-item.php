<?php
class ChecklistItem extends ActiveRecordBase {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // The columns we will have access to
    public $id, $checklist_item_id, $checklist_section_id, $name, $assigned_to, $sequence, $status;

    // Columns from other tables
    public $checked, $checklist_website_item_id, $notes_count, $section;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'checklist_items' );

        if ( isset( $this->checklist_item_id ) )
            $this->id = $this->checklist_item_id;
    }

    /**
     * Get
     *
     * @param int $checklist_item_id
     */
    public function get( $checklist_item_id ) {
        $this->prepare(
            'SELECT `checklist_item_id`, `checklist_section_id`, `name`, `assigned_to` FROM `checklist_items` WHERE `checklist_item_id` = :checklist_item_id'
            , 'i'
            , array( ':checklist_item_id' => $checklist_item_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->checklist_item_id;
    }

    /**
     * Get all
     *
     * @return ChecklistItem[]
     */
    public function get_all() {
        return $this->prepare(
            'SELECT `checklist_item_id`, `checklist_section_id`, `name`, `assigned_to` FROM `checklist_items` WHERE `status` = :status ORDER BY `sequence` ASC'
            , 'i'
            , array( ':status' => self::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'ChecklistItem' );
    }

    /**
     * Get by checklist
     *
     * @param int $checklist_id
     * @return array
     */
    public function get_by_checklist( $checklist_id ) {
		$checklist_items = $this->prepare(
            'SELECT ci.`checklist_item_id`, ci.`name`, ci.`assigned_to`, ci.`sequence`, cwi.`checked`, cwi.`checklist_website_item_id`, COUNT( cwin.`checklist_website_item_id` ) AS notes_count, cs.`name` AS section FROM `checklist_items` AS ci LEFT JOIN `checklist_website_items` AS cwi ON ( ci.`checklist_item_id` = cwi.`checklist_item_id` ) LEFT JOIN `checklist_website_item_notes` AS cwin ON ( cwi.`checklist_website_item_id` = cwin.`checklist_website_item_id` ) LEFT JOIN `checklist_sections` AS cs ON ( ci.`checklist_section_id` = cs.`checklist_section_id` ) WHERE ci.`status` = :status AND cwi.`checklist_id` = :checklist_id AND cs.`status` = :checklist_section_status GROUP BY ci.`checklist_section_id`, cwi.`checklist_website_item_id` ORDER BY ci.`sequence` ASC'
            , 'iii'
            , array( ':status' => self::STATUS_ACTIVE, ':checklist_id' => $checklist_id, ':checklist_section_status' => ChecklistSection::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'ChecklistItem' );

		return $checklist_items;
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'checklist_section_id' => $this->checklist_section_id
            , 'status' => $this->status
        ), 'ii' );

        $this->id = $this->checklist_item_id = $this->get_insert_id();
    }

    /**
     * Update
     */
    public function save() {
        parent::update(
            array(
                'name' => $this->name
                , 'assigned_to' => $this->assigned_to
                , 'sequence' => $this->sequence
                , 'status' => $this->status
            )
            , array( 'checklist_item_id' => $this->id )
            , 'ssii'
            , 'i'
        );
    }
}
