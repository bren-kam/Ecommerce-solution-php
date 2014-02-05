<?php

require_once 'test/base-database-test.php';

class SocialMediaCurrentAdTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const WEBSITE_PAGE_ID = 9;
    
    /**
     * @var SocialMediaCurrentAd
     */
    private $sm_current_ad;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_current_ad = new SocialMediaCurrentAd();

        // Define
        $this->phactory->define( 'sm_current_ad', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'website_page_id' => self::WEBSITE_PAGE_ID ) );
        $this->phactory->recall();
    }

    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_current_ad');

        // Get
        $this->sm_current_ad->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_current_ad->fb_page_id );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_current_ad->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_current_ad->website_page_id = self::WEBSITE_PAGE_ID;
        $this->sm_current_ad->create();

        // Get
        $ph_sm_current_ad = $this->phactory->get( 'sm_current_ad', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::WEBSITE_PAGE_ID, $ph_sm_current_ad->website_page_id );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_current_ad');

        // Save
        $this->sm_current_ad->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_current_ad->content = 'Help us help you!';
        $this->sm_current_ad->save();

        // Get
        $ph_sm_current_ad = $this->phactory->get( 'sm_current_ad', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( $this->sm_current_ad->content, $ph_sm_current_ad->content );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_current_ad = null;
    }
}
