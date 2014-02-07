<?php

require_once 'test/base-database-test.php';

class KnowledgeBaseArticleViewTest extends BaseDatabaseTest {
    const KB_ARTICLE_ID = 3;
    const USER_ID = 5;

    /**
     * @var KnowledgeBaseArticleView
     */
    private $kb_article_view;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_article_view = new KnowledgeBaseArticleView();

        // Define
        $this->phactory->define( 'kb_article_view', array( 'kb_article_id' => self::KB_ARTICLE_ID, 'user_id' => self::USER_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->kb_article_view->kb_article_id = self::KB_ARTICLE_ID;
        $this->kb_article_view->user_id = self::USER_ID;
        $this->kb_article_view->create();

        // Make sure it's in the database
        $ph_kb_article_view = $this->phactory->get( 'kb_article_view', array( 'kb_article_id' => self::KB_ARTICLE_ID ) );

        // Assert
        $this->assertEquals( self::USER_ID, $ph_kb_article_view->user_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_article_view = null;
    }
}
