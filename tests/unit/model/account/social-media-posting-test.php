<?php

require_once 'base-database-test.php';

class SocialMediaPostingTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaPosting
     */
    private $sm_posting;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_posting = new SocialMediaPosting();
    }

    /**
     * Test
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_posting = null;
    }
}
