<?php

require_once 'base-database-test.php';

class EmailTest extends BaseDatabaseTest {
    /**
     * @var Email
     */
    private $email;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email = new Email();
    }
    
    /**
     * Test Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $email = 'cranky@crank.com';

        // Create
        $email_id = $this->phactory->insert( 'emails', compact( 'website_id', 'email' ), 'is' );

        // Get
        $this->email->get( $email_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $email, $this->email->email );

        // Clean up
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Getting an email by email
     */
    public function testGetEmailByEmail() {
        // Declare variables
        $account_id = -5;
        $email_address = 'conn@ells.com';

        // Insert an email that has not been synced
        $this->phactory->insert( 'emails', array( 'website_id' => $account_id, 'email' => $email_address ), 'is' );

        $email_id = $this->phactory->get_insert_id();

        // Get unsynced emails
        $this->email->get_by_email( $account_id, $email_address );

        $this->assertEquals( $this->email->id, $email_id );

        // Delete email
        $this->phactory->delete( 'emails', array( 'email' => $email_address ), 's' );
    }

    /**
     * Get Dashboard Subscribers By Account
     */
    public function testGetDashboardSubscribersByAccount() {
        // Declare variables
        $website_id = -5;
        $email = 'con@nells.com';
        $email2 = 'con2@nells.com';
        $email3 = 'con3@nells.com';
        $email4 = 'con4@nells.com';
        $email5 = 'con5@nells.com';
        $email6 = 'con6@nells.com';

        // Insert an email that has not been synced
        $this->phactory->query( "INSERT INTO `emails` ( `website_id`, `email` ) VALUES ( $website_id, '$email' ), ( $website_id, '$email2' ), ( $website_id, '$email3' ), ( $website_id, '$email4' ), ( $website_id, '$email5' ), ( $website_id, '$email6' )" );

        // Get unsynced emails
        $emails = $this->email->get_dashboard_subscribers_by_account( $website_id );

        $this->assertTrue( current( $emails ) instanceof Email );
        $this->assertLessThanOrEqual( count( $emails ), 5 );

        // Clean up
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->email->website_id = -3;
        $this->email->email = 'ender@game.com';
        $this->email->name = "Ender's Game";
        $this->email->status = 1;
        $this->email->create();

        $this->assertTrue( !is_null( $this->email->id ) );

        // Make sure it's in the database
        $email = $this->phactory->get_var( 'SELECT `email` FROM `emails` WHERE `email_id` = ' . (int) $this->email->id );

        $this->assertEquals( 'ender@game.com', $email );

        // Delete
        $this->phactory->delete( 'emails', array( 'email_id' => $this->email->id ), 'i' );
    }
    
    /**
     * Test updating a email
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create test
        $this->email->website_id = -3;
        $this->email->email = 'ender@game.com';
        $this->email->name = "Ender's Game";
        $this->email->status = 1;
        $this->email->create();

        // Update test
        $this->email->status = 0;
        $this->email->save();

        // Now check it!
        $status = $this->phactory->get_var( 'SELECT `status` FROM `emails` WHERE `email_id` = ' . (int) $this->email->id );

        $this->assertEquals( '0', $status );

        // Delete the email
        $this->phactory->delete( 'emails', array( 'email_id' => $this->email->id ), 'i' );
    }

    /**
     * Unsubscribe Bulk
     */
    public function testUnsubscribeBulk() {
        // Declare variables
        $website_id = -5;
        $status = 0;
        $email = $emails[] = 'idon@care.com';

        // Insert 2
        $this->phactory->insert( 'emails', compact( 'website_id', 'email', 'status' ), 'iss' );

        $email = $emails[] = 'idont@care.com';
        $this->phactory->insert( 'emails', compact( 'website_id', 'email', 'status' ), 'iss' );

        // Unsubscribe
        $this->email->unsubscribe_bulk( $emails, $website_id );

        // Make sure there are none left
        $email = $this->phactory->get_var( "SELECT `email` FROM `emails` WHERE `website_id` = $website_id AND `status` <> $status" );

        $this->assertFalse( $email );

        // Clean up
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Clean Bulk
     */
    public function testCleanBulk() {
        // Declare variables
        $website_id = -5;
        $status = 2;
        $email = $emails[] = 'idon@care.com';

        // Insert 2
        $this->phactory->insert( 'emails', compact( 'website_id', 'email', 'status' ), 'iss' );

        $email = $emails[] = 'idont@care.com';
        $this->phactory->insert( 'emails', compact( 'website_id', 'email', 'status' ), 'iss' );

        // Unsubscribe
        $this->email->clean_bulk( $emails, $website_id );

        // Make sure there are none left
        $email = $this->phactory->get_var( "SELECT `email` FROM `emails` WHERE `website_id` = $website_id AND `status` <> $status" );

        $this->assertFalse( $email );

        // Clean up
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Sync Bulk
     */
    public function testSyncBulk() {
        // Declare variables
        $website_id = -5;
        $date_synced = '2012-01-01 00:00:00';

        // Insert 2
        $email_ids[] = $this->phactory->insert( 'emails', compact( 'website_id', 'date_synced' ), 'is' );
        $email_ids[] = $this->phactory->insert( 'emails', compact( 'website_id', 'date_synced' ), 'is' );

        // Unsubscribe
        $this->email->sync_bulk( $email_ids );

        // Make sure there are none left
        $retrieved_date_synced = $this->phactory->get_var( "SELECT `date_synced` FROM `emails` WHERE `website_id` = $website_id AND `date_synced` < '2013-01-01 00:00:00'" );

        $this->assertFalse( $retrieved_date_synced );

        // Clean up
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Remove Associations
     */
    public function testRemoveAssociations() {
        // Declare variables
        $email_id = -5;
        $email_list_id = -3;

        // Set ID
        $this->email->id = $email_id;

        // Create
        $this->phactory->insert( 'email_associations', compact( 'email_id', 'email_list_id' ), 'i' );

        // Remove associations
        $this->email->remove_associations();

        $retrieved_email_list_id = $this->phactory->get_var( "SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = $email_id" );

        $this->assertFalse( $retrieved_email_list_id );
    }

    /**
     * Remove All
     *
     * @depends testCreate
     * @depends testRemoveAssociations
     * @depends testSave
     */
    public function testRemoveAll() {
        // Declare variables
        $mc_list_id = 'abc123';
        $email = 'keepyou@guessing.com';
        $email_list_id = -3;
        $status = 1;
        $new_status = 0;

        // Create
        $this->email->email = $email;
        $this->email->status = $status;
        $this->email->create();

        $this->phactory->insert( 'email_associations', array( 'email_id' => $this->email->id, 'email_list_id' => $email_list_id ), 'ii' );

        // Setup stub
        library( 'MCAPI' );
        $stub_mc = $this->getMock( 'MCAPI', array(), array(), '', false );
        $stub_mc->expects($this->once())->method('listUnsubscribe')->with( $mc_list_id, $email );
        $stub_mc->errorCode = 232; // Just to get more code coverage -- it should continue going

        // Do it!
        $this->email->remove_all( $mc_list_id, $stub_mc );

        // Changes status
        $this->assertEquals( $new_status, $this->email->status );

        // Should have no associations
        $retrieved_email_list_id = $this->phactory->get_var( 'SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = ' . (int) $this->email->id );

        $this->assertFalse( $retrieved_email_list_id );

        // Clean Up
        $this->phactory->delete( 'emails', array( 'email_id' => $this->email->id ), 'i' );
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
        $dt->order_by( 'e.`email`', 'e.`name`', 'e.`date_created`' );

        $emails = $this->email->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $emails ) instanceof Email );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
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
        $dt->order_by( '`subject`', '`status`', 'date_sent' );

        $count = $this->email->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Test Import
     */
    public function testImport() {
        // Make it possible to call this function
        $class = new ReflectionClass('Email');
        $method = $class->getMethod( 'import' );
        $method->setAccessible(true);

        // Declare Variables
        $website_id = -5;
        $email = 'test@googoo.com';
        $name = 'Lee';
        $values = array(
             $email => $name
        );

        // Insert
        $method->invokeArgs( $this->email, array( $website_id, $values ) );

        // Get emails
        $fetched_email = $this->phactory->get_var( "SELECT `email` FROM `email_import_emails` WHERE `website_id` = $website_id AND `name` = '$name'" );

        $this->assertEquals( $fetched_email, $email );

        // Cleanup
        $this->phactory->delete( 'email_import_emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Import Emails
     */
    public function testImportEmails() {
        // Make it possible to call this function
        $class = new ReflectionClass('Email');
        $method = $class->getMethod( 'import_emails' );
        $method->setAccessible(true);

        // Declare variables
        $website_id = -15;
        $email = 'test@flock.com';

        // Insert
        $this->phactory->insert( 'email_import_emails', compact( 'website_id', 'email' ), 'is' );

        // Test
        $method->invokeArgs( $this->email, array( $website_id ) );

        // Get
        $fetched_email = $this->phactory->get_var( "SELECT `email` FROM `emails` WHERE `website_id` = $website_id" );

        $this->assertEquals( $email, $fetched_email );

        // Cleanup
        $this->phactory->delete( 'email_import_emails', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Add Associations to imported emails
     */
    public function testAddAssociationsToImportedEmails() {
        // Make it possible to call this function
        $class = new ReflectionClass('Email');
        $method = $class->getMethod( 'add_associations_to_imported_emails' );
        $method->setAccessible(true);

        // Declare variables
        $website_id = -15;
        $email = 'test@flocking.com';
        $email_list_ids = array( -2, -4, -6 );

        // Insert
        $this->phactory->insert( 'email_import_emails', compact( 'website_id', 'email' ), 'is' );
        $email_id = $this->phactory->insert( 'emails', compact( 'website_id', 'email' ), 'is' );

        // Add
        $method->invokeArgs( $this->email, array( $website_id, $email_list_ids ) );

        // Get
        $fetched_email_list_ids = $this->phactory->get_col( "SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = $email_id ORDER BY `email_list_id` DESC" );

        $this->assertEquals( $email_list_ids, $fetched_email_list_ids );

        $this->phactory->delete( 'email_import_emails', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'email_associations', compact( 'email_id' ), 'i' );
    }

    /**
     * Test Delete Imported
     */
    public function testDeleteImported() {
        // Declare variables
        $website_id = -5;
        $email = 'test@googoo.com';

        // Insert
        $this->phactory->insert( 'email_import_emails', compact( 'website_id', 'email' ), 'is' );

        // Delete imported
        $this->email->delete_imported( $website_id );

        // Make sure there are none left
        $email = $this->phactory->get_var( "SELECT `email` FROM `email_import_emails` WHERE `website_id` = $website_id" );

        $this->assertFalse( $email );
    }

    /**
     * Test Import All
     *
     * @depends testDeleteImported
     * @depends testImport
     */
    public function testImportAll() {
        // Declare Variables
        $website_id = -5;
        $email = 'test@googoo.com';
        $name = 'Lee';
        $unsubscribed_email = 'test@gaga.com';
        $unsubscribed_name = 'Eel';
        $emails = array(
            array(
                'email' => $email
                , 'name' => $name
            )
            , array(
                'email' => $unsubscribed_email
                , 'name' => $unsubscribed_name
            )
        );

        // Insert
        $this->phactory->insert( 'emails', array( 'website_id' => $website_id, 'email' => $unsubscribed_email, 'name' => $unsubscribed_name, 'status' => 0 ), 'issi' );

        // Import all
        $this->email->import_all( $website_id, $emails );

        // Get emails
        $fetched_email = $this->phactory->get_var( "SELECT `email` FROM `email_import_emails` WHERE `website_id` = $website_id AND `name` = '$name'" );

        $this->assertEquals( $fetched_email, $email );

        // Cleanup
        $this->phactory->delete( 'email_import_emails', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Complete Import
     *
     * @depends testImportEmails
     * @depends testAddAssociationsToImportedEmails
     * @depends testDeleteImported
     */
    public function testCompleteImport() {
        // Declare variables
        $website_id = -25;
        $email = 'test@flock.com';
        $email_list_ids = array( -2, -4, -6 );

        // Insert
        $this->phactory->insert( 'email_import_emails', compact( 'website_id', 'email' ), 'is' );

        // Complete the import
        $this->email->complete_import( $website_id, $email_list_ids );

        $email_id = $this->phactory->get_var( "SELECT `email_id` FROM `emails` WHERE `website_id` = $website_id AND `email` = " . $this->phactory->quote( $email ) );

        // Make sure it's there
        $this->assertGreaterThan( 0, $email_id );

        // Make sure it has the emails it should
        $fetched_email_list_ids = $this->phactory->get_col( "SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = $email_id ORDER BY `email_list_id` DESC" );

        $this->assertEquals( $email_list_ids, $fetched_email_list_ids );

        // Clean Up
        $this->phactory->delete( 'email_import_emails', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'emails', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'email_associations', compact( 'email_id' ), 'i' );
    }

    /**
     * Test Get Associations
     */
    public function testGetAssociations() {
        // Declare variables
        $this->email->id = $email_id = -5;
        $email_list_ids = array( '-2', '-3', '-4' );

        // Delete any associations from before hand
        $this->phactory->delete( 'email_associations', array( 'email_id' => $this->email->id ) , 'i' );

        // Insert
        $this->phactory->query( "INSERT INTO `email_associations` ( `email_id`, `email_list_id` ) VALUES ( $email_id, " . implode( "), ( $email_id, ", $email_list_ids ) . ")" );

        // Get associations
        $fetched_email_list_ids = $this->email->get_associations();

        // Don't care about the order, but it reverses it.
        $email_list_ids = array_reverse( $email_list_ids );

        $this->assertEquals( $email_list_ids, $fetched_email_list_ids );

        // Delete any images from before hand
        $this->phactory->delete( 'email_associations', array( 'email_id' => $this->email->id ) , 'i' );
    }

    /**
     * Test Adding Associations
     */
    public function testAddAssociations() {
        // Declare variables
        $this->email->id = -5;
        $email_list_ids = array( '-2', '-3', '-4' );

        // Delete any associations from before hand
        $this->phactory->delete( 'email_associations', array( 'email_id' => $this->email->id ) , 'i' );

        // Add
        $this->email->add_associations( $email_list_ids );

        // See if they are there
        $fetched_email_list_ids = $this->phactory->get_col( 'SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = ' . $this->email->id . ' ORDER BY `email_list_id` DESC' );

        $this->assertEquals( $email_list_ids, $fetched_email_list_ids );

        // Delete any images from before hand
        $this->phactory->delete( 'email_associations', array( 'email_id' => $this->email->id ) , 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email = null;
    }
}
