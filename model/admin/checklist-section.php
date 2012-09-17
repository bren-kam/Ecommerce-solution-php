<?php
class ChecklistSection extends ActiveRecordBase {
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
     * Get all
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( 'SELECT `checklist_section_id`, `name` FROM `checklist_sections` WHERE `status` = 1 ORDER BY `sequence` ASC', PDO::FETCH_CLASS, 'ChecklistSection' );
    }

    /**
     * Update
     */
    public function update() {
        parent::update(
            array(
                'name' => $this->name
                , 'sequence' => $this->sequence
                , 'status' => $this->status
            )
            , array( 'checklist_section_id' => $this->id )
            , 'sii'
            , 'i'
        );
    }
}
