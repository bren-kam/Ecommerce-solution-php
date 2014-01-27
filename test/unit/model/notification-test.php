<?php

require_once 'test/base-database-test.php';

class NotificationTest extends BaseDatabaseTest {
    /**
     * @var Notification
     */
    private $notification;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->notification = new Notification();
    }

    /**
     * Test getting the company
     */
    public function testCreate() {
        // Create notfication
        $this->notification->user_id = 513;
        $this->notification->message = 'Hello World!';
        $this->notification->create();

        // Get the message
        $message = $this->phactory->get_var( 'SELECT `message` FROM `notification` WHERE `user_id` = 513' );

        // Assert it
        $this->assertEquals( $this->notification->message, $message );

        // Delete the message
        $this->phactory->query( 'DELETE FROM `notification` WHERE `user_id` = 513' );
    }

    /**
     * Test getting notifications by user
     *
     * @depends testCreate
     */
    public function testGetByUser() {
        // Create notification
        $this->notification->user_id = 513;
        $this->notification->message = 'Hiphop';
        $this->notification->create();

        // Get notifications
        $notifications = $this->notification->get_by_user( 513 );

        // Make sure all is good in the world
        $this->assertTrue( $notifications[0] instanceof Notification );
        $this->assertEquals( $notifications[0]->message, 'Hiphop' );

        // Delete the message
        $this->phactory->query( 'DELETE FROM `notification` WHERE `user_id` = 513' );
    }

    /**
     * Test Delete by user
     *
     * @depends testCreate
     */
    public function testDeleteByUser() {
        // Create notification
        $this->notification->user_id = 513;
        $this->notification->message = 'Hiphop';
        $this->notification->create();

        // Delete all the notifications
        $this->notification->delete_by_user( 513 );

        // Count how many notifications there are
        $count = $this->phactory->get_var( 'SELECT COUNT( `id` ) FROM `notification` WHERE `user_id` = 513' );

        // Should be nothing left
        $this->assertEquals( $count, 0 );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->notification = null;
    }
}