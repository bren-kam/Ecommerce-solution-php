<?php
class KnowledgeBasePage extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $kb_category_id, $name;

    // Artificial column
    public $category;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'kb_page' );
    }

    /**
     * Get
     *
     * @param int $id
     */
    public function get( $id ) {
        $this->prepare(
            'SELECT `id`, `kb_category_id`, `name` FROM `kb_page` WHERE `id` = :id'
            , 'i'
            , array( ':id' => $id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get by category
     *
     * @param int $kb_category_id
     * @return KnowledgeBasePage[]
     */
    public function get_by_category( $kb_category_id ) {
        return $this->prepare(
            'SELECT `id`, `kb_category_id`, `name` FROM `kb_page` WHERE `kb_category_id` = :kb_category_id'
            , 'i'
            , array( ':kb_category_id' => $kb_category_id )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBasePage' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'kb_category_id' => $this->kb_category_id
            , 'name' => $this->name
        ), 'is' );

        $this->id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'kb_category_id' => $this->kb_category_id
            , 'name' => $this->name
        ), array(
            'id' => $this->id
        ), 'is', 'i' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'id' => $this->id
        ), 'i' );
    }

    /**
	 * List
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return KnowledgeBasePage[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT kbp.`id`, kbp.`name`, CONCAT( IF( kbc2.`name` IS NOT NULL, CONCAT( kbc2.`name`, ' > ' ), '' ), kbc.`name` ) AS category FROM `kb_page` AS kbp LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kbp.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBasePage' );
	}

    /**
	 * Count all
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare(
            "SELECT COUNT( kbp.`id` ) FROM `kb_page` AS kbp LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kbp.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
