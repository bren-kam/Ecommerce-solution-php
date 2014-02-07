<?php

require_once 'test/base-database-test.php';

class PostingTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 3;
    const FB_PAGE_ID = 5;
    const FB_USER_ID = 9;
    const KEY = 'Red Baron';

    // Websites
    const TITLE = 'Grimm Brothers';
    
    /**
     * @var Posting
     */
    private $posting;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->posting = new Posting();
        
        // Define
        $this->phactory->define( 'sm_posting', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'fb_user_id' => self::FB_USER_ID, 'key' => self::KEY ) );
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'websites', array( 'title' => self::TITLE ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Connected
     */
    public function testConnected() {
        // Create
        $ph_sm_facebook_page = $this->phactory->create('sm_facebook_page');
        $this->phactory->create( 'sm_posting', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $connected = $this->posting->connected( self::FB_USER_ID );

        // Assert
        $this->assertTrue( $connected );

        // Reset
        $this->phactory->recall();

        // Create
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'status' => SocialMediaFacebookPage::STATUS_INACTIVE ) );
        $this->phactory->create( 'sm_posting', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $connected = $this->posting->connected( self::FB_USER_ID );

        // Assert
        $this->assertFalse( $connected );
    }


    /**
     * Test Getting Connected Pages
     */
    public function testGetConnectedPages() {
        // Craete
        $this->phactory->create('sm_posting');

        // Get
        $fb_page_ids = $this->posting->get_connected_pages( self::FB_USER_ID );
        $expected_fb_page_ids = array( self::FB_PAGE_ID );

        // Assert
        $this->assertEquals( $expected_fb_page_ids, $fb_page_ids );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare
        $access_token = 'Sweet wheels on cheese!';

        // Create
        $this->phactory->create('sm_posting');

        // Connect
        $this->posting->connect( self::FB_USER_ID, self::FB_PAGE_ID, $access_token, self::KEY );

        // Get
        $ph_sm_posting = $this->phactory->get( 'sm_posting', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $access_token, $ph_sm_posting->access_token );
    }

    /**
     * Test Update Access Token
     */
    public function testUpdateAccessToken() {
        // Declare
        $access_token = 'Sweet wheels on cheese!';

        // Create
        $this->phactory->create('sm_posting');

        // Update
        $this->posting->update_access_token( $access_token, self::FB_PAGE_ID );

        // Get
        $ph_sm_posting = $this->phactory->get( 'sm_posting', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $access_token, $ph_sm_posting->access_token );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->posting = null;
    }
}
