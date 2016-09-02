<?php
class KnowledgeBaseArticle extends ActiveRecordBase {
    const STATUS_DELETED = 0;
    const STATUS_PUBLISHED = 1;

    // The columns we will have access to
    public $id, $kb_category_id, $kb_page_id, $user_id, $title, $slug, $content, $status, $date_created, $date_updated;

    // Artificial columns/columns from other tables
    public $section, $category, $page, $helpful, $unhelpful, $rating, $views;

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
     */
    public function get( $id ) {
        $this->prepare(
            'SELECT `id`, `kb_category_id`, `kb_page_id`, `user_id`, `title`, `slug`, `content`, `status`, `date_updated` FROM `kb_article` WHERE `id` = :id AND `status` <> :status'
            , 'ii'
            , array( ':id' => $id, ':status' => self::STATUS_DELETED )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get by IDs
     *
     * @param int $id
     */
    public function get_by_ids( $ids = []) {
        if ( !$ids )
            return [];

        return $this->prepare(
            'SELECT `id`, `kb_category_id`, `kb_page_id`, `user_id`, `title`, `slug`, `content`, `status` FROM `kb_article` WHERE `id` IN ('.( implode(',', $ids) ).') AND `status` <> :status'
            , 'i'
            , array( ':status' => self::STATUS_DELETED )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseArticle' );
    }


    /**
     * Get by page
     *
     * @param int $kb_page_id
     * @return KnowledgeBaseArticle[]
     */
    public function get_by_page( $kb_page_id ) {
        return $this->prepare(
            'SELECT kba.`id`, kba.`kb_category_id`, kba.`kb_page_id`, kba.`user_id`, kba.`title`, kba.`slug`, kba.`content`, kba.`status` FROM `kb_article` AS kba LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) WHERE kbp.`id` = :kb_page_id AND kba.`status` <> :status'
            , 'ii'
            , array( ':kb_page_id' => $kb_page_id, ':status' => self::STATUS_DELETED )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseArticle' );
    }

    /**
     * Get by category
     *
     * @param int $kb_category_id
     * @return KnowledgeBaseArticle[]
     */
    public function get_by_category( $kb_category_id ) {
        return $this->prepare(
            'SELECT kba.`id`, kba.`kb_category_id`, kba.`kb_page_id`, kba.`user_id`, kba.`title`, kba.`slug`, kba.`content`, kba.`status` FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) WHERE kbc.`id` = :kb_category_id AND kba.`status` <> :status'
            , 'ii'
            , array( ':kb_category_id' => $kb_category_id, ':status' => self::STATUS_DELETED )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseArticle' );
    }

    /**
     * Get by views
     *
     * @param string $section
     * @return KnowledgeBaseArticle[]
     */
    public function get_by_views( $section ) {
        return $this->prepare(
            'SELECT kba.`id`, kba.`kb_category_id`, kba.`kb_page_id`, kba.`user_id`, kba.`title`, kba.`slug`, kba.`content`, kba.`status` FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_article_view` AS kbav ON ( kbav.`kb_article_id` = kba.`id` ) WHERE kbc.`section` = :section AND kba.`status` <> :status GROUP BY kba.`id` ORDER BY COUNT( kbav.`kb_article_id` ) DESC LIMIT 15'
            , 'si'
            , array( ':section' => $section, ':status' => self::STATUS_DELETED )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseArticle' );
    }

    /**
     * Search
     *
     * @param string $search
     * @return KnowledgeBaseArticle[]
     */
    public function search( $search ) {
        return $this->prepare(
            "SELECT `id`, `kb_category_id`, `kb_page_id`, `user_id`, `title`, `slug`, `content`, `status`, MATCH ( `title`, `content` ) AGAINST( :search ) AS relevance FROM `kb_article` WHERE `status` <> :status AND MATCH ( `title`, `content` ) AGAINST( :search2 ) ORDER BY relevance DESC"
            , 'is'
            , array( ':status' => self::STATUS_DELETED, ':search' => $search, ':search2' => $search )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseArticle' );
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
            , 'title' => strip_tags($this->title)
            , 'slug' => strip_tags($this->slug)
            , 'content' => format::strip_only( $this->content, '<script>' )
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
            , 'title' => strip_tags($this->title)
            , 'slug' => strip_tags($this->slug)
            , 'content' => format::strip_only( $this->content, '<script>' )
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
            "SELECT kba.`id`, kba.`title`, CONCAT( IF( kbc2.`name` IS NOT NULL, CONCAT( kbc2.`name`, ' > ' ), '' ), kbc.`name` ) AS category, kbp.`name` AS page, kbar.helpful, kbar.unhelpful, kbar.rating, kbav.views FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) LEFT JOIN ( SELECT `kb_article_id`, SUM( `rating` ) AS rating, SUM( IF( " . KnowledgeBaseArticleRating::POSITIVE . " = `rating`, 1, 0 ) ) AS helpful, SUM( IF( " . KnowledgeBaseArticleRating::NEGATIVE . " = `rating`, 1, 0 ) ) AS unhelpful FROM `kb_article_rating` GROUP BY `kb_article_id` ) AS kbar ON ( kbar.`kb_article_id` = kba.`id` ) LEFT JOIN ( SELECT `kb_article_id`, COUNT(*) AS views FROM `kb_article_view` GROUP BY `kb_article_id` ) AS kbav ON ( kbav.`kb_article_id` = kba.`id` ) WHERE kba.`status` <> " . self::STATUS_DELETED . " $where GROUP BY kba.`id` $order_by LIMIT $limit"
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
            "SELECT COUNT( DISTINCT kba.`id` ) FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) LEFT JOIN ( SELECT `kb_article_id`, SUM( `rating` ) AS rating, SUM( IF( " . KnowledgeBaseArticleRating::POSITIVE . " = `rating`, 1, 0 ) ) AS helpful, SUM( IF( " . KnowledgeBaseArticleRating::NEGATIVE . " = `rating`, 1, 0 ) ) AS unhelpful FROM `kb_article_rating` GROUP BY `kb_article_id` ) AS kbar ON ( kbar.`kb_article_id` = kba.`id` ) LEFT JOIN ( SELECT `kb_article_id`, COUNT(*) AS views FROM `kb_article_view` GROUP BY `kb_article_id` ) AS kbav ON ( kbav.`kb_article_id` = kba.`id` ) WHERE kba.`status` <> " . self::STATUS_DELETED . $where
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}

    /**
	 * List by User
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return KnowledgeBaseArticle[]
	 */
	public function list_by_user( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT kba.`id`, kba.`title`, kbc.`section`, CONCAT( IF( kbc2.`name` IS NOT NULL, CONCAT( kbc2.`name`, ' > ' ), '' ), kbc.`name` ) AS category, kbp.`name` AS page, kbar.`rating`, COUNT( kbav.`kb_article_id` ) AS views FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) LEFT JOIN `kb_article_view` AS kbav ON ( kbav.`kb_article_id` = kba.`id` ) LEFT JOIN `kb_article_rating` AS kbar ON ( kbar.`kb_article_id` = kba.`id` ) WHERE kba.`status` <> " . self::STATUS_DELETED . " $where GROUP BY kba.`id` $order_by LIMIT $limit"
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
	public function count_by_user( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT kba.`id` ) FROM `kb_article` AS kba LEFT JOIN `kb_category` AS kbc ON ( kbc.`id` = kba.`kb_category_id` ) LEFT JOIN `kb_category` AS kbc2 ON ( kbc2.`id` = kbc.`parent_id` ) LEFT JOIN `kb_page` AS kbp ON ( kbp.`id` = kba.`kb_page_id` ) LEFT JOIN `kb_article_view` AS kbav ON ( kbav.`kb_article_id` = kba.`id` ) LEFT JOIN `kb_article_rating` AS kbar ON ( kbar.`kb_article_id` = kba.`id` ) WHERE kba.`status` <> " . self::STATUS_DELETED . $where
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
