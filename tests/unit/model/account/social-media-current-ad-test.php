<?php

require_once 'base-database-test.php';

class SocialMediaCurrentAdTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaCurrentAd
     */
    private $sm_current_ad;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_current_ad = new SocialMediaCurrentAd();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->phactory->insert( 'sm_current_ad', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_current_ad->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_current_ad->fb_page_id );

        // Clean up
        $this->phactory->delete( 'sm_current_ad', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $website_page_id = -7;

        // Create
        $this->sm_current_ad->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_current_ad->website_page_id = $website_page_id;
        $this->sm_current_ad->create();

        // Get
        $retrieved_website_page_id = $this->phactory->get_var( "SELECT `website_page_id` FROM `sm_current_ad` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_website_page_id, $this->sm_current_ad->website_page_id );

        // Clean up
        $this->phactory->delete( 'sm_current_ad', compact( 'sm_facebook_page_id' ), 'i' );
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
        $this->sm_current_ad->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_current_ad->create();

        // Update test
        $this->sm_current_ad->content = $content;
        $this->sm_current_ad->save();

        // Now check it!
        $retrieved_content = $this->phactory->get_var( "SELECT `content` FROM `sm_current_ad` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_content, $content );

        // Clean up
        $this->phactory->delete( 'sm_current_ad', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_current_ad = null;
    }
}
