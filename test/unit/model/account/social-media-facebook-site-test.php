<?php

require_once 'test/base-database-test.php';

class SocialMediaFacebookSiteTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const CONTENT = 'Misty Mountains';
    
    /**
     * @var SocialMediaFacebookSite
     */
    private $sm_facebook_site;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_facebook_site = new SocialMediaFacebookSite();
        
        // Define
        $this->phactory->define( 'sm_facebook_site', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'content' => self::CONTENT ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_facebook_site');

        // Get
        $this->sm_facebook_site->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_facebook_site->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_facebook_site->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_facebook_site->key = self::KEY;
        $this->sm_facebook_site->create();

        // Get
        $ph_sm_facebook_site = $this->phactory->get( 'sm_facebook_site', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_facebook_site->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_facebook_site');

        // Save
        $this->sm_facebook_site->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_facebook_site->content = self::CONTENT;
        $this->sm_facebook_site->save();

        // Get
        $ph_sm_facebook_site = $this->phactory->get( 'sm_facebook_site', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::CONTENT, $ph_sm_facebook_site->content );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_facebook_site = null;
    }
}
