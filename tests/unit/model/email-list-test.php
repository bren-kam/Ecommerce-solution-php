<?php

require_once 'base-database-test.php';

class EmailListTest extends BaseDatabaseTest {
    /**
     * @var EmailList
     */
    private $email_list;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_list = new EmailList();
    }

    /**
     * Test Getting the Default Email List
     */
    public function testGet() {
        // Declare variables
        $website_id = -5;
        $name = 'Dig My Dipper';

        // Insert an email list
        $this->db->insert( 'email_lists', compact( 'website_id', 'name' ), 'is' );

        $email_list_id = $this->db->get_insert_id();

        $this->email_list->get( $email_list_id, $website_id );

        $this->assertEquals( $name, $this->email_list->name );

        // Delete
        $this->db->delete( 'email_lists',compact( 'website_id' ), 'i' );
    }

    /**
     * Test Getting the Default Email List
     */
    public function testGetDefaultEmailList() {
        // Declare variables
        $website_id = -5;
        $category_id = 0;

        // Insert an email list
        $this->db->insert( 'email_lists', compact( 'website_id', 'category_id' ), 'ii' );

        $email_list_id = $this->db->get_insert_id();

        $this->email_list->get_default_email_list( $website_id );

        $this->assertEquals( $email_list_id, $this->email_list->id );

        // Delete
        $this->db->delete( 'email_lists',compact( 'website_id' ), 'i' );
    }

    /**
     * Test Getting by account
     */
    public function testGetByAccount() {
        // Declare variables
        $website_id = -5;

        // Insert an email list
        $this->db->insert( 'email_lists', compact( 'website_id' ), 'i' );

        $email_lists = $this->email_list->get_by_account( $website_id );

        $this->assertTrue( current( $email_lists ) instanceof EmailList );

        // Delete
        $this->db->delete( 'email_lists',compact( 'website_id' ), 'i' );
    }

    /**
     * Test Get Count by account
     */
    public function testGetCountByAccount() {
        
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $website_id = -3;
        $name = 'Test Default';

        $this->email_list->website_id = $website_id;
        $this->email_list->name = $name;
        $this->email_list->create();

        // Make sure it's in the database
        $fetched_name = $this->db->get_var( 'SELECT `name` FROM `email_lists` WHERE `email_list_id` = ' . (int) $this->email_list->id );

        $this->assertEquals( $name, $fetched_name );

        // Delete
        $this->db->delete( 'email_lists', compact( 'website_id' ), 'i' );
    }

    /**
     * Test save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $website_id = -3;
        $name = 'Test Default';

        // Create
        $this->email_list->website_id = $website_id;
        $this->email_list->create();

        // Save
        $this->email_list->name = $name;
        $this->email_list->save();

        // Make sure it's in the database
        $fetched_name = $this->db->get_var( 'SELECT `name` FROM `email_lists` WHERE `email_list_id` = ' . (int) $this->email_list->id );

        $this->assertEquals( $name, $fetched_name );

        // Delete
        $this->db->delete( 'email_lists', compact( 'website_id' ), 'i' );
    }

    /**
     * List All
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'el.`name`', 'el.`description`', 'el.`date_created`' );

        $email_lists = $this->email_list->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $email_lists ) instanceof EmailList );

        // Get rid of everything
        unset( $user, $_GET, $dt, $email_lists );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'el.`name`', 'el.`description`', 'el.`date_created`' );

        $count = $this->email_list->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_list = null;
    }
}
