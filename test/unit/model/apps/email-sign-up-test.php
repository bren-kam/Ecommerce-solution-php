<?php

require_once 'test/base-database-test.php';

class EmailSignUpTest extends BaseDatabaseTest {
    const FB_PAGE_ID = 5;
    const TAB = 'Here lies earth';
    const KEY = 'Red Baron';

    // Websites
    const TITLE = 'Grimm Brothers';
    
    /**
     * @var EmailSignUp
     */
    private $email_sign_up;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->email_sign_up = new EmailSignUp();
        
        // Define
        $this->phactory->define( 'sm_email_sign_up', array( 'fb_page_id' => self::FB_PAGE_ID, 'tab' => self::TAB, 'key' => self::KEY ) );
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'websites', array( 'title' => self::TITLE ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Getting Tab
     */
    public function testGetTab() {
        // Create
        $this->phactory->create( 'sm_email_sign_up' );

        // Get
        $tab = $this->email_sign_up->get_tab( self::FB_PAGE_ID );

        // Assert
        $this->assertEquals( self::TAB, $tab );
    }

    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_email_sign_up', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $account = $this->email_sign_up->get_connected_website( self::FB_PAGE_ID );

        // Assert
        $this->assertEquals( self::TITLE, $account->title );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare
        $fb_page_id = 8;

        // Create
        $this->phactory->create('sm_email_sign_up');

        // Connect
        $this->email_sign_up->connect( $fb_page_id, self::KEY );

        // Get
        $ph_sm_email_sign_up = $this->phactory->get( 'sm_email_sign_up', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $fb_page_id, $ph_sm_email_sign_up->fb_page_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->email_sign_up = null;
    }
}
