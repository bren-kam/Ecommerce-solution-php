<?php

require_once 'test/base-database-test.php';

class SocialMediaContactUsTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaContactUs
     */
    private $sm_contact_us;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_contact_us = new SocialMediaContactUs();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->phactory->insert( 'sm_contact_us', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_contact_us->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_contact_us->fb_page_id );

        // Clean up
        $this->phactory->delete( 'sm_contact_us', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $website_page_id = -7;

        // Create
        $this->sm_contact_us->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_contact_us->website_page_id = $website_page_id;
        $this->sm_contact_us->create();

        // Get
        $retrieved_website_page_id = $this->phactory->get_var( "SELECT `website_page_id` FROM `sm_contact_us` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_website_page_id, $this->sm_contact_us->website_page_id );

        // Clean up
        $this->phactory->delete( 'sm_contact_us', compact( 'sm_facebook_page_id' ), 'i' );
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
        $this->sm_contact_us->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_contact_us->create();

        // Update test
        $this->sm_contact_us->content = $content;
        $this->sm_contact_us->save();

        // Now check it!
        $retrieved_content = $this->phactory->get_var( "SELECT `content` FROM `sm_contact_us` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_content, $content );

        // Clean up
        $this->phactory->delete( 'sm_contact_us', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_contact_us = null;
    }
}
