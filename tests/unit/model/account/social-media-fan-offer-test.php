<?php

require_once 'base-database-test.php';

class SocialMediaFanOfferTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaFanOffer
     */
    private $sm_fan_offer;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_fan_offer = new SocialMediaFanOffer();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->db->insert( 'sm_fan_offer', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_fan_offer->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_fan_offer->fb_page_id );

        // Clean up
        $this->db->delete( 'sm_fan_offer', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $key = 'Poke';

        // Create
        $this->sm_fan_offer->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_fan_offer->key = $key;
        $this->sm_fan_offer->create();

        // Get
        $retrieved_key = $this->db->get_var( "SELECT `key` FROM `sm_fan_offer` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_key, $this->sm_fan_offer->key );

        // Clean up
        $this->db->delete( 'sm_fan_offer', compact( 'sm_facebook_page_id' ), 'i' );
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
        $new_before = 'ekoP';

        // Create
        $this->sm_fan_offer->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_fan_offer->before = $before;
        $this->sm_fan_offer->create();

        // Update test
        $this->sm_fan_offer->before = $new_before;
        $this->sm_fan_offer->save();

        // Now check it!
        $retrieved_before = $this->db->get_var( "SELECT `before` FROM `sm_fan_offer` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_before, $new_before );

        // Clean up
        $this->db->delete( 'sm_fan_offer', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_fan_offer = null;
    }
}
