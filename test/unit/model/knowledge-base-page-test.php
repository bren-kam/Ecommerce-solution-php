<?php

require_once 'test/base-database-test.php';

class KnowledgeBasePageTest extends BaseDatabaseTest {
    const NAME = 'Import Subscribers';
    const KB_CATEGORY_ID = 3;

    // KB Category
    const CATEGORY_NAME = 'Subscribers';

    /**
     * @var KnowledgeBasePage
     */
    private $kb_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_page = new KnowledgeBasePage();

        // Define
        $this->phactory->define( 'kb_page', array( 'kb_category_id' => self::KB_CATEGORY_ID, 'name' => self::NAME ) );
        $this->phactory->define( 'kb_category', array( 'name' => self::CATEGORY_NAME, 'section' => KnowledgeBaseCategory::SECTION_ADMIN ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_kb_page = $this->phactory->create('kb_page');

        // Get
        $this->kb_page->get( $ph_kb_page->id );

        // Assert
        $this->assertEquals( self::NAME, $this->kb_page->name );
    }

    /**
     * Test Get by Category
     */
    public function testGetByCategory() {
        // Create
        $this->phactory->create('kb_page');

        // Get
        $pages = $this->kb_page->get_by_category( self::KB_CATEGORY_ID );
        $page = current( $pages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBasePage', $pages );
        $this->assertEquals( self::NAME, $page->name );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->kb_page->name = self::NAME;
        $this->kb_page->create();

        // Assert
        $this->assertNotNull( $this->kb_page->id );

        // Get
        $ph_kb_page = $this->phactory->get( 'kb_page', array( 'id' => $this->kb_page->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_kb_page->name );
    }

    /**
     * Test Save
     */
    public function testSave() {
        // Create
        $ph_kb_page = $this->phactory->create('kb_page');

        // Save
        $this->kb_page->id = $ph_kb_page->id;
        $this->kb_page->name = 'Export Lists';
        $this->kb_page->save();

        // Get
        $ph_kb_page = $this->phactory->get( 'kb_page', array( 'id' => $ph_kb_page->id ) );

        // Assert
        $this->assertEquals( $this->kb_page->name, $ph_kb_page->name );
    }

    /**
     * Test Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Create
        $ph_kb_page = $this->phactory->create('kb_page');

        // Delete
        $this->kb_page->id = $ph_kb_page->id;
        $this->kb_page->remove();

        // Get
        $ph_kb_page = $this->phactory->get( 'kb_page', array( 'id' => $ph_kb_page->id ) );

        // Assert
        $this->assertNull( $ph_kb_page );
    }

    /**
     * Test List All
     */
    public function testListAll() {
        // Get Stub
        $stub_user = $this->getMock('User');

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $this->phactory->create( 'kb_page', array( 'kb_category_id' => $ph_kb_category->id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'kbp.`name`', 'category' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_page->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_page->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kbp.`name`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false ) );

        // Assert
        $pages = $this->kb_page->list_all( $dt->get_variables() );
        $page = current( $pages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBasePage', $pages );
        $this->assertEquals( self::NAME, $page->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $pages );
    }

    /**
     * Test Count All
     */
    public function testCountAll() {
        // Get Stub
        $stub_user = $this->getMock('User');

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $this->phactory->create( 'kb_page', array( 'kb_category_id' => $ph_kb_category->id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'kbp.`name`', 'category' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_page->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_page->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kbp.`name`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false ) );

        // Get
        $count = $this->kb_page->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_page = null;
    }
}
