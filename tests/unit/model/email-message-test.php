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
     * Test Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $subject = 'Right from the start';

        // Create
        $email_message_id = $this->phactory->insert( 'email_messages', compact( 'website_id', 'subject' ), 'is' );

        // Get
        $this->email_message->get( $email_message_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $subject, $this->email_message->subject );

        // Clean up
        $this->phactory->delete( 'email_messages', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Get Meta
     */
    public function testGetMeta() {
        // Set variables
        $email_message_id = -5;
        $type = 'product';
        $value = serialize( array(
            'product_id' => -3
            , 'price' => 30
            , 'order' => 1
        ) );

        $this->email_message->id = $email_message_id;

        // Create
        $this->phactory->insert( 'email_message_meta', compact( 'email_message_id', 'type', 'value' ), 'ss' );

        // Get
        $meta = $this->email_message->get_meta();

        // Make sure we grabbed the right one
        $this->assertEquals( $meta[0]['value'], $value );

        // Clean up
        $this->phactory->delete( 'email_message_meta', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Get Smart Meta - A
     *
     * @depends testGetMeta
     */
    public function testGetSmartMetaA() {
        // Set variables
        $email_message_id = -5;
        $type = 'product';
        $product_id = -3;
        $price = 30;
        $value = serialize( array(
            'product_id' => $product_id
            , 'price' => $price
            , 'order' => 1
        ) );

        $this->email_message->id = $email_message_id;
        $this->email_message->type = $type;

        // Create
        $this->phactory->insert( 'email_message_meta', compact( 'email_message_id', 'type', 'value' ), 'ss' );

        // Get
        $this->email_message->get_smart_meta();

        // Make sure we grabbed the right one
        $this->assertEquals( $this->email_message->meta[$product_id]->price, $price );

        // Clean up
        $this->phactory->delete( 'email_message_meta', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Get Smart Meta - B
     *
     * @depends testGetMeta
     */
    public function testGetSmartMetaB() {
        // Set variables
        $email_message_id = -5;
        $type = 'product';

        $this->email_message->id = $email_message_id;
        $this->email_message->type = $type;

        // Get
        $this->email_message->get_smart_meta();

        // Make sure we grabbed the right one
        $this->assertEquals( $this->email_message->meta, array() );
    }

    /**
     * Get Smart Meta - C
     *
     * @depends testGetMeta
     */
    public function testGetSmartMetaC() {
        // Set variables
        $email_message_id = -5;
        $type = 'view-button-color';
        $value = 'ff0000';

        $this->email_message->id = $email_message_id;

        // Create
        $this->phactory->insert( 'email_message_meta', compact( 'email_message_id', 'type', 'value' ), 'ss' );

        // Get
        $this->email_message->get_smart_meta();

        // Make sure we grabbed the right one
        $this->assertEquals( $this->email_message->meta[$type], $value );

        // Clean up
        $this->phactory->delete( 'email_message_meta', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Create
     */
    public function testCreate() {
        // Declare variables
        $website_id = -3;
        $subject = "I don't believe you";

        // Create
        $this->email_message->website_id = $website_id;
        $this->email_message->subject = $subject;
        $this->email_message->create();

        // Make sure it's in the database
        $fetched_subject = $this->phactory->get_var( 'SELECT `subject` FROM `email_messages` WHERE `email_message_id` = ' . (int) $this->email_message->id );

        $this->assertEquals( $subject, $fetched_subject );

        // Delete
        $this->phactory->delete( 'email_messages', compact( 'website_id' ), 'i' );
    }

    /**
     * Add Associations
     */
    public function testAddAssociations() {
        // Declare variables
        $email_message_id = -3;
        $email_list_ids = array( -2, -4, -6 );

        $this->email_message->id = $email_message_id;

        // Add
        $this->email_message->add_associations( $email_list_ids );

        $fetched_email_list_ids = $this->phactory->get_col( "SELECT `email_list_id` FROM `email_message_associations` WHERE `email_message_id` = $email_message_id ORDER BY `email_list_id` DESC");

        $this->assertEquals( $email_list_ids, $fetched_email_list_ids );

        // Clean up
        $this->phactory->delete( 'email_message_associations', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Add Meta
     */
    public function testAddMeta() {
        // Declare variables
        $email_message_id = -3;
        $meta = array( array( 'view-button-color', 'FF0000' ) );

        $this->email_message->id = $email_message_id;

        // Add
        $this->email_message->add_meta( $meta );

        $fetched_meta_color = $this->phactory->get_var( "SELECT `value` FROM `email_message_meta` WHERE `email_message_id` = $email_message_id AND `type` = " . $this->phactory->quote( $meta[0][0] ) );

        $this->assertEquals( $meta[0][1], $fetched_meta_color );

        // Clean up
        $this->phactory->delete( 'email_message_meta', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $website_id = -3;
        $subject = "I don't believe you";

        // Create
        $this->email_message->website_id = $website_id;
        $this->email_message->create();

        // Save
        $this->email_message->subject = $subject;
        $this->email_message->save();

        // Make sure it's in the database
        $fetched_subject = $this->phactory->get_var( 'SELECT `subject` FROM `email_messages` WHERE `email_message_id` = ' . (int) $this->email_message->id );

        $this->assertEquals( $subject, $fetched_subject );

        // Delete
        $this->phactory->delete( 'email_messages', compact( 'website_id' ), 'i' );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Make it possible to call this function
        $class = new ReflectionClass('EmailMessage');
        $method = $class->getMethod( 'remove' );
        $method->setAccessible(true);

        // Set variables
        $website_id = -7;
        $subject = 'Right from the start';

        // Create
        $email_message_id = $this->phactory->insert( 'email_messages', compact( 'website_id', 'subject' ), 'is' );

        // Get
        $this->email_message->get( $email_message_id, $website_id );

        // Remove
        $method->invoke( $this->email_message );

        $email_message = $this->phactory->get_row( "SELECT * FROM `email_messages` WHERE `email_message_id` = $email_message_id" );

        // Make sure we grabbed the right one
        $this->assertFalse( $email_message );
    }

    /**
     * Remove Associations
     */
    public function testRemoveAssociations() {
        // Set variables
        $email_message_id = -7;
        $email_list_id = -5;

        // Insert
        $this->phactory->insert( 'email_message_associations', compact( 'email_message_id', 'email_list_id' ), 'ii' );

        // Remove
        $this->email_message->id = $email_message_id;
        $this->email_message->remove_associations();

        // Get/check
        $email_list_id = $this->phactory->get_var( "SELECT `email_list_id` FROM `email_message_associations` WHERE `email_message_id` = $email_message_id" );

        $this->assertFalse( $email_list_id );

        // Clean up
        $this->phactory->delete( 'email_message_associations', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Remove Meta
     */
    public function testRemoveMeta() {
        // Set variables
        $email_message_id = -7;
        $type = 'view-button-color';
        $value = 'ff0000';

        // Create
        $this->phactory->insert( 'email_message_meta', compact( 'email_message_id', 'type', 'value' ), 'ss' );

        // Remove
        $this->email_message->id = $email_message_id;
        $this->email_message->remove_meta();

        // Get/check
        $fetched_value = $this->phactory->get_var( "SELECT `value` FROM `email_message_meta` WHERE `email_message_id` = $email_message_id AND `type` = " . $this->phactory->quote( $type ) );

        $this->assertFalse( $fetched_value );

        // Clean up
        $this->phactory->delete( 'email_message_meta', compact( 'email_message_id' ), 'i' );
    }

    /**
     * Test Update all email messages to "scheduled"
     */
    public function testUpdateScheduledEmails() {
        // Declare variables
        $account_id = -5;
        $scheuled_status = 2;

        // Create an email message
        $this->phactory->insert( 'email_messages', array(
            'website_id' => $account_id
            , 'email_template_id' => -3
            , 'subject' => 'George of the Jungle'
            , 'message' => 'George, George, George of the Jungle!'
            , 'type' => 'product'
            , 'status' => 1 // scheduled
            , 'date_created' => '2012-10-10 00:00:00'
            , 'date_sent' => '2012-10-10 00:00:00'
        ), 'iisssis' );

        $email_message_id = $this->phactory->get_insert_id();

        // Update it to scheduled
        $this->email_message->update_scheduled_emails();

        // Get status to make sure it's scheduled
        $status = $this->phactory->get_var( "SELECT `status` FROM `email_messages` WHERE `email_message_id` = $email_message_id" );

        $this->assertEquals( $scheuled_status, $status );

        // Delete email
        $this->phactory->delete( 'email_messages', array( 'email_message_id' => $email_message_id ), 'i' );
    }

    /**
     * Get Dashboard Messages By Account
     */
    public function testGetDashboardMessagesByAccount() {
        // Set variables
        $website_id = -7;
        $status = EmailMessage::STATUS_SENT;
        $subject = "You just threw it away";

        // Insert
        $this->phactory->insert( 'email_messages', compact( 'website_id', 'subject', 'status' ), 'isi' );

        $email_messages = $this->email_message->get_dashboard_messages_by_account( $website_id );

        $this->assertTrue( current( $email_messages ) instanceof EmailMessage );

        // Clean up
        $this->phactory->delete( 'email_messages', compact( 'website_id' ), 'i' );
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
        $dt->order_by( '`subject`', '`status`', 'date_sent' );

        $email_messages = $this->email_message->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $email_messages ) instanceof EmailMessage );

        // Get rid of everything
        unset( $user, $_GET, $dt, $email_messages );
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
        $dt->order_by( '`subject`', '`status`', 'date_sent' );

        $count = $this->email_message->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_message = null;
    }
}
