<?php

require_once 'test/base-database-test.php';

class ChecklistTest extends BaseDatabaseTest {
    const TYPE = 'Website Setup';

    // Checklist Website Item
    const CHECKLIST_WEBSITE_ITEM_CHECKED = 0;

    // Checklist items
    const CHECKLIST_ITEM_STATUS = 1;

    // Website
    const WEBSITE_STATUS = 1;

    /**
     * @var Checklist
     */
    private $checklist;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist = new Checklist();

        // Define
        $this->phactory->define( 'checklists', array( 'website_id' => self::WEBSITE_ID, 'type' => self::TYPE ) );
        $this->phactory->define( 'checklist_website_items', array( 'checked' => self::CHECKLIST_WEBSITE_ITEM_CHECKED ) );
        $this->phactory->define( 'checklist_items', array( 'status' => self::CHECKLIST_ITEM_STATUS ) );
        $this->phactory->define( 'websites', array( 'status' => self::WEBSITE_STATUS ) );
        $this->phactory->define( 'users' );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_checklist = $this->phactory->create('checklists');

        // Get
        $this->checklist->get( $ph_checklist->checklist_id );

        // Assert
        $this->assertEquals( self::TYPE, $this->checklist->type );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->checklist->type = self::TYPE;
        $this->checklist->create();

        // Assert
        $this->assertNotNull( $this->checklist->id );

        // Make sure it's in the database
        $ph_checklist = $this->phactory->get( 'checklists', array( 'checklist_id' => $this->checklist->id ) );

        // Assert
        $this->assertEquals( self::TYPE, $ph_checklist->type );
    }

    /**
     * Tests getting incomplete checklists
     */
    public function testGetIncomplete() {
        // Create
        $ph_checklist = $this->phactory->create('checklists');
        $ph_checklist_item = $this->phactory->create('checklist_items');
        $this->phactory->create( 'checklist_website_items', array( 'checklist_id' => $ph_checklist->checklist_id, 'checklist_item_id' => $ph_checklist_item->checklist_item_id ) );

        // Get
        $incomplete_checklists = $this->checklist->get_incomplete();
        $expected_array = array( self::WEBSITE_ID => $ph_checklist->checklist_id );

        $this->assertEquals( $expected_array, $incomplete_checklists );
    }

    /**
     * Test listing all companies
     */
    public function testListAll() {
        // Mock User
        $stub_user = $this->getMock('User');

        // Create
        $ph_user = $this->phactory->create('users');
        $ph_website = $this->phactory->create( 'websites', array( 'user_id' => $ph_user->user_id ) );
        $this->phactory->create( 'checklists', array( 'website_id' => $ph_website->website_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'days_left', 'w.`title`', 'u2.`contact_name`', 'c.`type`', 'c.`date_created`' );
        $dt->search( array( 'w.`title`' => false ) );

        // Get
        $checklists = $this->checklist->list_all( $dt->get_variables() );
        $checklist = current( $checklists );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Checklist', $checklists );
        $this->assertEquals( self::TYPE, $checklist->type );

        // Get rid of everything
        unset( $user, $_GET, $dt, $checklists );
    }

    /**
     * Test counting the companies
     */
    public function testCountAll() {
        // Mock User
        $stub_user = $this->getMock('User');

        // Create
        $ph_user = $this->phactory->create('users');
        $ph_website = $this->phactory->create( 'websites', array( 'user_id' => $ph_user->user_id ) );
        $this->phactory->create( 'checklists', array( 'website_id' => $ph_website->website_id ) );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'days_left', 'w.`title`', 'u2.`contact_name`', 'c.`type`', 'c.`date_created`' );
        $dt->search( array( 'w.`title`' => false ) );

        $count = $this->checklist->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist = null;
    }
}
