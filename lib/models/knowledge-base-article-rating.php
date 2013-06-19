<?php
class KnowledgeBaseArticleRating extends ActiveRecordBase {
    const POSITIVE = 1;
    const NEGATIVE = -1;

    // The columns we will have access to
    public $kb_article_id, $rating, $user_id;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'kb_article_rating' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'kb_article_id' => $this->kb_article_id
            , 'user_id' => $this->user_id
            , 'rating' => $this->rating
        ), 'iii' );
    }
}
