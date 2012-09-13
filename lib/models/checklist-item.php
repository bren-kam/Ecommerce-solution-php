<?php
class ChecklistItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $checklist_item_id, $name, $assigned_to, $sequence, $status;

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
     * Get by checklist
     *
     * @param int $checklist_id
     * @return array
     */
    public function get_by_checklist( $checklist_id ) {
		$checklist_items = $this->prepare(
            'SELECT ci.`checklist_item_id`, ci.`name`, ci.`assigned_to`, ci.`sequence`, cwi.`checked`, cwi.`checklist_website_item_id`, COUNT( cwin.`checklist_website_item_id` ) AS notes_count, cs.`name` AS section FROM `checklist_items` AS ci LEFT JOIN `checklist_website_items` AS cwi ON ( ci.`checklist_item_id` = cwi.`checklist_item_id` ) LEFT JOIN `checklist_website_item_notes` AS cwin ON ( cwi.`checklist_website_item_id` = cwin.`checklist_website_item_id` ) LEFT JOIN `checklist_sections` AS cs ON ( ci.`checklist_section_id` = cs.`checklist_section_id` ) WHERE ci.`status` = 1 AND cwi.`checklist_id` = :checklist_id AND cs.`status` = 1 GROUP BY ci.`checklist_section_id`, cwi.`checklist_website_item_id` ORDER BY ci.`sequence` ASC'
            , 'i'
            , array( ':checklist_id' => $checklist_id )
        )->get_results( PDO::FETCH_CLASS, 'ChecklistItem' );

		return $checklist_items;
    }
}
