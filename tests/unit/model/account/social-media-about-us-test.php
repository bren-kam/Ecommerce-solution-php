<?php

require_once 'base-database-test.php';

class SocialMediaAboutUsTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaAboutUs
     */
    private $sm_about_us;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_about_us = new SocialMediaAboutUs();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->db->insert( 'sm_about_us', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_about_us->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_about_us->fb_page_id );

        // Clean up
        $this->db->delete( 'sm_about_us', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $website_page_id = -7;

        // Create
        $this->sm_about_us->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_about_us->website_page_id = $website_page_id;
        $this->sm_about_us->create();

        // Get
        $retrieved_website_page_id = $this->db->get_var( "SELECT `website_page_id` FROM `sm_about_us` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_website_page_id, $this->sm_about_us->website_page_id );

        // Clean up
        $this->db->delete( 'sm_about_us', compact( 'sm_facebook_page_id' ), 'i' );
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
        $new_content = 'ekoP';

        // Create
        $this->sm_about_us->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_about_us->content = $content;
        $this->sm_about_us->create();

        // Update test
        $this->sm_about_us->content = $new_content;
        $this->sm_about_us->save();

        // Now check it!
        $retrieved_content = $this->db->get_var( "SELECT `content` FROM `sm_about_us` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_content, $new_content );

        // Clean up
        $this->db->delete( 'sm_about_us', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_about_us = null;
    }
}
