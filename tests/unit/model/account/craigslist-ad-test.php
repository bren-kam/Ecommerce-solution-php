<?php

require_once 'base-database-test.php';

class CraigslistAdTest extends BaseDatabaseTest {
    /**
     * @var CraigslistAd
     */
    private $craigslist_ad;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->craigslist_ad = new CraigslistAd();
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
        $this->craigslist_ad = null;
    }
}
