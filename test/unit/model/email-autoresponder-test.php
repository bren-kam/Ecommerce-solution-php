<?php

require_once 'test/base-database-test.php';

class EmailAutoresponderTest extends BaseDatabaseTest {
    /**
     * @var EmailAutoresponder
     */
    private $email_autoresponder;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_autoresponder = new EmailAutoresponder();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $name = 'Bedroom Responder';

        // Create
        $email_autoresponder_id = $this->phactory->insert( 'email_autoresponders', compact( 'website_id', 'name' ), 'is' );

        // Get
        $this->email_autoresponder->get( $email_autoresponder_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $name, $this->email_autoresponder->name );

        // Clean up
        $this->phactory->delete( 'email_autoresponders', compact( 'website_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->email_autoresponder->website_id = -3;
        $this->email_autoresponder->email_list_id = -5;
        $this->email_autoresponder->name = 'Testing Default Responder';
        $this->email_autoresponder->subject = 'Welcome to Testing';
        $this->email_autoresponder->message = 'You are now on the testing default';
        $this->email_autoresponder->current_offer = 0;
        $this->email_autoresponder->default = 0;
        $this->email_autoresponder->create();

        $this->assertTrue( !is_null( $this->email_autoresponder->id ) );

        // Make sure it's in the database
        $subject = $this->phactory->get_var( 'SELECT `subject` FROM `email_autoresponders` WHERE `email_autoresponder_id` = ' . (int) $this->email_autoresponder->id );

        $this->assertEquals( 'Welcome to Testing', $subject );

        // Delete
        $this->phactory->delete( 'email_autoresponders', array( 'email_autoresponder_id' => $this->email_autoresponder->id ), 'i' );
    }
    
    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Set variables
        $website_id = -7;
        $name = 'Bedroom Responder';

        // Create
        $email_autoresponder_id = $this->phactory->insert( 'email_autoresponders', compact( 'website_id' ), 'i' );

        // Get
        $this->email_autoresponder->get( $email_autoresponder_id, $website_id );
        $this->email_autoresponder->name = $name;
        $this->email_autoresponder->save();

        // Now check it!
        $retrieved_name = $this->phactory->get_var( "SELECT `name` FROM `email_autoresponders` WHERE `email_autoresponder_id` = $email_autoresponder_id" );

        $this->assertEquals( $retrieved_name, $name );

        // Clean up
        $this->phactory->delete( 'email_autoresponders', compact( 'website_id' ), 'i' );
    }
    
    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Set variables
        $website_id = -7;
        $name = 'Bedroom Responder';

        // Create
        $email_autoresponder_id = $this->phactory->insert( 'email_autoresponders', compact( 'website_id', 'name' ), 'is' );

        // Get
        $this->email_autoresponder->get( $email_autoresponder_id, $website_id );

        // Remove/Delete
        $this->email_autoresponder->remove();

        $retrieved_name = $this->phactory->get_var( "SELECT `name` FROM `email_autoresponders` WHERE `email_autoresponder_id` = $email_autoresponder_id" );

        $this->assertFalse( $retrieved_name );
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
        $dt->order_by( '`name`', '`subject`' );

        $email_autoresponders = $this->email_autoresponder->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $email_autoresponders ) instanceof EmailAutoresponder );

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
        $dt->order_by( '`name`', '`subject`' );

        $count = $this->email_autoresponder->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_autoresponder = null;
    }
}
