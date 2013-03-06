<?php

require_once 'base-database-test.php';

class CraigslistTagTest extends BaseDatabaseTest {
    /**
     * @var CraigslistTag
     */
    private $craigslist_tag;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->craigslist_tag = new CraigslistTag();
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
        $this->craigslist_tag = null;
    }
}
