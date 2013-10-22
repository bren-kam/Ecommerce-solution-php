<?php

require_once 'base-database-test.php';

class KnowledgeBaseArticleRatingTest extends BaseDatabaseTest {
    /**
     * @var KnowledgeBaseArticleRating
     */
    private $kb_article_rating;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_article_rating = new KnowledgeBaseArticleRating();
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Declare variables
        $kb_article_id = -3;
        $rating = 1;

        // Create
        $this->kb_article_rating->kb_article_id = $kb_article_id;
        $this->kb_article_rating->rating = $rating;
        $this->kb_article_rating->create();

        // Make sure it's in the database
        $fetched_rating = $this->phactory->get_var( "SELECT `rating` FROM `kb_article_rating` WHERE `kb_article_id` = $kb_article_id" );

        $this->assertEquals( $rating, $fetched_rating );

        // Delete the comment
        $this->phactory->delete( 'kb_article_rating', compact( 'kb_article_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_article_rating = null;
    }
}
