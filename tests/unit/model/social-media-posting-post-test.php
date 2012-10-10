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
     * Test method
     */
    public function testMethod() {
        // Do stuff
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->post = null;
    }
}
