<?php

require_once 'test/base-database-test.php';

class WebsiteReachCommentTest extends BaseDatabaseTest {
    const COMMENT = 'I like this product';
    /**
     * @var WebsiteReachComment
     */
    private $website_reach_comment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->website_reach_comment = new WebsiteReachComment();

        // Define
        $this->phactory->define( 'website_reach_comments', array( 'comment' => self::COMMENT ) );
        $this->phactory->define( 'website_reaches', array( 'website_id' => self::WEBSITE_ID ) );
        $this->phactory->recall();
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->website_reach_comment->comment = self::COMMENT;
        $this->website_reach_comment->create();

        // Assert
        $this->assertNotNull( $this->website_reach_comment->id );

        // Get
        $ph_website_reach_comment = $this->phactory->get( 'website_reach_comments', array( 'website_reach_comment_id' => $this->website_reach_comment->id ) );

        // Assert
        $this->assertEquals( self::COMMENT, $ph_website_reach_comment->comment );
    }

    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_reach = $this->phactory->create('website_reaches');
        $ph_website_reach_comment = $this->phactory->create( 'website_reach_comments', array( 'website_reach_id' => $ph_website_reach->website_reach_id ) );

        // Get
        $this->website_reach_comment->get( $ph_website_reach_comment->website_reach_comment_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( $ph_website_reach_comment->website_reach_comment_id, $this->website_reach_comment->id );
    }

    /**
     * Get By Reach
     */
    public function testGetByReach() {
        // Create
        $ph_website_reach = $this->phactory->create('website_reaches');
        $this->phactory->create( 'website_reach_comments', array( 'website_reach_id' => $ph_website_reach->website_reach_id ) );

        // Get
        $website_reach_comments = $this->website_reach_comment->get_by_reach( $ph_website_reach->website_reach_id, self::WEBSITE_ID );
        $website_reach_comment = current( $website_reach_comments );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteReachComment', $website_reach_comments );
        $this->assertEquals( self::COMMENT, $website_reach_comment->comment );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $ph_website_reach_comment = $this->phactory->create('website_reach_comments');

        // Remove
        $this->website_reach_comment->id = $ph_website_reach_comment->website_reach_comment_id;
        $this->website_reach_comment->remove();

        // Get
        $ph_website_reach_comment = $this->phactory->get( 'website_reach_comments', array( 'website_reach_comment_id' => $ph_website_reach_comment->website_reach_comment_id ) );

        // Assert
        $this->assertNull( $ph_website_reach_comment );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->website_reach_comment = null;
    }
}
