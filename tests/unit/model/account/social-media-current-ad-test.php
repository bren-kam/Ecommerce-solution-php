<?php

require_once 'base-database-test.php';

class SocialMediaCurrentAdTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaCurrentAd
     */
    private $sm_current_ad;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_current_ad = new SocialMediaCurrentAd();
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
        $this->sm_current_ad = null;
    }
}
