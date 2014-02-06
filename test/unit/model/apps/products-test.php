<?php

require_once 'test/base-database-test.php';

class security {
	public static function is_ssl() {
		return false;
	}
}

class ProductsTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 3;
    const FB_PAGE_ID = 5;
    const CONTENT = 'Here lies earth';
    const KEY = 'Red Baron';

    // Websites
    const TITLE = 'Grimm Brothers';

    /**
     * @var Products
     */
    private $products;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->products = new Products();

        // Define
        $this->phactory->define( 'sm_products', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'fb_page_id' => self::FB_PAGE_ID, 'content' => self::CONTENT, 'key' => self::KEY ) );
        $this->phactory->define( 'sm_facebook_page', array( 'website_id' => self::WEBSITE_ID, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'websites', array( 'title' => self::TITLE ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting Tab - A
     */
    public function testGetTab() {
        // Declare
        $product_catalog = 0;

        // Create
        $ph_website = $this->phactory->create( 'websites', array( 'product_catalog' => $product_catalog ) );
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_products', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $tab = $this->products->get_tab( self::FB_PAGE_ID );

        // Assert
        $this->assertEquals( self::CONTENT, $tab );

        // Reset
        $this->phactory->recall();

        // Declare
        $product_catalog = 1;

        // Create
        $ph_website = $this->phactory->create( 'websites', array( 'product_catalog' => $product_catalog ) );
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_products', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $tab = $this->products->get_tab( self::FB_PAGE_ID );

        // Assert
        $this->assertTrue( is_string( $tab ) );
    }
    
    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_sm_facebook_page = $this->phactory->create( 'sm_facebook_page', array( 'website_id' => $ph_website->website_id ) );
        $this->phactory->create( 'sm_products', array( 'sm_facebook_page_id' => $ph_sm_facebook_page->id ) );

        // Get
        $account = $this->products->get_connected_website( self::FB_PAGE_ID );

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
        $this->phactory->create('sm_products');

        // Connect
        $this->products->connect( $fb_page_id, self::KEY );

        // Get
        $ph_sm_products = $this->phactory->get( 'sm_products', array( 'key' => self::KEY ) );

        // Assert
        $this->assertEquals( $fb_page_id, $ph_sm_products->fb_page_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->products = null;
    }
}
