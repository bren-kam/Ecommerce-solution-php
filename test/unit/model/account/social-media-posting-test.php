<?php

require_once 'test/base-database-test.php';

class SocialMediaPostingTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const ACCESS_TOKEN = 'Misty Mountains';
    
    /**
     * @var SocialMediaPosting
     */
    private $sm_posting;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_posting = new SocialMediaPosting();
        
        // Define
        $this->phactory->define( 'sm_posting', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'access_token' => self::ACCESS_TOKEN ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_posting');

        // Get
        $this->sm_posting->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_posting->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_posting->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_posting->key = self::KEY;
        $this->sm_posting->create();

        // Get
        $ph_sm_posting = $this->phactory->get( 'sm_posting', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_posting->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_posting');

        // Save
        $this->sm_posting->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_posting->access_token = self::ACCESS_TOKEN;
        $this->sm_posting->save();

        // Get
        $ph_sm_posting = $this->phactory->get( 'sm_posting', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::ACCESS_TOKEN, $ph_sm_posting->access_token );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_posting = null;
    }
}
