<?php

require_once 'base-database-test.php';

class security {
	public static function is_ssl() {
		return false;
	}
}

class ProductsTest extends BaseDatabaseTest {
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
    }

    /**
     * Test Getting Tab - A
     */
    public function testGetTabA() {
        // Declare variables
        $fb_page_id = -5;
        $sm_facebook_page_id = -7;
        $account_id = -9;
        $product_catalog = 0;
        $content = 'Hip, Hip, Hurray!';

        // Insert About Us
        $this->phactory->insert( 'websites', array( 'website_id' => $account_id, 'title' => 'Banagrams', 'product_catalog' => $product_catalog ), 'isi' );
        $this->phactory->insert( 'sm_products', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'content' => $content ), 'iis' );
        $this->phactory->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id ), 'ii' );

        // Get it
        $tab = $this->products->get_tab( $fb_page_id );

        $this->assertTrue( is_string( $tab ) );

        // Delete it
        $this->phactory->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->phactory->delete( 'sm_products', array( 'fb_page_id' => $fb_page_id ), 'i' );
        $this->phactory->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Test Getting Tab - B
     */
    public function testGetTabB() {
        // Declare variables
        $fb_page_id = -5;
        $sm_facebook_page_id = -7;
        $account_id = -9;
        $product_catalog = 1;
        $content = 'Hip, Hip, Hurray!';

        // Insert About Us
        $this->phactory->insert( 'websites', array( 'website_id' => $account_id, 'title' => 'Banagrams', 'product_catalog' => $product_catalog ), 'isi' );
        $this->phactory->insert( 'sm_products', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'content' => $content ), 'iis' );
        $this->phactory->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id ), 'iii' );

        // Get it
        $tab = $this->products->get_tab( $fb_page_id );

        $this->assertTrue( is_string( $tab ) );

        // Delete it
        $this->phactory->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->phactory->delete( 'sm_products', array( 'fb_page_id' => $fb_page_id ), 'i' );
        $this->phactory->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Test Get Connected Website
     */
    public function testGetConnectedWebsite() {
        // Declare variables
        $account_id = -9;
        $sm_facebook_page_id = -7;
        $fb_page_id = -5;
        $key = 'Sirius Black';

        // Insert Website Page/FB Page/About Us
        $this->phactory->insert( 'websites', array( 'website_id' => $account_id, 'title' => 'Banagrams' ), 'is' );
        $this->phactory->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id ), 'iii' );
        $this->phactory->insert( 'sm_products', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'key' => $key ), 'iis' );

        $account = $this->products->get_connected_website( $fb_page_id );

        $this->assertEquals( $account->key, $key );

        // Delete it
        $this->phactory->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->phactory->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->phactory->delete( 'sm_products', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare variables
        $fb_page_id = -5;
        $key = 'Red Baron';

        // Insert About Us
        $this->phactory->insert( 'sm_products', array( 'key' => $key,  ), 's' );

        // Get it
        $this->products->connect( $fb_page_id, $key );

        // Get the key
        $fetched_fb_page_id = $this->phactory->get_var( "SELECT `fb_page_id` FROM `sm_products` WHERE `key` = '$key'" );

        $this->assertEquals( $fb_page_id, $fetched_fb_page_id );

        // Delete it
        $this->phactory->delete( 'sm_products', array( 'key' => $key ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->products = null;
    }
}
