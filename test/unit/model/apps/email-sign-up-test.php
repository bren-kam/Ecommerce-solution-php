<?php

require_once 'test/base-database-test.php';

class EmailSignUpTest extends BaseDatabaseTest {
    /**
     * @var EmailSignUp
     */
    private $email_sign_up;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->email_sign_up = new EmailSignUp();
    }

    /**
     * Test Getting Tab
     */
    public function testGetTab() {
        // Declare variables
        $fb_page_id = -5;
        $content = 'Hip, Hip, Hurray!';

        // Insert About Us
        $this->phactory->insert( 'sm_email_sign_up', array( 'fb_page_id' => $fb_page_id, 'tab' => $content ), 'is' );

        // Get it
        $tab = $this->email_sign_up->get_tab( $fb_page_id );

        $this->assertEquals( $tab, $content );

        // Delete it
        $this->phactory->delete( 'sm_email_sign_up', array( 'fb_page_id' => $fb_page_id ), 'i' );
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
        $this->phactory->insert( 'websites', array( 'website_id' => $account_id, 'title' => 'Banagrams' ), 'is' );
        $this->phactory->insert( 'sm_facebook_page', array( 'id' => $sm_facebook_page_id, 'website_id' => $account_id ), 'iii' );
        $this->phactory->insert( 'sm_email_sign_up', array( 'sm_facebook_page_id' => $sm_facebook_page_id, 'fb_page_id' => $fb_page_id, 'key' => $key ), 'iis' );

        $account = $this->email_sign_up->get_connected_website( $fb_page_id );

        $this->assertEquals( $account->key, $key );

        // Delete it
        $this->phactory->delete( 'websites', array( 'website_id' => $account_id ), 'i' );
        $this->phactory->delete( 'sm_facebook_page', array( 'website_id' => $account_id ), 'i' );
        $this->phactory->delete( 'sm_email_sign_up', array( 'fb_page_id' => $fb_page_id ), 'i' );
    }

    /**
     * Test Connect
     */
    public function testConnect() {
        // Declare variables
        $fb_page_id = -5;
        $key = 'Red Baron';

        // Insert About Us
        $this->phactory->insert( 'sm_email_sign_up', array( 'key' => $key,  ), 's' );

        // Get it
        $this->email_sign_up->connect( $fb_page_id, $key );

        // Get the key
        $fetched_fb_page_id = $this->phactory->get_var( "SELECT `fb_page_id` FROM `sm_email_sign_up` WHERE `key` = '$key'" );

        $this->assertEquals( $fb_page_id, $fetched_fb_page_id );

        // Delete it
        $this->phactory->delete( 'sm_email_sign_up', array( 'key' => $key ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->email_sign_up = null;
    }
}
