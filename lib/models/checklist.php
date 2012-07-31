<?php
class Checklist extends ActiveRecordBase {
    // The columsn we will have access to
    public $id, $checklist_id;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'checklists' );
    }

    /**
     * Get Incomplete checklists
     *
     * @return array
     */
    public function get_incomplete() {
        $checklist_ids = $this->get_results( 'SELECT a.`checklist_id`, a.`website_id` FROM `checklists` AS a LEFT JOIN `checklist_website_items` AS b ON ( a.`checklist_id` = b.`checklist_id` ) WHERE b.`checked` = 0 GROUP BY `website_id`', PDO::FETCH_ASSOC );

		return ar::assign_key( $checklist_ids, 'website_id', true );
	}
}
