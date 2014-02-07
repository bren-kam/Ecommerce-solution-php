<?php

require_once 'test/base-database-test.php';

class KnowledgeBaseArticleTest extends BaseDatabaseTest {
    const TITLE = 'How to Import Subscribers';

    // KB Category
    const SECTION = 'admin';

    /**
     * @var KnowledgeBaseArticle
     */
    private $kb_article;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_article = new KnowledgeBaseArticle();

        // Define
        $this->phactory->define( 'kb_article', array( 'title' => self::TITLE, 'status' => KnowledgeBaseArticle::STATUS_PUBLISHED ) );
        $this->phactory->define( 'kb_category', array( 'section' => self::SECTION ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_kb_article = $this->phactory->create('kb_article');

        // Get
        $this->kb_article->get( $ph_kb_article->id );

        // Assert
        $this->assertEquals( self::TITLE, $this->kb_article->title );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->kb_article->title = self::TITLE;
        $this->kb_article->create();

        // Assert
        $this->assertNotNull( $this->kb_article->id );

        // Get
        $ph_kb_article = $this->phactory->get( 'kb_article', array( 'id' => $this->kb_article->id ) );

        // Assert
        $this->assertEquals( self::TITLE, $ph_kb_article->title );
    }

    /**
     * Test Save
     */
    public function testSave() {
        // Create
        $ph_kb_article = $this->phactory->create('kb_article');

        // Save
        $this->kb_article->id = $ph_kb_article->id;
        $this->kb_article->title = 'How to Export Subscribers';
        $this->kb_article->save();

        // Get
        $ph_kb_article = $this->phactory->get( 'kb_article', array( 'id' => $this->kb_article->id ) );

        // Assert
        $this->assertEquals( $this->kb_article->title, $ph_kb_article->title );
    }

    /**
     * Test Listing All
     */
    public function testListAll() {
        // Get Mock
        $stub_user = $this->getMock('User');

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $this->phactory->create( 'kb_article', array( 'kb_category_id' => $ph_kb_category->id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'kba.`title`', 'category', 'page' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_article->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_article->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kba.`title`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false, 'kbp.`name`' => false ) );

        // Get
        $articles = $this->kb_article->list_all( $dt->get_variables() );
        $article = current( $articles );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBaseArticle', $articles );
        $this->assertEquals( self::TITLE, $article->title );

        // Get rid of everything
        unset( $user, $_GET, $dt, $articles );
    }

    /**
     * Test Count All
     */
    public function testCountAll() {
        // Get Mock
        $stub_user = $this->getMock('User');

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $this->phactory->create( 'kb_article', array( 'kb_category_id' => $ph_kb_category->id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'kba.`title`', 'category', 'page' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_article->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_article->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kba.`title`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false, 'kbp.`name`' => false ) );

        // Get
        $count = $this->kb_article->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

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
