<?php

require_once 'base-database-test.php';

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
        $subject = $this->db->get_var( 'SELECT `subject` FROM `email_autoresponders` WHERE `email_autoresponder_id` = ' . (int) $this->email_autoresponder->id );

        $this->assertEquals( 'Welcome to Testing', $subject );

        // Delete
        $this->db->delete( 'email_autoresponders', array( 'email_autoresponder_id' => $this->email_autoresponder->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_autoresponder = null;
    }
}
