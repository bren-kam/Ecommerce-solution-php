<?php

require_once 'test/base-database-test.php';

class EmailTest extends BaseDatabaseTest {
    const EMAIL = 'cranky@crank.com';
    const NAME = 'Sebastian';

    // Email Associations
    const EMAIL_ID = 15;
    const EMAIL_LIST_ID = 17;

    /**
     * @var Email
     */
    private $email;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email = new Email();

        // Define
        $this->phactory->define( 'emails', array( 'website_id' => self::WEBSITE_ID, 'email' => self::EMAIL, 'status' => Email::STATUS_SUBSCRIBED ) );
        $this->phactory->define( 'email_associations', array( 'email_id' => self::EMAIL_ID, 'email_list_id' => self::EMAIL_LIST_ID ) );
        $this->phactory->define( 'email_import_emails', array( 'website_id' => self::WEBSITE_ID, 'email' => self::EMAIL, 'name' => self::NAME ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_email = $this->phactory->create('emails');

        // Get
        $this->email->get( $ph_email->email_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::EMAIL, $this->email->email );
    }

    /**
     * Test Getting an email by email
     */
    public function testGetEmailByEmail() {
        // Create
        $ph_email = $this->phactory->create('emails');

        // Get Get
        $this->email->get_by_email( self::WEBSITE_ID, self::EMAIL );

        // Assert
        $this->assertEquals( $ph_email->email_id, $this->email->id );
    }

    /**
     * Get Dashboard Subscribers By Account
     */
    public function testGetDashboardSubscribersByAccount() {
        // Create
        $this->phactory->create('emails');

        // Get
        $emails = $this->email->get_dashboard_subscribers_by_account( self::WEBSITE_ID );
        $email = current( $emails );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Email', $emails );
        $this->assertEquals( self::EMAIL, $email->email );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->email->email = self::EMAIL;
        $this->email->create();

        // Assert
        $this->assertNotNull( $this->email->id );

        // Get
        $ph_email = $this->phactory->get( 'emails', array( 'email_id' => $this->email->id ) );

        // Assert
        $this->assertEquals( self::EMAIL, $ph_email->email );
    }

    /**
     * Test updating a email
     */
    public function testSave() {
       // Create
       $ph_email = $this->phactory->create('emails');

        // Update test
        $this->email->id = $ph_email->email_id;
        $this->email->email = 'water@mud.com';
        $this->email->save();

        // Get
        $ph_email = $this->phactory->get( 'emails', array( 'email_id' => $ph_email->email_id ) );

        // Assert
        $this->assertEquals( $this->email->email, $ph_email->email );
    }

    /**
     * Remove Associations
     */
    public function testRemoveAssociations() {
        // Create
        $this->phactory->create('email_associations');

        // Remove associations
        $this->email->id = self::EMAIL_ID;
        $this->email->remove_associations();

        // Get
        $ph_email_association = $this->phactory->get( 'email_associations',  array( 'email_id' => self::EMAIL_ID ) );

        // Assert
        $this->assertNull( $ph_email_association );
    }


    /**
     * List All
     */
    public function testListAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('emails');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'e.`email`', 'e.`name`', 'e.`date_created`' );

        // Get
        $emails = $this->email->list_all( $dt->get_variables() );
        $email = current( $emails );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'Email', $emails );
        $this->assertEquals( self::EMAIL, $email->email );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('emails');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`subject`', '`status`', 'date_sent' );

        // Get
        $count = $this->email->count_all( $dt->get_count_variables() );

        // Assert
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

        // Declare
        $values = array(
             self::EMAIL => self::NAME
        );

        // Import
        $method->invokeArgs( $this->email, array( self::WEBSITE_ID, $values ) );

        // Get
        $ph_email_import_email = $this->phactory->get( 'email_import_emails', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEquals( self::EMAIL, $ph_email_import_email->email );
    }

    /**
     * Test Import Emails
     */
    public function testImportEmails() {
        // Make it possible to call this function
        $class = new ReflectionClass('Email');
        $method = $class->getMethod( 'import_emails' );
        $method->setAccessible(true);

        // Create
        $this->phactory->create('email_import_emails');

        // Import Emails
        $method->invokeArgs( $this->email, array( self::WEBSITE_ID ) );

        // Get
        $ph_email = $this->phactory->get( 'emails', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_email->name );
    }

    /**
     * Test Add Associations to imported emails
     */
    public function testAddAssociationsToImportedEmails() {
        // Make it possible to call this function
        $class = new ReflectionClass('Email');
        $method = $class->getMethod( 'add_associations_to_imported_emails' );
        $method->setAccessible(true);

        // Create
        $this->phactory->create('email_import_emails');
        $ph_email = $this->phactory->create('emails');

        // Add
        $method->invokeArgs( $this->email, array( self::WEBSITE_ID, array( self::EMAIL_LIST_ID ) ) );

        // Get
        $ph_email_association = $this->phactory->get( 'email_associations', array( 'email_id' => $ph_email->email_id ) );

        // Assert
        $this->assertEquals( self::EMAIL_LIST_ID, $ph_email_association->email_list_id );
    }

    /**
     * Test Delete Imported
     */
    public function testDeleteImported() {
        // Create
        $this->phactory->create('email_import_emails');

        // Delete imported
        $this->email->delete_imported( self::WEBSITE_ID );

        // Get
        $ph_email_import_email = $this->phactory->get( 'email_import_emails', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertNull( $ph_email_import_email );
    }

    /**
     * Test Import All
     *
     * @depends testDeleteImported
     * @depends testImport
     */
    public function testImportAll() {
        // Declare
        $emails = array(
            array(
                'email' => self::EMAIL
                , 'name' => self::NAME
            )
        );

        // Import all
        $this->email->import_all( self::WEBSITE_ID, $emails );

        // Get emails
        $ph_email_import_email = $this->phactory->get( 'email_import_emails', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEquals( self::EMAIL, $ph_email_import_email->email );
    }


    /**
     * Test Get Associations
     */
    public function testGetAssociations() {
        // Create
        $this->phactory->create('email_associations');

        // Get associations
        $this->email->id = self::EMAIL_ID;
        $email_list_ids = $this->email->get_associations();
        $expected_email_list_ids = array( self::EMAIL_LIST_ID );

        // Assert
        $this->assertEquals( $expected_email_list_ids, $email_list_ids );
    }

    /**
     * Test Adding Associations
     */
    public function testAddAssociations() {
        // Add
        $this->email->id = self::EMAIL_ID;
        $this->email->add_associations( array( self::EMAIL_LIST_ID ) );

        // Get
        $ph_email_association = $this->phactory->get( 'email_associations', array( 'email_id' => self::EMAIL_ID ) );

        // Assert
        $this->assertEquals( self::EMAIL_LIST_ID, $ph_email_association->email_list_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email = null;
    }
}
