<?php

require_once 'test/base-database-test.php';

class ShareAndSaveTest extends BaseDatabaseTest {
    const FB_PAGE_ID = 5;
    const BEFORE = 'Like us and receive 10% off!';
    const AFTER = 'Here is your coupon!';
    const KEY = 'Red Baron';

    // Websites
    const TITLE = 'Grimm Brothers';

    /**
     * @var ShareAndSave
     */
    private $share_and_save;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->share_and_save = new ShareAndSave();

        // Define
        $this->phactory->define( 'sm_share_and_save', array( 'fb_page_id' => self::FB_PAGE_ID, 'before' => self::BEFORE, 'after' => self::AFTER, 'key' => self::KEY ) );
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'websites', array( 'title' => self::TITLE ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting Tab
     */
    public function testGetTab() {
        // Create
        $this->phactory->create( 'sm_share_and_save' );

        // Get
        $liked = false;
        $share_and_save = $this->share_and_save->get_tab( self::FB_PAGE_ID, $liked );

        // Assert
        $this->assertEquals( self::BEFORE, $share_and_save->content );

        // Get
        $liked = true;
        $share_and_save = $this->share_and_save->get_tab( self::FB_PAGE_ID, $liked );

        // Assert
        $this->assertEquals( self::AFTER, $share_and_save->content );
    }

    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_share_and_save', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $account = $this->share_and_save->get_connected_website( self::FB_PAGE_ID );

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
        $this->phactory->create('sm_share_and_save');

        // Connect
        $this->share_and_save->connect( $fb_page_id, self::KEY );

        // Get
        $ph_sm_share_and_save = $this->phactory->get( 'sm_share_and_save', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $fb_page_id, $ph_sm_share_and_save->fb_page_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->share_and_save = null;
    }
}
