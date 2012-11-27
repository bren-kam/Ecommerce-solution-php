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
     * Test Getting an email by email
     */
    public function testGetEmailByEmail() {
        // Declare variables
        $account_id = -5;
        $email_address = 'con@nells.com';

        // Insert an email that has not been synced
        $this->db->insert( 'emails', array( 'website_id' => $account_id, 'email' => $email_address ), 'is' );

        $email_id = $this->db->get_insert_id();

        // Get unsynced emails
        $email = $this->email->get_email_by_email( $account_id, $email_address );

        $this->assertEquals( $email->id, $email_id );

        // Delete email
        $this->db->delete( 'emails', array( 'email' => $email_address ), 's' );
    }

    /**
     * Test Getting Unsynchronized Emails (so they can be synced)
     */
    public function testGetUnsynced() {
        // Declare variables
        $account_id = -5;

        // Insert an email that has not been synced
        $this->db->insert( 'emails', array( 'website_id' => $account_id, 'email' => 'lan@caster.com', 'name' => 'George', 'date_created' => '2010-10-10 00:00:00' ), 'isss' );

        $email_id = $this->db->get_insert_id();

        // Get unsynced emails
        $emails = $this->email->get_unsynced();

        $this->assertTrue( current( $emails ) instanceof Email );

        // Delete email
        $this->db->delete( 'emails', array( 'email_id' => $email_id ), 'i' );
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
        $email = $this->db->get_var( 'SELECT `email` FROM `emails` WHERE `email_id` = ' . (int) $this->email->id );

        $this->assertEquals( 'ender@game.com', $email );

        // Delete
        $this->db->delete( 'emails', array( 'email_id' => $this->email->id ), 'i' );
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
        $status = $this->db->get_var( 'SELECT `status` FROM `emails` WHERE `email_id` = ' . (int) $this->email->id );

        $this->assertEquals( '0', $status );

        // Delete the email
        $this->db->delete( 'emails', array( 'email_id' => $this->email->id ), 'i' );
    }

    /**
     * Test Adding Associations
     */
    public function testAddAssociations() {
        // Declare variables
        $this->email->id = -5;
        $email_list_ids = array( '-2', '-3', '-4' );

        // Delete any images from before hand
        $this->db->delete( 'email_associations', array( 'email_id' => $this->email->id ) , 'i' );

        // Add images
        $this->email->add_associations( $email_list_ids );

        // See if they are there
        $fetched_email_list_ids = $this->db->get_col( 'SELECT `email_list_id` FROM `email_associations` WHERE `email_id` = ' . $this->email->id . ' ORDER BY `email_list_id` DESC' );

        $this->assertEquals( $email_list_ids, $fetched_email_list_ids );

        // Delete any images from before hand
        $this->db->delete( 'email_associations', array( 'email_id' => $this->email->id ) , 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email = null;
    }
}
