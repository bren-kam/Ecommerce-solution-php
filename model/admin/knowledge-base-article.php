<?php
class KnowledgeBaseArticle extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $user_id, $title, $slug, $content, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'kb_article' );
    }

    /**
     * Get
     *
     * @param int $id
     * @return KnowledgeBaseArticle
     */
    public function get( $id ) {
        $this->prepare(
            'SELECT `id`, `user_id`, `title`, `slug`, `content` FROM `kb_article` WHERE `id` = :id'
            , 'i'
            , array( ':id' => $id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();
        
        $this->id = $this->insert( array(
            'user_id' => $this->user_id
            , 'title' => $this->title
            , 'slug' => $this->slug
            , 'content' => $this->content
            , 'date_created' => $this->date_created
        ), 'issss' );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'user_id' => $this->user_id
            , 'title' => $this->title
            , 'slug' => $this->slug
            , 'content' => $this->content
        ), array(
            'id' => $this->id
        ), 'isss', 'i' );
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
	 * @return KnowledgeBaseArticle[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT kbp.`id`, kbp.`name`, CONCAT( IF( kbc2.`name` IS NOT NULL, CONCAT( kbc2.`name`, ' > ' ), '' ), kbc.`name` ) AS category FROM `kb_page` AS kbp LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kbp.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseArticle' );
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
