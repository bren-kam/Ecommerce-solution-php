<?php

require_once 'base-database-test.php';

class KnowledgeBaseArticleViewTest extends BaseDatabaseTest {
    /**
     * @var KnowledgeBaseArticleView
     */
    private $kb_article_view;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_article_view = new KnowledgeBaseArticleView();
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Declare variables
        $kb_article_id = -3;
        $user_id = -5;

        // Create
        $this->kb_article_view->kb_article_id = $kb_article_id;
        $this->kb_article_view->user_id = $user_id;
        $this->kb_article_view->create();

        // Make sure it's in the database
        $fetched_user_id = $this->phactory->get_var( "SELECT `user_id` FROM `kb_article_view` WHERE `kb_article_id` = $kb_article_id" );

        $this->assertEquals( $user_id, $fetched_user_id );

        // Delete the comment
        $this->phactory->delete( 'kb_article_view', compact( 'kb_article_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_article_view = null;
    }
}
