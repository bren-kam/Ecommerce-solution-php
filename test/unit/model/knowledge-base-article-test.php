<?php

require_once 'test/base-database-test.php';

class KnowledgeBaseArticleTest extends BaseDatabaseTest {
    /**
     * @var KnowledgeBaseArticle
     */
    private $kb_article;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_article = new KnowledgeBaseArticle();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $title = 'How to Import Subscribers';

        // Create
        $id = $this->phactory->insert( 'kb_article', compact( 'title' ), 's' );

        $this->kb_article->get( $id );

        $this->assertEquals( $title, $this->kb_article->title );

        // Clean up
        $this->phactory->delete( 'kb_article', compact( 'id' ), 'i' );
    }

    /**
     * Test Create
     *
     * @depends testGet
     */
    public function testCreate() {
        // Declare variables
        $title = 'How to Import Subscribers';

        // Create
        $this->kb_article->title = $title;
        $this->kb_article->create();

        // Make sure it's in the database
        $this->kb_article->get( $this->kb_article->id );

        $this->assertEquals( $title, $this->kb_article->title );

        // Delete the comment
        $this->phactory->delete( 'kb_article', array( 'id' => $this->kb_article->id ), 'i' );
    }

    /**
     * Test Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $first_title = 'How to Import Subscribers';
        $second_title = 'How to Export Subscribers';

        // Create
        $this->kb_article->title = $first_title;
        $this->kb_article->create();

        // Save
        $this->kb_article->title = $second_title;
        $this->kb_article->save();

        // Make sure it's in the database
        $fetched_title = $this->phactory->get_var( "SELECT `title` FROM `kb_article` WHERE `id` = " . (int) $this->kb_article->id );

        $this->assertEquals( $second_title, $fetched_title );

        // Delete the comment
        $this->phactory->delete( 'kb_article', array( 'id' => $this->kb_article->id ), 'i' );
    }

    /**
     * Test Listing All
     *
     * @depends testCreate
     */
    public function testListAll() {
        // Declare variables
        $title = 'How to Import Subscribers';
        $section = 'admin';

        // Insert
        $kb_category_id = $this->phactory->insert( 'kb_category', compact('section'), 's' );

        // Create
        $this->kb_article->kb_category_id = $kb_category_id;
        $this->kb_article->title = $title;
        $this->kb_article->create();

        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'kba.`title`', 'category', 'page' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_article->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_article->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kba.`title`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false, 'kbp.`name`' => false ) );

        $articles = $this->kb_article->list_all( $dt->get_variables() );

        // Make sure they exist
        $this->assertTrue( current( $articles ) instanceof KnowledgeBaseArticle );

        // Get rid of everything
        unset( $user, $_GET, $dt, $articles );

        // Delete the comment
        $this->phactory->delete( 'kb_article', array( 'id' => $this->kb_article->id ), 'i' );
        $this->phactory->delete( 'kb_category', array( 'id' => $kb_category_id ), 'i' );
    }

    /**
     * Test Count All
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'kba.`title`', 'category', 'page' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_article->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_article->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kba.`title`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false, 'kbp.`name`' => false ) );

        $count = $this->kb_article->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertEquals( (int) $count, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_article = null;
    }
}
