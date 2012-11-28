<?php

require_once 'base-database-test.php';

class FanOfferTest extends BaseDatabaseTest {
    /**
     * @var FanOffer
     */
    private $fan_offer;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->fan_offer = new FanOffer();
    }

    /**
     * Test Getting Tab
     */
    public function testGetTabA() {
        // Declare variables
        $fb_page_id = -5;
        $liked = false;
        $before = 'What do you need to like me?';

        // Insert About Us
        $this->db->insert( 'sm_fan_offer', array( 'fb_page_id' => $fb_page_id, 'before' => $before ), 'is' );

        // Get it
        $tab = $this->fan_offer->get_tab( $fb_page_id, $liked );

        $this->assertEquals( $tab->content, $before );

        // Delete it
        $this->db->delete( 'sm_fan_offer', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Getting Tab - B
     */
    public function testGetTabB() {
        // Declare variables
        $fb_page_id = -5;
        $liked = true;
        $after = 'Yay! You liked me!';

        // Insert About Us
        $this->db->insert( 'sm_fan_offer', array( 'fb_page_id' => $fb_page_id, 'after' => $after ), 'is' );

        // Get it
        $tab = $this->fan_offer->get_tab( $fb_page_id, $liked );

        $this->assertEquals( $after, $tab->content );

        // Delete it
        $this->db->delete( 'sm_fan_offer', array( 'fb_page_id' => $fb_page_id ), 'i' );
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
        $this->db->insert( 'websites', array( 'website_id' => $account_id, 'title' => 'Banagrams' ), 'is' );
        $this->db->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id ), 'iii' );
        $this->db->insert( 'sm_fan_offer', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'key' => $key ), 'iis' );

        $account = $this->fan_offer->get_connected_website( $fb_page_id );

        $this->assertEquals( $account->key, $key );

        // Delete it
        $this->db->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_fan_offer', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare variables
        $fb_page_id = -5;
        $key = 'Red Baron';

        // Insert About Us
        $this->db->insert( 'sm_fan_offer', array( 'key' => $key,  ), 's' );

        // Get it
        $this->fan_offer->connect( $fb_page_id, $key );

        // Get the key
        $fetched_fb_page_id = $this->db->get_var( "SELECT `fb_page_id` FROM `sm_fan_offer` WHERE `key` = '$key'" );

        $this->assertEquals( $fb_page_id, $fetched_fb_page_id );

        // Delete it
        $this->db->delete( 'sm_fan_offer', array( 'key' => $key ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->fan_offer = null;
    }
}
