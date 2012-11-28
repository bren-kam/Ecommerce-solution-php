<?php

require_once 'base-database-test.php';

class CurrentAdTest extends BaseDatabaseTest {
    /**
     * @var CurrentAd
     */
    private $current_ad;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->current_ad = new CurrentAd();
    }

    /**
     * Test Getting Tab - A
     */
    public function testGetTabA() {
        // Declare variables
        $fb_page_id = -5;
        $content = 'Hip, Hip, Hurray!';
        $success = false;

        // Insert About Us
        $this->db->insert( 'sm_current_ad', array( 'website_page_id' => 0, 'fb_page_id' => $fb_page_id, 'content' => $content ), 'iis' );

        // Get it
        $tab = $this->current_ad->get_tab( $fb_page_id, $success );

        $this->assertEquals( $tab, $content );

        // Delete it
        $this->db->delete( 'sm_current_ad', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Getting Tab - B
     */
    public function testGetTabB() {
        // Declare variables
        $sm_facebook_page_id = -7;
        $fb_page_id = -5;
        $website_page_id = -3;
        $account_id = -9;
        $success = true;

        // Insert Website Page/FB Page/About Us
        $this->db->insert( 'websites', array( 'website_id' => $account_id, 'domain' => 'wit.ty', 'title' => 'Wag wag' ), 'iss' );
        $this->db->insert( 'website_pages', array( 'website_page_id' => $website_page_id, 'website_id' => $account_id, 'title' => 'Moose Lumps!', 'content' => 'Mooses are cool!' ), 'iiss' );
        $this->db->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id, 'status' => 1 ), 'iii' );
        $this->db->insert( 'sm_current_ad', array( 'website_page_id' => $website_page_id, 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id ), 'iii' );

        // Get it
        $tab = $this->current_ad->get_tab( $fb_page_id, $success );

        $this->assertTrue( is_string( $tab ) );

        // Delete it
        $this->db->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'website_pages', array( 'website_page_id' => $website_page_id ), 'i' );
        $this->db->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_current_ad', array( 'fb_page_id' => $fb_page_id ), 'i' );
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
        $this->db->insert( 'sm_current_ad', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'key' => $key ), 'iis' );

        $account = $this->current_ad->get_connected_website( $fb_page_id );

        $this->assertEquals( $account->key, $key );

        // Delete it
        $this->db->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_current_ad', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare variables
        $fb_page_id = -5;
        $key = 'Red Baron';

        // Insert About Us
        $this->db->insert( 'sm_current_ad', array( 'key' => $key,  ), 's' );

        // Get it
        $this->current_ad->connect( $fb_page_id, $key );

        // Get the key
        $fetched_fb_page_id = $this->db->get_var( "SELECT `fb_page_id` FROM `sm_current_ad` WHERE `key` = '$key'" );

        $this->assertEquals( $fb_page_id, $fetched_fb_page_id );

        // Delete it
        $this->db->delete( 'sm_current_ad', array( 'key' => $key ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->current_ad = null;
    }
}
