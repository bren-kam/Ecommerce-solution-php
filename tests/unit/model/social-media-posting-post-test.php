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
     * Test Get
     */
    public function testGet() {
        // Set variables
        $status = 1;
        $sm_facebook_page_id = -5;

        // Create
        $sm_posting_post_id = $this->phactory->insert( 'sm_posting_posts', compact( 'status', 'sm_facebook_page_id' ), 'ii' );

        // Get
        $this->post->get( $sm_posting_post_id, $sm_facebook_page_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $status, $this->post->status );

        // Clean up
        $this->phactory->delete( 'sm_posting_posts', compact( 'sm_facebook_page_id' ), 'i' );
    }
    
    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $post = 'random content';
        
        // Create
        $this->post->post = $post;
        $this->post->create();

        // Make sure it's in the database
        $retrieved_post = $this->phactory->get_var( 'SELECT `post` FROM `sm_posting_posts` WHERE `sm_posting_post_id` = ' . (int) $this->post->id );

        $this->assertEquals( $post, $retrieved_post );

        // Delete
        $this->phactory->delete( 'sm_posting_posts', array( 'sm_posting_post_id' => $this->post->id ), 'i' );
    }

    /**
     * Test Get all unposted posts (the ones needing to besent to facebook)
     */
    public function testGetUnpostedPosts() {
        // Declare variables
        $sm_facebook_page_id = -6;

        // Create the post
        $sm_posting_post_id = $this->phactory->insert( 'sm_posting_posts', array(
            'sm_facebook_page_id' => $sm_facebook_page_id
            , 'access_post' => 'AAAD0VdBxxPMBAJGsPbZASGsqinooBUU2PVosdm1JQZBi9OOWTdiJPwidaSaODEIUwnmzZBKkqiWBuSiCRcV7oTw6OYZAHrLR4l5jUOzs2wZDZD'
            , 'post' => 'test post'
            , 'status' => 0
            , 'date_posted' => '2010-10-09 00:00:00'
            , 'date_created' => '2010-10-10 00:00:00'
        ), 'ississ' );

        // Now get them
        $test_posts = $this->post->get_unposted_posts();

        $this->assertTrue( current( $test_posts ) instanceof SocialMediaPostingPost );

        // Delete post
        $this->phactory->delete( 'sm_posting_posts', compact( 'sm_posting_post_id' ), 'i' );
    }

    /**
     * Test marking errors
     */
    public function testMarkErrors() {
        // Declare variables
        $error_message = 'This is a test error';
        $status = -1;

        // Create the post
        $sm_posting_post_id = $this->phactory->insert( 'sm_posting_posts', compact( 'status' ), 'i' );

        // Mark posts
        $errors[$sm_posting_post_id] = $error_message;
        $this->post->mark_errors( $errors );

        // Test to make sure that's the error
        $message = $this->phactory->get_var( "SELECT `error` FROM `sm_posting_posts` WHERE `sm_posting_post_id` = $sm_posting_post_id" );

        // Make sure they are the same
        $this->assertEquals( $error_message, $message );

        // Delete post
        $this->phactory->delete( 'sm_posting_posts', compact( 'sm_posting_post_id' ), 'i' );
    }

    /**
     * Test updating a product
     */
    public function testSave() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $status = 0;
        $new_status = 1;

        // Create posting post
        $this->post->id = $this->phactory->insert( 'sm_posting_posts', compact( 'status' ), 'i' );

        // Update test
        $this->post->status = $new_status;
        $this->post->save();

        $retrieved_status = $this->phactory->get_var( 'SELECT `status` FROM `sm_posting_posts` WHERE `sm_posting_post_id` = ' . (int) $this->post->id );

        $this->assertEquals( $retrieved_status, $new_status );

        // Delete
        $this->phactory->delete( 'sm_posting_posts', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Declare variables
        $post = 'random content';
        $sm_facebook_page_id = -5;

        // Create
        $this->post->post = $post;
        $this->post->sm_facebook_page_id = $sm_facebook_page_id;
        $this->post->create();

        // Remove/Delete
        $this->post->remove();

        $retrieved_post = $this->phactory->get_var( 'SELECT `post` FROM `sm_posting_posts` WHERE `sm_posting_post_id` = ' . (int) $this->post->id );

        $this->assertFalse( $retrieved_post );
    }

    /**
     * List All
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`post`', '`status`', '`date_posted`' );

        $posts = $this->post->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $posts ) instanceof SocialMediaPostingPost );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`post`', '`status`', '`date_posted`' );

        $count = $this->post->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->post = null;
    }
}
