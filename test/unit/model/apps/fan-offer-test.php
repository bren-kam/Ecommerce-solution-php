<?php

require_once 'test/base-database-test.php';

class FanOfferTest extends BaseDatabaseTest {
    const FB_PAGE_ID = 5;
    const BEFORE = 'Like us and receive 10% off!';
    const AFTER = 'Here is your coupon!';
    const KEY = 'Red Baron';

    // Website Pages
    const WEBSITE_PAGE_TITLE = 'Rumpelstiltskin';
    const WEBSITE_PAGE_CONTENT = 'Fairy Tales';

    // Websites
    const TITLE = 'Grimm Brothers';
    
    /**
     * @var FanOffer
     */
    private $fan_offer;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->fan_offer = new FanOffer();
        
        // Define
        $this->phactory->define( 'sm_fan_offer', array( 'fb_page_id' => self::FB_PAGE_ID, 'before' => self::BEFORE, 'after' => self::AFTER, 'key' => self::KEY ) );
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
        $this->phactory->create( 'sm_fan_offer' );

        // Get
        $liked = false;
        $fan_offer = $this->fan_offer->get_tab( self::FB_PAGE_ID, $liked );

        // Assert
        $this->assertEquals( self::BEFORE, $fan_offer->content );

        // Get
        $liked = true;
        $fan_offer = $this->fan_offer->get_tab( self::FB_PAGE_ID, $liked );

        // Assert
        $this->assertEquals( self::AFTER, $fan_offer->content );
    }

    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_fan_offer', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $account = $this->fan_offer->get_connected_website( self::FB_PAGE_ID );

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
        $this->phactory->create('sm_fan_offer');

        // Connect
        $this->fan_offer->connect( $fb_page_id, self::KEY );

        // Get
        $ph_sm_fan_offer = $this->phactory->get( 'sm_fan_offer', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $fb_page_id, $ph_sm_fan_offer->fb_page_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->fan_offer = null;
    }
}
