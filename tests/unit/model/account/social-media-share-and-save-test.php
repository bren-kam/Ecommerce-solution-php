<?php

require_once 'base-database-test.php';

class SocialMediaShareAndSaveTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaShareAndSave
     */
    private $sm_share_and_save;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_share_and_save = new SocialMediaShareAndSave();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->db->insert( 'sm_share_and_save', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_share_and_save->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_share_and_save->fb_page_id );

        // Clean up
        $this->db->delete( 'sm_share_and_save', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $key = 'Poke';

        // Create
        $this->sm_share_and_save->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_share_and_save->key = $key;
        $this->sm_share_and_save->create();

        // Get
        $retrieved_key = $this->db->get_var( "SELECT `key` FROM `sm_share_and_save` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_key, $this->sm_share_and_save->key );

        // Clean up
        $this->db->delete( 'sm_share_and_save', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $before = 'Poke';

        // Create
        $this->sm_share_and_save->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_share_and_save->create();

        // Update test
        $this->sm_share_and_save->before = $before;
        $this->sm_share_and_save->save();

        // Now check it!
        $retrieved_before = $this->db->get_var( "SELECT `before` FROM `sm_share_and_save` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_before, $before );

        // Clean up
        $this->db->delete( 'sm_share_and_save', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_share_and_save = null;
    }
}
