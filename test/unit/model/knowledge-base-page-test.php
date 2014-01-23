<?php

require_once 'test/base-database-test.php';

class KnowledgeBasePageTest extends BaseDatabaseTest {
    /**
     * @var KnowledgeBasePage
     */
    private $kb_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_page = new KnowledgeBasePage();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $name = 'Import Subscribers';

        // Create
        $id = $this->phactory->insert( 'kb_page', compact( 'name' ), 's' );

        $this->kb_page->get( $id );

        $this->assertEquals( $this->kb_page->name, $name );

        // Clean up
        $this->phactory->delete( 'kb_page', compact( 'id' ), 'i' );
    }

    /**
     * Test Get by Category
     */
    public function testGetByCategory() {
        // Declare variables
        $kb_category_id = -3;
        $name = 'Import Subscribers';

        // Create
        $this->phactory->insert( 'kb_page', compact( 'kb_category_id', 'name' ), 'is' );

        $pages = $this->kb_page->get_by_category( $kb_category_id );

        $this->assertTrue( current( $pages ) instanceof KnowledgeBasePage );

        // Clean up
        $this->phactory->delete( 'kb_page', compact( 'kb_category_id' ), 'i' );
    }

    /**
     * Test Create
     *
     * @depends testGet
     */
    public function testCreate() {
        // Declare variables
        $name = 'Import Subscribers';

        // Create
        $this->kb_page->name = $name;
        $this->kb_page->create();

        // Make sure it's in the database
        $this->kb_page->get( $this->kb_page->id );

        $this->assertEquals( $name, $this->kb_page->name );

        // Delete the comment
        $this->phactory->delete( 'kb_page', array( 'id' => $this->kb_page->id ), 'i' );
    }

    /**
     * Test Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $first_name = 'Import Subscribers';
        $second_name = 'Export Subscribers';

        // Create
        $this->kb_page->name = $first_name;
        $this->kb_page->create();

        // Save
        $this->kb_page->name = $second_name;
        $this->kb_page->save();

        // Make sure it's in the database
        $fetched_name = $this->phactory->get_var( "SELECT `name` FROM `kb_page` WHERE `id` = " . (int) $this->kb_page->id );

        $this->assertEquals( $fetched_name, $second_name );

        // Delete the comment
        $this->phactory->delete( 'kb_page', array( 'id' => $this->kb_page->id ), 'i' );
    }

    /**
     * Test Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Declare variables
        $name = 'Import Subscribers';

        // Create
        $this->kb_page->name = $name;
        $this->kb_page->create();

        $id = (int) $this->kb_page->id;

        // Delete ticket comment
        $this->kb_page->remove();

        // Check
        $fetched_kb_page_id = $this->phactory->get_var( "SELECT `kb_page_id` FROM `kb_page` WHERE `id` = $id" );

        $this->assertFalse( $fetched_kb_page_id );
    }

    /**
     * Test List All
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';
        $_GET['section'] = 'admin';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'kbp.`name`', 'category' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_page->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_page->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kbp.`name`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false ) );

        $pages = $this->kb_page->list_all( $dt->get_variables() );

        // Make sure they exist
        $this->assertTrue( current( $pages ) instanceof KnowledgeBasePage );

        // Get rid of everything
        unset( $user, $_GET, $dt, $pages );
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
        $dt->order_by( 'kbp.`name`', 'category' );
        $dt->add_where( ' AND kbc.`section` = ' . $this->kb_page->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $this->kb_page->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kbp.`name`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false ) );

        $count = $this->kb_page->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertEquals( (int) $count, $count );

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
