<?php

require_once 'test/base-database-test.php';

class SocialMediaFanOfferTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const BEFORE = 'Misty Mountains';
    
    /**
     * @var SocialMediaFanOffer
     */
    private $sm_fan_offer;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_fan_offer = new SocialMediaFanOffer();
        
        // Define
        $this->phactory->define( 'sm_fan_offer', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'before' => self::BEFORE ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_fan_offer');

        // Get
        $this->sm_fan_offer->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_fan_offer->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_fan_offer->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_fan_offer->key = self::KEY;
        $this->sm_fan_offer->create();

        // Get
        $ph_sm_fan_offer = $this->phactory->get( 'sm_fan_offer', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_fan_offer->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_fan_offer');

        // Save
        $this->sm_fan_offer->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_fan_offer->before = self::BEFORE;
        $this->sm_fan_offer->save();

        // Get
        $ph_sm_fan_offer = $this->phactory->get( 'sm_fan_offer', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::BEFORE, $ph_sm_fan_offer->before );
    }
    
    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_fan_offer = null;
    }
}
