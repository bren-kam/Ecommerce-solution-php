<?php

require_once 'test/base-database-test.php';

class SweepstakesTest extends BaseDatabaseTest {
    const FB_PAGE_ID = 5;
    const BEFORE = 'Like us and receive 10% off!';
    const AFTER = 'Here is your coupon!';
    const KEY = 'Red Baron';

    // Websites
    const TITLE = 'Grimm Brothers';

    /**
     * @var Sweepstakes
     */
    private $sweepstakes;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sweepstakes = new Sweepstakes();
        
        // Define
        $this->phactory->define( 'sm_sweepstakes', array( 'fb_page_id' => self::FB_PAGE_ID, 'before' => self::BEFORE, 'after' => self::AFTER, 'key' => self::KEY ) );
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'websites', array( 'title' => self::TITLE ) );
        $this->phactory->recall();
    }
    
    /**
     * Test Getting Tab
     */
    public function testGetTab() {
        // Create
        $this->phactory->create( 'sm_sweepstakes' );

        // Get
        $liked = false;
        $sweepstakes = $this->sweepstakes->get_tab( self::FB_PAGE_ID, $liked );

        // Assert
        $this->assertEquals( self::BEFORE, $sweepstakes->content );

        // Get
        $liked = true;
        $sweepstakes = $this->sweepstakes->get_tab( self::FB_PAGE_ID, $liked );

        // Assert
        $this->assertEquals( self::AFTER, $sweepstakes->content );
    }

    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_sweepstakes', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $account = $this->sweepstakes->get_connected_website( self::FB_PAGE_ID );

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
        $this->phactory->create('sm_sweepstakes');

        // Connect
        $this->sweepstakes->connect( $fb_page_id, self::KEY );

        // Get
        $ph_sm_sweepstakes = $this->phactory->get( 'sm_sweepstakes', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $fb_page_id, $ph_sm_sweepstakes->fb_page_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sweepstakes = null;
    }
}
