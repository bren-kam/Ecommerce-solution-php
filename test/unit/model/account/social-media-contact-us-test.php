<?php

require_once 'test/base-database-test.php';

class SocialMediaContactUsTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const WEBSITE_PAGE_ID = 9;
    
    /**
     * @var SocialMediaContactUs
     */
    private $sm_contact_us;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_contact_us = new SocialMediaContactUs();
        
        // Define
        $this->phactory->define( 'sm_contact_us', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'website_page_id' => self::WEBSITE_PAGE_ID ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_contact_us');

        // Get
        $this->sm_contact_us->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_contact_us->fb_page_id );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_contact_us->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_contact_us->website_page_id = self::WEBSITE_PAGE_ID;
        $this->sm_contact_us->create();

        // Get
        $ph_sm_contact_us = $this->phactory->get( 'sm_contact_us', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::WEBSITE_PAGE_ID, $ph_sm_contact_us->website_page_id );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_contact_us');

        // Save
        $this->sm_contact_us->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_contact_us->content = 'Help us help you!';
        $this->sm_contact_us->save();

        // Get
        $ph_sm_contact_us = $this->phactory->get( 'sm_contact_us', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( $this->sm_contact_us->content, $ph_sm_contact_us->content );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_contact_us = null;
    }
}
