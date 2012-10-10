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
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email = null;
    }
}
