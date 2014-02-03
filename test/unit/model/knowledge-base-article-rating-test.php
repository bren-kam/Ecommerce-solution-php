<?php

require_once 'test/base-database-test.php';

class KnowledgeBaseArticleRatingTest extends BaseDatabaseTest {
    const KB_ARTICLE_ID = 3;
    const RATING = 1;

    /**
     * @var KnowledgeBaseArticleRating
     */
    private $kb_article_rating;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_article_rating = new KnowledgeBaseArticleRating();

        // Define
        $this->phactory->define( 'kb_article_rating', array( 'kb_article_id' => self::KB_ARTICLE_ID, 'rating' => self::RATING ) );
        $this->phactory->recall();
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->kb_article_rating->kb_article_id = self::KB_ARTICLE_ID;
        $this->kb_article_rating->rating = self::RATING;
        $this->kb_article_rating->create();

        // Get
        $ph_kb_article_rating = $this->phactory->get( 'kb_article_rating', array( 'kb_article_id' => self::KB_ARTICLE_ID ) );

        // Assert
        $this->assertEquals( self::RATING, $ph_kb_article_rating->rating );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_article_rating = null;
    }
}
