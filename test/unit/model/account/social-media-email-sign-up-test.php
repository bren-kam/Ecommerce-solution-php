<?php

require_once 'test/base-database-test.php';

class SocialMediaEmailSignUpTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaEmailSignUp
     */
    private $sm_email_sign_up;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_email_sign_up = new SocialMediaEmailSignUp();
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Get
//     */
//    public function testGet() {
//        // Declare variables
//        $sm_facebook_page_id = -5;
//        $fb_page_id = -7;
//
//        // Insert
//        $this->phactory->insert( 'sm_email_sign_up', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );
//
//        // Get
//        $this->sm_email_sign_up->get( $sm_facebook_page_id );
//
//        $this->assertEquals( $fb_page_id, $this->sm_email_sign_up->fb_page_id );
//
//        // Clean up
//        $this->phactory->delete( 'sm_email_sign_up', compact( 'sm_facebook_page_id' ), 'i' );
//    }
//
//    /**
//     * Test create
//     */
//    public function testCreate() {
//        // Declare variables
//        $sm_facebook_page_id = -5;
//        $key = 'Poke';
//
//        // Create
//        $this->sm_email_sign_up->sm_facebook_page_id = $sm_facebook_page_id;
//        $this->sm_email_sign_up->key = $key;
//        $this->sm_email_sign_up->create();
//
//        // Get
//        $retrieved_key = $this->phactory->get_var( "SELECT `key` FROM `sm_email_sign_up` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );
//
//        $this->assertEquals( $retrieved_key, $this->sm_email_sign_up->key );
//
//        // Clean up
//        $this->phactory->delete( 'sm_email_sign_up', compact( 'sm_facebook_page_id' ), 'i' );
//    }
//
//    /**
//     * Save
//     *
//     * @depends testCreate
//     */
//    public function testSave() {
//        // Declare variables
//        $sm_facebook_page_id = -5;
//        $tab = 'Fogger';
//
//        // Create
//        $this->sm_email_sign_up->sm_facebook_page_id = $sm_facebook_page_id;
//        $this->sm_email_sign_up->create();
//
//        // Update test
//        $this->sm_email_sign_up->tab = $tab;
//        $this->sm_email_sign_up->save();
//
//        // Now check it!
//        $retrieved_tab = $this->phactory->get_var( "SELECT `tab` FROM `sm_email_sign_up` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );
//
//        $this->assertEquals( $retrieved_tab, $tab );
//
//        // Clean up
//        $this->phactory->delete( 'sm_email_sign_up', compact( 'sm_facebook_page_id' ), 'i' );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_email_sign_up = null;
    }
}
