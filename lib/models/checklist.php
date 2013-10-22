<?php
class Checklist extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $checklist_id, $website_id, $type, $date_created, $days_left;

    // Columns from other tables
    public $title, $online_specialist;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'checklists' );

        if ( isset( $this->checklist_id ) )
            $this->id = $this->checklist_id;
    }

    /**
     * Get
     *
     * @param int $checklist_id
     */
    public function get( $checklist_id ) {
        $this->prepare(
            'SELECT c.`checklist_id`, c.`website_id`, c.`type`, c.`date_created`, w.`title`, DATEDIFF( DATE_ADD( c.`date_created`, INTERVAL 30 DAY ), NOW() ) AS days_left FROM `checklists` AS c LEFT JOIN `websites` AS w ON ( c.`website_id` = w.`website_id` ) WHERE c.`checklist_id` = :checklist_id ORDER BY days_left ASC'
            , 'i'
            , array( ':checklist_id' => $checklist_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->checklist_id;
    }

    /**
     * Create Checklist
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'type' => strip_tags($this->type)
            , 'date_created' => $this->date_created
        ), 'iss' );

        $this->checklist_id = $this->id = $this->get_insert_id();
    }

    /**
     * Get Incomplete checklists
     *
     * @return array
     */
    public function get_incomplete() {
        $checklist_ids = $this->get_results( 'SELECT c.`checklist_id`, c.`website_id` FROM `checklists` AS c LEFT JOIN `checklist_website_items` AS cwi ON ( cwi.`checklist_id` = c.`checklist_id` ) LEFT JOIN `checklist_items` AS ci ON ( ci.`checklist_item_id` = cwi.`checklist_item_id` ) WHERE cwi.`checked` = 0 AND ci.`status` = 1 GROUP BY c.`website_id`', PDO::FETCH_ASSOC );

		return ar::assign_key( $checklist_ids, 'website_id', true );
	}

    /**
	 * Get all information of the checklists
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare( "SELECT c.`checklist_id`, c.`type`, c.`date_created`, w.`title`, u2.`contact_name` AS 'online_specialist', DATEDIFF( DATE_ADD( c.`date_created`, INTERVAL 30 DAY ), NOW() ) AS 'days_left' FROM `checklists` AS c LEFT JOIN `websites` AS w ON ( w.`website_id` = c.`website_id` ) INNER JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = w.`os_user_id` ) WHERE w.`status` = 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Checklist' );
	}

	/**
	 * Count all the checklists
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( c.`checklist_id` ) FROM `checklists` AS c LEFT JOIN `websites` AS w ON ( w.`website_id` = c.`website_id` ) INNER JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = w.`os_user_id` ) WHERE w.`status` = 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}