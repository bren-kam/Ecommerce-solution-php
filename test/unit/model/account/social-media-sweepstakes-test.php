<?php

require_once 'test/base-database-test.php';

class SocialMediaSweepstakesTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const BEFORE = 'Misty Mountains';
    
    /**
     * @var SocialMediaSweepstakes
     */
    private $sm_sweepstakes;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_sweepstakes = new SocialMediaSweepstakes();
        
        // Define
        $this->phactory->define( 'sm_sweepstakes', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'before' => self::BEFORE ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_sweepstakes');

        // Get
        $this->sm_sweepstakes->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_sweepstakes->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_sweepstakes->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_sweepstakes->key = self::KEY;
        $this->sm_sweepstakes->create();

        // Get
        $ph_sm_sweepstakes = $this->phactory->get( 'sm_sweepstakes', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_sweepstakes->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_sweepstakes');

        // Save
        $this->sm_sweepstakes->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_sweepstakes->before = self::BEFORE;
        $this->sm_sweepstakes->save();

        // Get
        $ph_sm_sweepstakes = $this->phactory->get( 'sm_sweepstakes', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::BEFORE, $ph_sm_sweepstakes->before );
    }
    
    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_sweepstakes = null;
    }
}
