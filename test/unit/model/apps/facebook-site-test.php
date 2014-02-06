<?php

require_once 'test/base-database-test.php';

class FacebookSiteTest extends BaseDatabaseTest {
    const FB_PAGE_ID = 5;
    const CONTENT = 'Here lies earth';
    const KEY = 'Red Baron';

    // Website Pages
    const WEBSITE_PAGE_TITLE = 'Rumpelstiltskin';
    const WEBSITE_PAGE_CONTENT = 'Fairy Tales';

    // Websites
    const TITLE = 'Grimm Brothers';
    
    /**
     * @var FacebookSite
     */
    private $facebook_site;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->facebook_site = new FacebookSite();
        
        // Define
        $this->phactory->define( 'sm_facebook_site', array( 'fb_page_id' => self::FB_PAGE_ID, 'content' => self::CONTENT, 'key' => self::KEY ) );
        $this->phactory->define( 'website_pages', array( 'website_id' => self::WEBSITE_ID, 'title' => self::WEBSITE_PAGE_TITLE, 'content' => self::WEBSITE_PAGE_CONTENT ) );
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'websites', array( 'title' => self::TITLE ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Getting Tab
     */
    public function testGetTab() {
        // Create
        $this->phactory->create( 'sm_facebook_site' );

        // Get
        $tab = $this->facebook_site->get_tab( self::FB_PAGE_ID );

        // Assert
        $this->assertEquals( self::CONTENT, $tab );
    }

    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_facebook_site', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $account = $this->facebook_site->get_connected_website( self::FB_PAGE_ID );

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
        $this->phactory->create('sm_facebook_site');

        // Connect
        $this->facebook_site->connect( $fb_page_id, self::KEY );

        // Get
        $ph_sm_facebook_site = $this->phactory->get( 'sm_facebook_site', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $fb_page_id, $ph_sm_facebook_site->fb_page_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->facebook_site = null;
    }
}
