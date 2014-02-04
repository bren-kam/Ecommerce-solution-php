<?php

require_once 'test/base-database-test.php';

class SocialMediaPostingPostTest extends BaseDatabaseTest {
    const SM_FACEBOOK_PAGE_ID = 9;
    const POST = 'LIKE us and receive 10% off!';
    const DATE_POSTED = '2014-01-01 00:00:00';

    // SM Facebook Page
    const FACEBOOK_PAGE_NAME = 'Hurdee Facebook Furniture';

    /**
     * @var SocialMediaPostingPost
     */
    private $post;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->post = new SocialMediaPostingPost();

        // Define
        $this->phactory->define( 'sm_posting_posts', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID, 'post' => self::POST, 'status' => SocialMediaPostingPost::STATUS_UNPOSTED, 'date_posted' => self::DATE_POSTED ) );
        $this->phactory->define( 'sm_facebook_page', array( 'name' => self::FACEBOOK_PAGE_NAME, 'status' => SocialMediaFacebookPage::STATUS_ACTIVE ) );
        $this->phactory->define( 'sm_posting', array( 'sm_facebook_page_id' => self::SM_FACEBOOK_PAGE_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_post = $this->phactory->create( 'sm_posting_posts', array( 'status' => SocialMediaPostingPost::STATUS_POSTED ) );

        // Get
        $this->post->get( $ph_post->sm_posting_post_id, self::SM_FACEBOOK_PAGE_ID );

        // Assert
        $this->assertEquals( SocialMediaPostingPost::STATUS_POSTED, $this->post->status );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->post->post = self::POST;
        $this->post->create();

        // Assert
        $this->assertNotNull( $this->post->id );

        // get
        $ph_post = $this->phactory->get( 'sm_posting_posts', array( 'sm_posting_post_id' => $this->post->id ) );

        // Assert
        $this->assertEquals( self::POST, $ph_post->post );
    }

    /**
     * Test Get all unposted posts (the ones needing to besent to facebook)
     */
    public function testGetUnpostedPosts() {
        // Create
        $ph_fb_page = $this->phactory->create('sm_facebook_page');
        $this->phactory->create( 'sm_posting', array( 'sm_facebook_page_id' => $ph_fb_page->id ) );
        $this->phactory->create( 'sm_posting_posts', array( 'sm_facebook_page_id' => $ph_fb_page->id ) );

        // Now get them
        $posts = $this->post->get_unposted_posts();
        $post = current( $posts );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'SocialMediaPostingPost', $posts );
        $this->assertEquals( self::POST, $post->post );
    }

    /**
     * Test marking errors
     */
    public function testMarkErrors() {
        // Declare
        $error_message = 'This is a test error';

        // Create
        $ph_post = $this->phactory->create('sm_posting_posts');

        // Mark posts
        $errors = array( $ph_post->sm_posting_post_id => $error_message );
        $this->post->mark_errors( $errors );

        // Get
        $ph_post = $this->phactory->get( 'sm_posting_posts', array( 'sm_posting_post_id' => $ph_post->sm_posting_post_id ) );

        // Assert
        $this->assertEquals( $error_message, $ph_post->error );
    }

    /**
     * Test updating a product
     */
    public function testSave() {
        // Create
        $ph_post = $this->phactory->create('sm_posting_posts');

        // Save
        $this->post->id = $ph_post->sm_posting_post_id;
        $this->post->status = SocialMediaPostingPost::STATUS_POSTED;
        $this->post->save();

        // Get
        $ph_post = $this->phactory->get( 'sm_posting_posts', array( 'sm_posting_post_id' => $ph_post->sm_posting_post_id ) );

        // Assert
        $this->assertEquals( SocialMediaPostingPost::STATUS_POSTED, $ph_post->status );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $ph_post = $this->phactory->create('sm_posting_posts');

        // Remove/Delete
        $this->post->id = $ph_post->sm_posting_post_id;
        $this->post->sm_facebook_page_id = self::SM_FACEBOOK_PAGE_ID;
        $this->post->remove();

        // Get
        $ph_post = $this->phactory->get( 'sm_posting_posts', array( 'sm_posting_post_id' => $ph_post->sm_posting_post_id ) );

        // Assert
        $this->assertNull( $ph_post );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Get stub user
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('sm_posting_posts');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`post`', '`status`', '`date_posted`' );

        // Get
        $posts = $this->post->list_all( $dt->get_variables() );
        $post = current( $posts );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'SocialMediaPostingPost', $posts );
        $this->assertEquals( self::POST, $post->post );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Get stub user
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('sm_posting_posts');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`post`', '`status`', '`date_posted`' );

        // Get
        $count = $this->post->count_all( $dt->get_count_variables() );

        // Assert
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
