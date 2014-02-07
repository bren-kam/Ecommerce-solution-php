<?php

require_once 'test/base-database-test.php';

class SocialMediaProductsTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 5;
    const FB_PAGE_ID = 3;
    const KEY = 'beal';
    const CONTENT = 'Misty Mountains';
    
    /**
     * @var SocialMediaProducts
     */
    private $sm_products;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_products = new SocialMediaProducts();
        
        // Define
        $this->phactory->define( 'sm_products', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'key' => self::KEY, 'content' => self::CONTENT ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $this->phactory->create('sm_products');

        // Get
        $this->sm_products->get( self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( self::FB_PAGE_ID, $this->sm_products->fb_page_id );
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->sm_products->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_products->key = self::KEY;
        $this->sm_products->create();

        // Get
        $ph_sm_products = $this->phactory->get( 'sm_products', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::KEY, $ph_sm_products->key );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create
        $this->phactory->create('sm_products');

        // Save
        $this->sm_products->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->sm_products->content = self::CONTENT;
        $this->sm_products->save();

        // Get
        $ph_sm_products = $this->phactory->get( 'sm_products', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID) );

        // Get
        $this->assertEquals( self::CONTENT, $ph_sm_products->content );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_products = null;
    }
}
