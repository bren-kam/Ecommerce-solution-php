<?php
class ChecklistSection extends ActiveRecordBase {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // The columns we will have access to
    public $id, $checklist_section_id, $name, $sequence, $status;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'checklist_sections' );

        if ( isset( $this->checklist_section_id ) )
            $this->id = $this->checklist_section_id;
    }

    /**
     * Get
     *
     * @param int $checklist_section_id
     */
    public function get( $checklist_section_id ) {
        $this->prepare(
            'SELECT `checklist_section_id`, `name`, `status` FROM `checklist_sections` WHERE `checklist_section_id` = :checklist_section_id'
            , 'i'
            , array( ':checklist_section_id' => $checklist_section_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->checklist_section_id;
    }

    /**
     * Get all
     *
     * @return array
     */
    public function get_all() {
        return $this->prepare(
            'SELECT `checklist_section_id`, `name` FROM `checklist_sections` WHERE `status` = :status ORDER BY `sequence` ASC'
            , 'i'
            , array( ':status' => self::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'ChecklistSection' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'status' => $this->status
        ), 'i' );

        $this->id = $this->checklist_section_id = $this->get_insert_id();
    }

    /**
     * Update
     */
    public function save() {
        parent::update( array(
            'name' => strip_tags($this->name)
            , 'sequence' => $this->sequence
            , 'status' => $this->status
        ) , array(
            'checklist_section_id' => $this->id
        ), 'sii', 'i' );
    }
}
