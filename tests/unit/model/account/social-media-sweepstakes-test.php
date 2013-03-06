<?php

require_once 'base-database-test.php';

class SocialMediaSweepstakesTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaSweepstakes
     */
    private $sm_sweepstakes;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_sweepstakes = new SocialMediaSweepstakes();
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
        $this->sm_sweepstakes = null;
    }
}
