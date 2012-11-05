<?php

require_once 'base-database-test.php';

class EmailMessageTest extends BaseDatabaseTest {
    /**
     * @var EmailMessage
     */
    private $email_message;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_message = new EmailMessage();
    }

    /**
     * Test Update all email messages to "scheduled"
     */
    public function testUpdateScheduledEmails() {
        // Declare variables
        $account_id = -5;
        $scheuled_status = 2;

        // Create an email message
        $this->db->insert( 'email_messages', array(
            'website_id' => $account_id
            , 'email_template_id' => -3
            , 'subject' => 'George of the Jungle'
            , 'message' => 'George, George, George of the Jungle!'
            , 'type' => 'product'
            , 'status' => 1 // scheduled
            , 'date_created' => '2012-10-10 00:00:00'
            , 'date_sent' => '2012-10-10 00:00:00'
        ), 'iisssis' );

        $email_message_id = $this->db->get_insert_id();

        // Update it to scheduled
        $this->email_message->update_scheduled_emails();

        // Get status to make sure it's scheduled
        $status = $this->db->get_var( "SELECT `status` FROM `email_messages` WHERE `email_message_id` = $email_message_id" );

        $this->assertEquals( $scheuled_status, $status );

        // Delete email
        $this->db->delete( 'email_messages', array( 'email_message_id' => $email_message_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_message = null;
    }
}
