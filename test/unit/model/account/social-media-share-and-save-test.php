<?php

require_once 'test/base-database-test.php';

class SocialMediaShareAndSaveTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const BEFORE = 'Misty Mountains';
    
    /**
     * @var SocialMediaShareAndSave
     */
    private $sm_share_and_save;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_share_and_save = new SocialMediaShareAndSave();
        
        // Define
        $this->phactory->define( 'sm_share_and_save', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'before' => self::BEFORE ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_share_and_save');

        // Get
        $this->sm_share_and_save->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_share_and_save->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_share_and_save->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_share_and_save->key = self::KEY;
        $this->sm_share_and_save->create();

        // Get
        $ph_sm_share_and_save = $this->phactory->get( 'sm_share_and_save', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_share_and_save->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_share_and_save');

        // Save
        $this->sm_share_and_save->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_share_and_save->before = self::BEFORE;
        $this->sm_share_and_save->save();

        // Get
        $ph_sm_share_and_save = $this->phactory->get( 'sm_share_and_save', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::BEFORE, $ph_sm_share_and_save->before );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_share_and_save = null;
    }
}
