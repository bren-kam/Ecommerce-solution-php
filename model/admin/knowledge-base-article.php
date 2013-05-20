<?php
class KnowledgeBaseArticle extends ActiveRecordBase {
    const STATUS_DELETED = 0;
    const STATUS_PUBLISHED = 1;

    // The columns we will have access to
    public $id, $kb_category_id, $kb_page_id, $user_id, $title, $slug, $content, $status, $date_created;

    // Artificial columns
    public $category, $page;

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
            'SELECT `id`, `kb_category_id`, `kb_page_id`, `user_id`, `title`, `slug`, `content`, `status` FROM `kb_article` WHERE `id` = :id AND `status` <> :status'
            , 'ii'
            , array( ':id' => $id, ':status' => self::STATUS_DELETED )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();
        
        $this->id = $this->insert( array(
            'kb_category_id' => $this->kb_category_id
            , 'kb_page_id' => $this->kb_page_id
            , 'user_id' => $this->user_id
            , 'title' => $this->title
            , 'slug' => $this->slug
            , 'content' => $this->content
            , 'status' => $this->status
            , 'date_created' => $this->date_created
        ), 'iiisssis' );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'kb_category_id' => $this->kb_category_id
            , 'kb_page_id' => $this->kb_page_id
            , 'user_id' => $this->user_id
            , 'title' => $this->title
            , 'slug' => $this->slug
            , 'content' => $this->content
            , 'status' => $this->status
        ), array(
            'id' => $this->id
        ), 'iiisssi', 'i' );
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
            "SELECT kba.`id`, kba.`title`, CONCAT( IF( kbc2.`name` IS NOT NULL, CONCAT( kbc2.`name`, ' > ' ), '' ), kbc.`name` ) AS category, kbp.`name` AS page FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) WHERE `status` <> " . self::STATUS_DELETED . " $where $order_by LIMIT $limit"
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
            "SELECT COUNT( kba.`id` ) FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) WHERE `status` <> " . self::STATUS_DELETED . $where
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
