<?php

require_once 'test/base-database-test.php';

class EmailListTest extends BaseDatabaseTest {
    const NAME = 'Facebook Fans';

    /**
     * @var EmailList
     */
    private $email_list;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_list = new EmailList();

        // Define
        $this->phactory->define( 'email_lists', array( 'website_id' => self::WEBSITE_ID, 'category_id' => EmailList::DEFAULT_CATEGORY_ID, 'name' => self::NAME ) );
        $this->phactory->define( 'emails', array( 'website_id' => self::WEBSITE_ID, 'status' => Email::STATUS_SUBSCRIBED ) );
        $this->phactory->define( 'email_messages', array( 'website_id' => self::WEBSITE_ID ) );
        $this->phactory->define( 'email_associations' );
        $this->phactory->define( 'email_message_associations' );
        $this->phactory->recall();
    }

    /**
     * Test Getting the Default Email List
     */
    public function testGet() {
        // Craete
        $ph_email_list = $this->phactory->create('email_lists');

        // Get
        $this->email_list->get( $ph_email_list->email_list_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->email_list->name );
    }

    /**
     * Test Getting the Default Email List
     */
    public function testGetDefaultEmailList() {
        // Create
        $ph_email_list = $this->phactory->create('email_lists');

        // Get
        $this->email_list->get_default_email_list( self::WEBSITE_ID );

        // Assert
        $this->assertEquals( $ph_email_list->email_list_id, $this->email_list->id );
    }

    /**
     * Test Getting by account
     */
    public function testGetByAccount() {
        // Create
        $this->phactory->create('email_lists');

        // Get
        $email_lists = $this->email_list->get_by_account( self::WEBSITE_ID );
        $email_list = current( $email_lists );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailList', $email_lists );
        $this->assertEquals( self::NAME, $email_list->name );
    }

    /**
     * Test Get Count by account
     */
    public function testGetCountByAccount() {
        // Create
        $ph_email_list = $this->phactory->create('email_lists');
        $ph_email = $this->phactory->create('emails');
        $this->phactory->create( 'email_associations', array( 'email_list_id' => $ph_email_list->email_list_id, 'email_id' => $ph_email->email_id ) );

        // Get count
        $email_lists = $this->email_list->get_count_by_account( self::WEBSITE_ID );
        $email_list = current( $email_lists );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailList', $email_lists );
        $this->assertEquals( self::NAME, $email_list->name );
    }

    /**
     * Test Get Count by message
     */
    public function testGetCountByMessage() {
        // Create
        $ph_email_list = $this->phactory->create('email_lists');
        $ph_email_message = $this->phactory->create('email_messages');
        $this->phactory->create( 'email_message_associations', array( 'email_list_id' => $ph_email_list->email_list_id, 'email_message_id' => $ph_email_message->email_message_id ) );
        $ph_email = $this->phactory->create('emails');
        $this->phactory->create( 'email_associations', array( 'email_list_id' => $ph_email_list->email_list_id, 'email_id' => $ph_email->email_id ) );

        // Get count
        $email_lists = $this->email_list->get_by_message( $ph_email_message->email_message_id, self::WEBSITE_ID );
        $email_list = current( $email_lists );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailList', $email_lists );
        $this->assertEquals( self::NAME, $email_list->name );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->email_list->website_id = self::WEBSITE_ID;
        $this->email_list->name = self::NAME;
        $this->email_list->create();

        // Assert
        $this->assertNotNull( $this->email_list->id );

        // Get
        $ph_email_list = $this->phactory->get( 'email_lists', array( 'email_list_id' => $this->email_list->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_email_list->name );
    }

    /**
     * Test save
     */
    public function testSave() {
        // Create
        $ph_email_list = $this->phactory->create('email_lists');

        // Save
        $this->email_list->id = $ph_email_list->email_list_id;
        $this->email_list->name = 'Weebles';
        $this->email_list->save();

        // Get
        $ph_email_list = $this->phactory->get( 'email_lists', array( 'email_list_id' => $this->email_list->id ) );

        // Assert
        $this->assertEquals( $this->email_list->name, $ph_email_list->name );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $ph_email_list = $this->phactory->create('email_lists');

        // Remove
        $this->email_list->id = $ph_email_list->email_list_id;
        $this->email_list->remove();

        // Get
        $ph_email_list = $this->phactory->get( 'email_lists', array( 'email_list_id' => $this->email_list->id ) );

        // Assert
        $this->assertNotNull( $ph_email_list );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Mock User
        $stub_user = $this->getMock('user');

        // Create
        $this->phactory->create('email_lists');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'el.`name`', 'el.`description`', 'el.`date_created`' );

        // Get
        $email_lists = $this->email_list->list_all( $dt->get_variables() );
        $email_list = current( $email_lists );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailList', $email_lists );
        $this->assertEquals( self::NAME, $email_list->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $email_lists );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Mock User
        $stub_user = $this->getMock('user');

        // Create
        $this->phactory->create('email_lists');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'el.`name`', 'el.`description`', 'el.`date_created`' );

        // Get
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
