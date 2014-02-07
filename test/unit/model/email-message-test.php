<?php

require_once 'test/base-database-test.php';

class EmailMessageTest extends BaseDatabaseTest {
    const SUBJECT = 'Right from the start';

    // Email Message Meta
    const EMAIL_MESSAGE_ID = 13;
    const EMAIL_MESSAGE_TYPE = 'product';

    // Email Message Associations
    const EMAIL_LIST_ID = 23;

    /**
     * Email Message Value
     * @var string
     */
    protected $email_message_value = array(
        'product_id' => 12
        , 'price' => 14
        , 'order' => 1
    );

    /**
     * @var EmailMessage
     */
    private $email_message;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_message = new EmailMessage();

        // Define
        $this->phactory->define( 'email_messages', array( 'website_id' => self::WEBSITE_ID, 'subject' => self::SUBJECT, 'status' => EmailMessage::STATUS_SENT ) );
        $this->phactory->define( 'email_message_meta', array( 'email_message_id' => self::EMAIL_MESSAGE_ID, 'type' => self::EMAIL_MESSAGE_TYPE, 'value' => serialize( $this->email_message_value ) ) );
        $this->phactory->define( 'email_message_associations', array( 'email_message_id' => self::EMAIL_MESSAGE_ID, 'email_list_id' => self::EMAIL_LIST_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_email_message = $this->phactory->create('email_messages');

        // Get
        $this->email_message->get( $ph_email_message->email_message_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::SUBJECT, $this->email_message->subject );
    }

    /**
     * Test Get Meta
     */
    public function testGetMeta() {
        // Create
        $this->phactory->create('email_message_meta');

        // Get
        $this->email_message->id = self::EMAIL_MESSAGE_ID;
        $meta = $this->email_message->get_meta();
        $expected_meta = serialize( $this->email_message_value );

        // Assert
        $this->assertEquals( $expected_meta, $meta[0]['value'] );
    }

    /**
     * Get Smart Meta - A
     *
     * @depends testGetMeta
     */
    public function testGetSmartMeta() {
        // Create
        $this->phactory->create('email_message_meta');

        // Get
        $this->email_message->id = self::EMAIL_MESSAGE_ID;
        $this->email_message->type = self::EMAIL_MESSAGE_TYPE;
        $this->email_message->get_smart_meta();

        // Assert
        $this->assertEquals( $this->email_message_value['price'], $this->email_message->meta[$this->email_message_value['product_id']]->price );

        // Try scenario two
        $this->phactory->recall();

        // Create
        $this->phactory->create( 'email_message_meta', array( 'value' => '' ) );

        // Get
        $this->email_message->get_smart_meta();
        $expected_meta = array();

        // Assert
        $this->assertEquals( $expected_meta, $this->email_message->meta );

        // Try scenario three
        $this->email_message->type = null;
        $this->phactory->recall();

        // Declare
        $type = 'view-button-color';
        $value = 'FF0000';

        // Create
        $this->phactory->create( 'email_message_meta', compact( 'type', 'value' ) );

        // Get
        $this->email_message->get_smart_meta();
        $expected_meta = array( $type => $value );

        // Assert
        $this->assertEquals( $expected_meta, $this->email_message->meta );
    }

    /**
     * Create
     */
    public function testCreate() {
        // Create
        $this->email_message->website_id = self::WEBSITE_ID;
        $this->email_message->subject = self::SUBJECT;
        $this->email_message->create();

        // Assert
        $this->assertNotNull( $this->email_message->id );

        // Make sure it's in the database
        $ph_email_message = $this->phactory->get( 'email_messages', array( 'email_message_id' => $this->email_message->id ) );

        // Assert
        $this->assertEquals( self::SUBJECT, $ph_email_message->subject );
    }

    /**
     * Add Associations
     */
    public function testAddAssociations() {
        // Add
        $this->email_message->id = self::EMAIL_MESSAGE_ID;
        $this->email_message->add_associations( array( self::EMAIL_LIST_ID ) );

        // Get
        $ph_email_message_association = $this->phactory->get( 'email_message_associations', array( 'email_message_id' => self::EMAIL_MESSAGE_ID ) );

        // Assert
        $this->assertEquals( self::EMAIL_LIST_ID, $ph_email_message_association->email_list_id );
    }


    /**
     * Add Meta
     */
    public function testAddMeta() {
        // Declare
        $type = 'view-button-color';
        $value = 'FF0000';

        // Add
        $this->email_message->id = self::EMAIL_MESSAGE_ID;
        $this->email_message->add_meta( array( array( $type, $value ) ) );

        // Get
        $ph_email_message_meta = $this->phactory->get( 'email_message_meta', array( 'email_message_id' => self::EMAIL_MESSAGE_ID ) );

        // Assert
        $this->assertEquals( $value, $ph_email_message_meta->value );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_email_message = $this->phactory->create('email_messages');

        // Save
        $this->email_message->id = $ph_email_message->email_message_id;
        $this->email_message->website_id = self::WEBSITE_ID;
        $this->email_message->subject = "Bon Bon";
        $this->email_message->save();

        // Get
        $ph_email_message = $this->phactory->get( 'email_messages', array( 'email_message_id' => $ph_email_message->email_message_id ) );

        // Assert
        $this->assertEquals( $this->email_message->subject, $ph_email_message->subject );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Reflection
        $class = new ReflectionClass('EmailMessage');
        $method = $class->getMethod('remove');
        $method->setAccessible(true);

        // Create
        $ph_email_message = $this->phactory->create('email_messages');

        // Remove
        $this->email_message->id = $ph_email_message->email_message_id;
        $method->invoke( $this->email_message );

        // Get
        $ph_email_message = $this->phactory->get( 'email_messages', array( 'email_message_id' => $ph_email_message->email_message_id ) );

        // Assert
        $this->assertNull( $ph_email_message );
    }

    /**
     * Remove Associations
     */
    public function testRemoveAssociations() {
        // Craete
        $this->phactory->create('email_message_associations');

        // Remove
        $this->email_message->id = self::EMAIL_MESSAGE_ID;
        $this->email_message->remove_associations();

        // Get
        $ph_email_message_association = $this->phactory->get( 'email_message_associations', array( 'email_message_id' => self::EMAIL_MESSAGE_ID ) );

        // Assert
        $this->assertNull( $ph_email_message_association );
    }

    /**
     * Remove Meta
     */
    public function testRemoveMeta() {
        // Create
        $this->phactory->create('email_message_meta');

        // Remove
        $this->email_message->id = self::EMAIL_MESSAGE_ID;
        $this->email_message->remove_meta();

        // Get
        $ph_email_message_meta = $this->phactory->get( 'email_message_meta', array( 'email_message_id' => self::EMAIL_MESSAGE_ID ) );

        // Assert
        $this->assertNull( $ph_email_message_meta );
    }

    /**
     * Get Dashboard Messages By Account
     */
    public function testGetDashboardMessagesByAccount() {
        // Create
        $this->phactory->create('email_messages');

        // Get
        $email_messages = $this->email_message->get_dashboard_messages_by_account( self::WEBSITE_ID );
        $email_message = current( $email_messages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailMessage', $email_messages );
        $this->assertEquals( self::SUBJECT, $email_message->subject );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('email_messages');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`subject`', '`status`', 'date_sent' );

        // Get
        $email_messages = $this->email_message->list_all( $dt->get_variables() );
        $email_message = current( $email_messages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailMessage', $email_messages );
        $this->assertEquals( self::SUBJECT, $email_message->subject );

        // Get rid of everything
        unset( $user, $_GET, $dt, $email_messages );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Stub User
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('email_messages');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`subject`', '`status`', 'date_sent' );

        // Get
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
