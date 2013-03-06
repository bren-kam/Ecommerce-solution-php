<?php

require_once 'base-database-test.php';

class AnalyticsEmailTest extends BaseDatabaseTest {
    /**
     * @var AnalyticsEmail
     */
    private $analytics_email;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->analytics_email = new AnalyticsEmail();
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
        $this->analytics_email = null;
    }
}
