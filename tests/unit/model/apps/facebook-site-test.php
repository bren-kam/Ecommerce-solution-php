<?php

require_once 'base-database-test.php';

class FacebookSiteTest extends BaseDatabaseTest {
    /**
     * @var FacebookSite
     */
    private $facebook_site;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->facebook_site = new FacebookSite();
    }

    /**
     * Test Getting Tab
     */
    public function testGetTab() {
        // Declare variables
        $fb_page_id = -5;
        $content = 'Hip, Hip, Hurray!';

        // Insert About Us
        $this->db->insert( 'sm_facebook_site', array( 'fb_page_id' => $fb_page_id, 'content' => $content ), 'is' );

        // Get it
        $tab = $this->facebook_site->get_tab( $fb_page_id );

        $this->assertEquals( $content, $tab );

        // Delete it
        $this->db->delete( 'sm_facebook_site', array( 'fb_page_id' => $fb_page_id ), 'i' );
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
        $this->db->insert( 'sm_facebook_site', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'key' => $key ), 'iis' );

        $account = $this->facebook_site->get_connected_website( $fb_page_id );

        $this->assertEquals( $account->key, $key );

        // Delete it
        $this->db->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_facebook_site', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare variables
        $fb_page_id = -5;
        $key = 'Red Baron';

        // Insert About Us
        $this->db->insert( 'sm_facebook_site', array( 'key' => $key,  ), 's' );

        // Get it
        $this->facebook_site->connect( $fb_page_id, $key );

        // Get the key
        $fetched_fb_page_id = $this->db->get_var( "SELECT `fb_page_id` FROM `sm_facebook_site` WHERE `key` = '$key'" );

        $this->assertEquals( $fb_page_id, $fetched_fb_page_id );

        // Delete it
        $this->db->delete( 'sm_facebook_site', array( 'key' => $key ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->facebook_site = null;
    }
}
