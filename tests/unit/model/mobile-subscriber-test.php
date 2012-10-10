<?php

require_once 'base-database-test.php';

class MobileSubscriberTest extends BaseDatabaseTest {
    /**
     * @var MobileSubscriber
     */
    private $mobile_subscriber;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_subscriber = new MobileSubscriber();
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
        $this->mobile_subscriber = null;
    }
}
