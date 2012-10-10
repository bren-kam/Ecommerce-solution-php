<?php

require_once 'base-database-test.php';

class CraigslistTest extends BaseDatabaseTest {
    /**
     * @var Craigslist
     */
    private $craigslist;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->craigslist = new Craigslist();
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
        $this->craigslist = null;
    }
}
