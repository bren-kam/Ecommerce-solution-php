<?php

require_once 'test/base-database-test.php';

class NotificationTest extends BaseDatabaseTest {
    const USER_ID = 7;
    const MESSAGE = 'Hello World!';

    /**
     * @var Notification
     */
    private $notification;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->notification = new Notification();

        // Define
        $this->phactory->define( 'notification', array( 'user_id' => self::USER_ID, 'message' => self::MESSAGE ) );
        $this->phactory->recall();
    }

    /**
     * Test getting the company
     */
    public function testCreate() {
        // Create notfication
        $this->notification->user_id = self::USER_ID;
        $this->notification->message = self::MESSAGE;
        $this->notification->create();

        // Assert
        $this->assertNotNull( $this->notification->id );

        // Get
        $ph_notification = $this->phactory->get( 'notification', array( 'id' => $this->notification->id ) );

        // Assert
        $this->assertEquals( self::MESSAGE, $ph_notification->message );
    }

    /**
     * Test getting notifications by user
     */
    public function testGetByUser() {
        // Create
        $this->phactory->create('notification');

        // Get
        $notifications = $this->notification->get_by_user( self::USER_ID );
        $notification = current( $notifications );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Notification', $notifications );
        $this->assertEquals( self::MESSAGE, $notification->message );
    }

    /**
     * Test Delete by user
     */
    public function testDeleteByUser() {
        // Create
        $this->phactory->create('notification');

        // Delete
        $this->notification->delete_by_user( self::USER_ID );

        // Get
        $ph_notification = $this->phactory->get( 'notification', array( 'user_id' => self::USER_ID ) );

        // Assert
        $this->assertNull( $ph_notification );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->notification = null;
    }
}