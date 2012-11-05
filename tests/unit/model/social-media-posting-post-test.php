<?php

require_once 'base-database-test.php';

class SocialMediaPostingPostTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaPostingPost
     */
    private $post;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->post = new SocialMediaPostingPost();
    }

    /**
     * Test Get all unposted posts (the ones needing to besent to facebook)
     */
    public function testGetUnpostedPosts() {
        // Create the post
        $this->db->insert( 'sm_posting_posts', array(
            'sm_facebook_page_id' => 6
            , 'access_token' => 'AAAD0VdBxxPMBAJGsPbZASGsqinooBUU2PVosdm1JQZBi9OOWTdiJPwidaSaODEIUwnmzZBKkqiWBuSiCRcV7oTw6OYZAHrLR4l5jUOzs2wZDZD'
            , 'post' => 'test post'
            , 'status' => 0
            , 'date_posted' => '2010-10-09 00:00:00'
            , 'date_created' => '2010-10-10 00:00:00'
        ), 'ississ' );

        // For deleting
        $post_id = $this->db->get_insert_id();

        // Now get them
        $test_posts = $this->post->get_unposted_posts();

        $this->assertTrue( current( $test_posts ) instanceof SocialMediaPostingPost );

        // Delete post
        $this->db->delete( 'sm_posting_posts', array( 'sm_posting_post_id' => $post_id ), 'i' );
    }

    /**
     * Test marking errors
     */
    public function testMarkErrors() {
        // Declare variables
        $error_message = 'This is a test error';

        // Create the post
        $this->db->insert( 'sm_posting_posts', array(
            'sm_facebook_page_id' => 6
            , 'access_token' => 'AAAD0VdBxxPMBAJGsPbZASGsqinooBUU2PVosdm1JQZBi9OOWTdiJPwidaSaODEIUwnmzZBKkqiWBuSiCRcV7oTw6OYZAHrLR4l5jUOzs2wZDZD'
            , 'post' => 'test post'
            , 'status' => 1
            , 'date_posted' => '2010-10-09 00:00:00'
            , 'date_created' => '2010-10-10 00:00:00'
        ), 'ississ' );

        $post_id = $this->db->get_insert_id();

        $errors[$post_id] = $error_message;

        // Mark posts
        $this->post->mark_errors( $errors );

        // Test to make sure that's the error
        $message = $this->db->get_var( "SELECT `error` FROM `sm_posting_posts` WHERE `sm_posting_post_id` = $post_id" );

        // Make sure they are the same
        $this->assertEquals( $error_message, $message );

        // Delete post
        $this->db->delete( 'sm_posting_posts', array( 'sm_posting_post_id' => $post_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->post = null;
    }
}
