<?php

require_once 'test/base-database-test.php';

class EmailTemplateTest extends BaseDatabaseTest {
    const NAME = 'Brand New Day';
    const TYPE = 'default';
    const TEMPLATE = '|[subject]|[message]|';

    // Email Template Associations
    const EMAIL_TEMPLATE_ID = 15;

    /**
     * @var EmailTemplate
     */
    private $email_template;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_template = new EmailTemplate();

        // Define
        $this->phactory->define( 'email_templates', array( 'name' => self::NAME, 'type' => self::TYPE, 'template' => self::TEMPLATE ) );
        $this->phactory->define( 'email_template_associations', array( 'email_template_id' => self::EMAIL_TEMPLATE_ID, 'website_id' => self::WEBSITE_ID ) );
        $this->phactory->recall();
    }

    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_email_template = $this->phactory->create('email_templates');
        $this->phactory->create( 'email_template_associations', array( 'email_template_id' => $ph_email_template->email_template_id ) );

        // Get
        $this->email_template->get( $ph_email_template->email_template_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->email_template->name );
    }

    /**
     * Get Default
     */
    public function testGetDefault() {
        // Create
        $ph_email_template = $this->phactory->create('email_templates');
        $this->phactory->create( 'email_template_associations', array( 'email_template_id' => $ph_email_template->email_template_id ) );

        // Get
        $this->email_template->get_default( self::WEBSITE_ID );

        // Make sure we grabbed the right one
        $this->assertEquals( self::NAME, $this->email_template->name );
    }

    /**
     * Get By Account
     */
    public function testGetByAccount() {
        // Create
        $ph_email_template = $this->phactory->create('email_templates');
        $this->phactory->create( 'email_template_associations', array( 'email_template_id' => $ph_email_template->email_template_id ) );

        // Get
        $email_templates = $this->email_template->get_by_account( self::WEBSITE_ID );
        $email_template = current( $email_templates );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'EmailTemplate', $email_templates );
        $this->assertEquals( self::NAME, $email_template->name );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->email_template->name = self::NAME;
        $this->email_template->create();

        // Assert
        $this->assertNotNull( $this->email_template->id );

        // Get
        $ph_email_template = $this->phactory->get( 'email_templates', array( 'email_template_id' => $this->email_template->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_email_template->name );
    }

    /**
     * Test Save
     */
    public function testSave() {
        // Create
        $ph_email_template = $this->phactory->create('email_templates');

        // Save
        $this->email_template->id = $ph_email_template->email_template_id;
        $this->email_template->type = 'custom';
        $this->email_template->save();

        // Get
        $ph_email_template = $this->phactory->get( 'email_templates', array( 'email_template_id' => $ph_email_template->email_template_id ) );

        // Assert
        $this->assertEquals( $this->email_template->type, $ph_email_template->type );
    }

    /**
     * Test Add Association
     */
    public function testAddAssociation() {
        // Add association
        $this->email_template->id = self::EMAIL_TEMPLATE_ID;
        $this->email_template->add_association( self::WEBSITE_ID );

        // Get
        $ph_email_template_association = $this->phactory->get( 'email_template_associations', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEquals( self::EMAIL_TEMPLATE_ID, $ph_email_template_association->email_template_id );
    }

    /**
     * Test Get Complete
     *
     * @depends testGet
     * @depends testGetDefault
     */
    public function testGetCompleteA() {
        // Declare
        $settings = 'remove-header-footer';
        $message = 'Take a look at our upcoming specials!';
        $subject = '[website_title] Specials!';
        $account_title = "Jim's Hoops";
        $this->email_template->template = self::TEMPLATE;

        // Stubs
        $stub_account = $this->getMock('Account');
        $stub_account->title = $account_title;
        $stub_account->expects($this->once())->method('get_settings')->with( $settings )->will($this->returnValue(false));

        $stub_email_message = $this->getMock( 'EmailMessage' );
        $stub_email_message->message = $message;
        $stub_email_message->subject = $subject;

        // Create
        $ph_email_template = $this->phactory->create('email_templates');
        $this->phactory->create( 'email_template_associations', array( 'email_template_id' => $ph_email_template->email_template_id ) );

        // Assign
        $stub_email_message->email_template_id = $ph_email_template->email_template_id;

        // Get HTML Message
        $html_message = $this->email_template->get_complete( $stub_account, $stub_email_message );
        $generated_subject = str_replace( '[website_title]', $account_title, $subject );
        $generated_template = str_replace( array( '[subject]', '[message]' ), array( $generated_subject, '<p>' . $message . '</p>' ), self::TEMPLATE );

        $this->assertEquals( $html_message, $generated_template );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_template = null;
    }
}
