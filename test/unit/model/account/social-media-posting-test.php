<?php

require_once 'test/base-database-test.php';

class SocialMediaPostingTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaPosting
     */
    private $sm_posting;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_posting = new SocialMediaPosting();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->phactory->insert( 'sm_posting', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_posting->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_posting->fb_page_id );

        // Clean up
        $this->phactory->delete( 'sm_posting', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $key = 'Poke';

        // Create
        $this->sm_posting->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_posting->key = $key;
        $this->sm_posting->create();

        // Get
        $retrieved_key = $this->phactory->get_var( "SELECT `key` FROM `sm_posting` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_key, $this->sm_posting->key );

        // Clean up
        $this->phactory->delete( 'sm_posting', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $access_token = 'gobbledy-gook';

        // Create
        $this->sm_posting->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_posting->create();

        // Update test
        $this->sm_posting->fb_page_id = 0;
        $this->sm_posting->access_token = $access_token;
        $this->sm_posting->save();

        // Now check it!
        $retrieved_access_token = $this->phactory->get_var( "SELECT `access_token` FROM `sm_posting` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_access_token, $access_token );

        // Clean up
        $this->phactory->delete( 'sm_posting', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_posting = null;
    }
}
