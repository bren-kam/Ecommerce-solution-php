<?php

require_once 'base-database-test.php';

class MobileMarketingTest extends BaseDatabaseTest {
    /**
     * @var MobileMarketing
     */
    private $mobile_marketing;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_marketing = new MobileMarketing();
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
        $this->mobile_marketing = null;
    }
}
