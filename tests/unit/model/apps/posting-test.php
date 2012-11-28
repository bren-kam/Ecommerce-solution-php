<?php

require_once 'base-database-test.php';

class PostingTest extends BaseDatabaseTest {
    /**
     * @var Posting
     */
    private $posting;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->posting = new Posting();
    }

    /**
     * Test Connected - A
     */
    public function testConnectedA() {
        // Declare variables
        $account_id = -9;
        $sm_facebook_page_id = -7;
        $fb_user_id = -3;
        $status = 1;

        // Insert Website Page/FB Page/About Us
        $this->db->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id, 'status' => $status ), 'iii' );
        $this->db->insert( 'sm_posting', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_user_id' => $fb_user_id ), 'ii' );

        $connected = $this->posting->connected( $fb_user_id );

        $this->assertTrue( $connected );

        $this->db->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_posting', array( 'fb_user_id' => $fb_user_id ), 'i' );
    }

    /**
     * Test Connected - B
     */
    public function testConnectedB() {
        // Declare variables
        $account_id = -9;
        $sm_facebook_page_id = -7;
        $fb_user_id = -3;
        $status = 0;

        // Insert Website Page/FB Page/About Us
        $this->db->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id, 'status' => $status ), 'iii' );
        $this->db->insert( 'sm_posting', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_user_id' => $fb_user_id ), 'ii' );

        $connected = $this->posting->connected( $fb_user_id );

        $this->assertFalse( $connected );

        $this->db->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->db->delete( 'sm_posting', array( 'fb_user_id' => $fb_user_id ), 'i' );
    }

    /**
     * Test Getting Connected Pages
     */
    public function testGetConnectedPages() {
        // Declare variables
        $sm_facebook_page_id = -7;
        $fb_user_id = -3;

        // Add posting
        $this->db->insert( 'sm_posting', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_user_id' => $fb_user_id ), 'ii' );

        $fb_page_id = $this->db->get_insert_id();

        // Get the IDS
        $fb_page_ids = $this->posting->get_connected_pages( $fb_user_id );

        $this->assertTrue( in_array( $fb_page_id, $fb_page_ids ) );

        // Delete
        $this->db->delete( 'sm_posting', array( 'fb_user_id' => $fb_user_id ), 'i' );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare variables
        $fb_user_id = -1;
        $fb_page_id = -5;
        $access_token = 'Sweet wheels on cheese!';
        $key = 'Red Baron';

        // Insert About Us
        $this->db->insert( 'sm_posting', array( 'fb_user_id' => $fb_user_id, 'key' => $key ), 's' );

        // Get it
        $this->posting->connect( $fb_user_id, $fb_page_id, $access_token, $key );

        // Get the key
        $fetched_access_token = $this->db->get_var( "SELECT `access_token` FROM `sm_posting` WHERE `key` = '$key'" );

        $this->assertEquals( $access_token, $fetched_access_token );

        // Delete it
        $this->db->delete( 'sm_posting', array( 'key' => $key ), 's' );
    }

    /**
     * Test Update Access Token
     */
    public function testUpdateAccessToken() {
        // Declare variables
        $fb_user_id = -3;
        $fb_page_id = -5;
        $access_token = 'Ring around the rosey';

        // Insert About Us
        $this->db->insert( 'sm_posting', array( 'fb_page_id' => $fb_page_id, 'fb_user_id' => $fb_user_id ), 's' );

        // Get it
        $this->posting->update_access_token( $fb_user_id, $access_token, $fb_page_id );

        // Get the key
        $fetched_access_token = $this->db->get_var( 'SELECT `access_token` FROM `sm_posting` WHERE `fb_page_id` = ' . (int) $fb_page_id );

        $this->assertEquals( $fetched_access_token, $access_token );

        // Delete it
        $this->db->delete( 'sm_posting', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->posting = null;
    }
}
