<?php

require_once 'base-database-test.php';

class SocialMediaProductsTest extends BaseDatabaseTest {
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
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->phactory->insert( 'sm_products', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_products->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_products->fb_page_id );

        // Clean up
        $this->phactory->delete( 'sm_products', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $key = 'Poke';

        // Create
        $this->sm_products->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_products->key = $key;
        $this->sm_products->create();

        // Get
        $retrieved_key = $this->phactory->get_var( "SELECT `key` FROM `sm_products` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_key, $this->sm_products->key );

        // Clean up
        $this->phactory->delete( 'sm_products', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $content = 'Poke';

        // Create
        $this->sm_products->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_products->create();

        // Update test
        $this->sm_products->content = $content;
        $this->sm_products->save();

        // Now check it!
        $retrieved_content = $this->phactory->get_var( "SELECT `content` FROM `sm_products` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_content, $content );

        // Clean up
        $this->phactory->delete( 'sm_products', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_products = null;
    }
}
