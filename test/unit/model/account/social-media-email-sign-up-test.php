<?php

require_once 'test/base-database-test.php';

class SocialMediaEmailSignUpTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const TAB = 'Misty Mountains';
    
    /**
     * @var SocialMediaEmailSignUp
     */
    private $sm_email_sign_up;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_email_sign_up = new SocialMediaEmailSignUp();
        
        // Define
        $this->phactory->define( 'sm_email_sign_up', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'tab' => self::TAB ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_email_sign_up');

        // Get
        $this->sm_email_sign_up->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_email_sign_up->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_email_sign_up->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_email_sign_up->key = self::KEY;
        $this->sm_email_sign_up->create();

        // Get
        $ph_sm_email_sign_up = $this->phactory->get( 'sm_email_sign_up', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_email_sign_up->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_email_sign_up');

        // Save
        $this->sm_email_sign_up->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_email_sign_up->tab = self::TAB;
        $this->sm_email_sign_up->save();

        // Get
        $ph_sm_email_sign_up = $this->phactory->get( 'sm_email_sign_up', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::TAB, $ph_sm_email_sign_up->tab );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_email_sign_up = null;
    }
}
