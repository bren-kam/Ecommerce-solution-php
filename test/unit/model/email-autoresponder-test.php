<?php

require_once 'test/base-database-test.php';

class EmailAutoresponderTest extends BaseDatabaseTest {
    const NAME = 'Bedroom Responder';

    /**
     * @var EmailAutoresponder
     */
    private $email_autoresponder;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_autoresponder = new EmailAutoresponder();

        // Define
        $this->phactory->define( 'email_autoresponders', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_email_autoresponder = $this->phactory->create( 'email_autoresponders' );

        // Get
        $this->email_autoresponder->get( $ph_email_autoresponder->email_autoresponder_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->email_autoresponder->name );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->email_autoresponder->website_id = self::WEBSITE_ID;
        $this->email_autoresponder->name = self::NAME;
        $this->email_autoresponder->create();

        // Assert
        $this->assertNotNull( $this->email_autoresponder->id );

        // Get
        $ph_email_autoresponder = $this->phactory->get( 'email_autoresponders', array( 'email_autoresponder_id' => $this->email_autoresponder->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_email_autoresponder->name );
    }

    /**
     * Save
     *
     * @depends testGet
     */
    public function testSave() {
        // Create
        $ph_email_autoresponder = $this->phactory->create( 'email_autoresponders' );

        // Save
        $this->email_autoresponder->id = $ph_email_autoresponder->email_autoresponder_id;
        $this->email_autoresponder->name = 'Default Responder';
        $this->email_autoresponder->save();

        // Get
        $ph_email_autoresponder = $this->phactory->get( 'email_autoresponders', array( 'email_autoresponder_id' => $ph_email_autoresponder->email_autoresponder_id ) );

        // Assert
        $this->assertEquals( $this->email_autoresponder->name, $ph_email_autoresponder->name );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Create
        $ph_email_autoresponder = $this->phactory->create( 'email_autoresponders' );

        // Remove/Delete
        $this->email_autoresponder->id = $ph_email_autoresponder->email_autoresponder_id;
        $this->email_autoresponder->remove();

        // Get
        $ph_email_autoresponder = $this->phactory->get( 'email_autoresponders', array( 'email_autoresponder_id' => $ph_email_autoresponder->email_autoresponder_id ) );

        // Assert
        $this->assertNull( $ph_email_autoresponder );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Get Mock User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create( 'email_autoresponders' );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`subject`' );

        // Get All
        $email_autoresponders = $this->email_autoresponder->list_all( $dt->get_variables() );
        $email_autoresponder = current( $email_autoresponders );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'EmailAutoresponder', $email_autoresponders );
        $this->assertEquals( self::NAME, $email_autoresponder->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get Mock User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create( 'email_autoresponders' );

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`subject`' );

        // Get
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
