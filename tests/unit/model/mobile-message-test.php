<?php

require_once 'base-database-test.php';

class MobileMessageTest extends BaseDatabaseTest {
    /**
     * @var MobileMessage
     */
    private $mobile_message;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_message = new MobileMessage();
    }

    /**
     * Test update scheduled messages
     */
    public function testUpdateScheduled() {
        // Declare variables
        $account_id = -5;
        $scheuled_status = 2;

        // Create an email message
        $this->phactory->insert( 'mobile_messages', array(
            'website_id' => $account_id
            , 'title' => 'George of the Jungle'
            , 'message' => 'George, George, George of the Jungle!'
            , 'status' => 1 // scheduled
            , 'date_created' => '2012-10-10 00:00:00'
            , 'date_sent' => '2012-10-10 00:00:00'
        ), 'ississ' );

        $mobile_message_id = $this->phactory->get_insert_id();

        // Update it to scheduled
        $this->mobile_message->update_scheduled();

        // Get status to make sure it's scheduled
        $status = $this->phactory->get_var( "SELECT `status` FROM `mobile_messages` WHERE `mobile_message_id` = $mobile_message_id" );

        $this->assertEquals( $scheuled_status, $status );

        // Delete email
        $this->phactory->delete( 'mobile_messages', array( 'mobile_message_id' => $mobile_message_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_message = null;
    }
}
