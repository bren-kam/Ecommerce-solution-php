<?php

require_once 'base-database-test.php';

class SocialMediaFacebookSiteTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaFacebookSite
     */
    private $sm_facebook_site;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_facebook_site = new SocialMediaFacebookSite();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->db->insert( 'sm_facebook_site', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_facebook_site->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_facebook_site->fb_page_id );

        // Clean up
        $this->db->delete( 'sm_facebook_site', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $key = 'Poke';

        // Create
        $this->sm_facebook_site->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_facebook_site->key = $key;
        $this->sm_facebook_site->create();

        // Get
        $retrieved_key = $this->db->get_var( "SELECT `key` FROM `sm_facebook_site` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_key, $this->sm_facebook_site->key );

        // Clean up
        $this->db->delete( 'sm_facebook_site', compact( 'sm_facebook_page_id' ), 'i' );
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
        $this->sm_facebook_site->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_facebook_site->create();

        // Update test
        $this->sm_facebook_site->content = $content;
        $this->sm_facebook_site->save();

        // Now check it!
        $retrieved_content = $this->db->get_var( "SELECT `content` FROM `sm_facebook_site` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_content, $content );

        // Clean up
        $this->db->delete( 'sm_facebook_site', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_facebook_site = null;
    }
}
