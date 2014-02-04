<?php

require_once 'test/base-database-test.php';

class WebsiteReachCommentTest extends BaseDatabaseTest {
    /**
     * @var WebsiteReachComment
     */
    private $website_reach_comment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->website_reach_comment = new WebsiteReachComment();
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Test create
//     */
//    public function testCreate() {
//        // Declare variables
//        $website_reach_id = -9;
//        $comment = 'I like this product';
//
//        // Create
//        $this->website_reach_comment->website_reach_id = $website_reach_id;
//        $this->website_reach_comment->comment = $comment;
//        $this->website_reach_comment->create();
//
//        // Make sure it's in the database
//        $retrieved_comment = $this->phactory->get_var( "SELECT `comment` FROM `website_reach_comments` WHERE `website_reach_id` = $website_reach_id" );
//
//        $this->assertEquals( $comment, $retrieved_comment );
//
//        // Delete
//        $this->phactory->delete( 'website_reach_comments', compact( 'website_reach_id' ), 'i' );
//    }
//
//    /**
//     * Get
//     */
//    public function testGet() {
//        // Set variables
//        $website_id = -7;
//
//        // Create
//        $website_reach_id = $this->phactory->insert( 'website_reaches', compact( 'website_id' ), 'i' );
//        $website_reach_comment_id = $this->phactory->insert( 'website_reach_comments', compact( 'website_reach_id' ), 'i' );
//
//        // Get
//        $this->website_reach_comment->get( $website_reach_comment_id, $website_id );
//
//        // Make sure we grabbed the right one
//        $this->assertEquals( $website_reach_comment_id, $this->website_reach_comment->id );
//
//        // Clean up
//        $this->phactory->delete( 'website_reach_comments', compact( 'website_reach_id' ), 'i' );
//        $this->phactory->delete( 'website_reaches', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Get By Reach
//     */
//    public function testGetByReach() {
//        // Declare Variables
//        $website_id = -5;
//        $user_id = 1; // Kerry Jones
//
//        // Create
//        $website_reach_id = $this->phactory->insert( 'website_reaches', compact( 'website_id' ), 'i' );
//        $this->phactory->insert( 'website_reach_comments', compact( 'website_reach_id', 'user_id' ), 'ii' );
//
//        // Get all
//        $website_reach_comments = $this->website_reach_comment->get_by_reach( $website_reach_id, $website_id );
//
//        $this->assertTrue( current( $website_reach_comments ) instanceof WebsiteReachComment );
//
//        // Clean up
//        $this->phactory->delete( 'website_reaches', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'website_reach_comments', compact( 'website_reach_id' ), 'i' );
//    }
//
//    /**
//     * Remove
//     *
//     * @depends testCreate
//     */
//    public function testRemove() {
//        // Declare variables
//        $website_reach_id = -9;
//        $comment = 'I like this product';
//
//        // Create
//        $this->website_reach_comment->website_reach_id = $website_reach_id;
//        $this->website_reach_comment->comment = $comment;
//        $this->website_reach_comment->create();
//
//        // Remove/Delete
//        $this->website_reach_comment->remove();
//
//        $retrieved_comment = $this->phactory->get_var( 'SELECT `comment` FROM `website_reach_comments` WHERE `website_reach_comment_id` = ' . (int) $this->website_reach_comment->id );
//
//        $this->assertFalse( $retrieved_comment );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->website_reach_comment = null;
    }
}
