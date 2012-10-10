<?php

require_once 'base-database-test.php';

class MobileListTest extends BaseDatabaseTest {
    /**
     * @var MobileList
     */
    private $mobile_list;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_list = new MobileList();
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
        $this->mobile_list = null;
    }
}
