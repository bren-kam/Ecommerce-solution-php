<?php

require_once 'base-database-test.php';

class EmailTemplateTest extends BaseDatabaseTest {
    /**
     * @var EmailTemplate
     */
    private $email_template;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_template = new EmailTemplate();
    }

    /**
     * Get
     */
    public function testGet() {
        // Set variables
        $name = 'Brand New Template';
        $website_id = -3;

        // Create
        $email_template_id = $this->phactory->insert( 'email_templates', compact( 'name' ), 's' );
        $this->phactory->insert( 'email_template_associations', compact( 'email_template_id', 'website_id' ), 'ii' );

        // Get
        $this->email_template->get( $email_template_id, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $name, $this->email_template->name );

        // Cleanup
        $this->phactory->delete( 'email_templates', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'email_template_associations', compact( 'website_id' ), 'i' );
    }

    /**
     * Get Default
     */
    public function testGetDefault() {
        // Set variables
        $name = 'Brand New Template';
        $email_template_type = 'default';
        $website_id = -3;

        // Create
        $email_template_id = $this->phactory->insert( 'email_templates', array( 'name' => $name, 'type' => $email_template_type ), 'ss' );
        $this->phactory->insert( 'email_template_associations', compact( 'email_template_id', 'website_id' ), 'ii' );

        // Get
        $this->email_template->get_default( $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $name, $this->email_template->name );

        // Cleanup
        $this->phactory->delete( 'email_templates', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'email_template_associations', compact( 'website_id' ), 'i' );
    }

    /**
     * Get By Account
     */
    public function testGetByAccount() {
        // Set variables
        $name = 'Brand New Template';
        $email_template_type = 'default';
        $website_id = -3;

        // Create
        $email_template_id = $this->phactory->insert( 'email_templates', array( 'name' => $name, 'type' => $email_template_type ), 'ss' );
        $this->phactory->insert( 'email_template_associations', compact( 'email_template_id', 'website_id' ), 'ii' );

        // Get
        $email_templates = $this->email_template->get_by_account( $website_id );

        // Make sure we grabbed the right one
        $this->assertTrue( current( $email_templates ) instanceof EmailTemplate );

        // Cleanup
        $this->phactory->delete( 'email_templates', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'email_template_associations', compact( 'website_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->email_template->template = 'purple people eaters';
        $this->email_template->type = 'default';
        $this->email_template->create();

        $this->assertTrue( !is_null( $this->email_template->id ) );

        // Make sure it's in the database
        $template = $this->phactory->get_var( 'SELECT `template` FROM `email_templates` WHERE `email_template_id` = ' . (int) $this->email_template->id );

        $this->assertEquals( 'purple people eaters', $template );

        // Delete
        $this->phactory->delete( 'email_templates', array( 'email_template_id' => $this->email_template->id ), 'i' );
    }

    /**
     * Test Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create test
        $this->email_template->type = 'default';
        $this->email_template->create();

        // Update test
        $this->email_template->type = 'custom';
        $this->email_template->save();

        // Now check it!
        $type = $this->phactory->get_var( 'SELECT `type` FROM `email_templates` WHERE `email_template_id` = ' . (int) $this->email_template->id );

        $this->assertEquals( $type, $this->email_template->type );

        // Delete the email
        $this->phactory->delete( 'email_templates', array( 'email_template_id' => $this->email_template->id ), 'i' );
    }

    /**
     * Test Add Association
     */
    public function testAddAssociation() {
        // Declare variables
        $this->email_template->id = -3;
        $account_id = '-5';

        // Delete any associations before hand
        $this->phactory->delete( 'email_template_associations', array( 'email_template_id' => $this->email_template->id ) , 'i' );

        // Add association
        $this->email_template->add_association( $account_id );

        // Make sure it's in the database
        $fetched_website_id = $this->phactory->get_var( 'SELECT `website_id` FROM `email_template_associations` WHERE `email_template_id` = ' . (int) $this->email_template->id );

        $this->assertEquals( $fetched_website_id, $account_id );

        // Delete any associations after
        $this->phactory->delete( 'email_template_associations', array( 'email_template_id' => $this->email_template->id ) , 'i' );
    }

    /**
     * Test Get Complete - A
     */
    public function testGetCompleteA() {
        // Declare Variables
        $website_id = -7;
        $type = 'website';
        $account_title = "Jim's Hoops";
        $message = 'Take a look at our upcoming specials!';
        $subject = '[website_title] Specials!';
        $template = '|[subject]|[message]|';
        $settings = 'remove-header-footer';

        // Create Account Stub
        $stub_account = $this->getMock( 'Account' );
        $stub_account->expects($this->once())->method('get_settings')->with( $settings )->will($this->returnValue(false));
        $stub_account->title = $account_title; // Just to get more code coverage -- it should continue going

        // Email Message Stub
        $stub_email_message = $this->getMock( 'EmailMessage' );
        $stub_email_message->message = $message;
        $stub_email_message->subject = $subject;

        // Create
        $stub_email_message->email_template_id = $email_template_id = $this->phactory->insert( 'email_templates', compact( 'template' ), 's' );
        $this->email_template->template = $template;
        $this->phactory->insert( 'email_template_associations', array( 'email_template_id' => $email_template_id, 'object_id' => $website_id, 'type' => $type ), 'iis' );

        // Get HTML Message
        $html_message = $this->email_template->get_complete( $stub_account, $stub_email_message );
        $generated_subject = str_replace( '[website_title]', $account_title, $subject );
        $generated_template = str_replace( array( '[subject]', '[message]' ), array( $generated_subject, '<p>' . $message . '</p>' ), $template );

        $this->assertEquals( $html_message, $generated_template );

        $this->phactory->delete( 'email_templates', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'email_template_associations', array( 'object_id' => $website_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_template = null;
    }
}
