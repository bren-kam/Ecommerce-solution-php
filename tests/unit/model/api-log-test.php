<?php

require_once 'base-database-test.php';

class ApiLogTest extends BaseDatabaseTest {
    /**
     * @var ApiLog
     */
    private $api_log;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->api_log = new ApiLog();
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->api_log->company_id = -3;
        $this->api_log->type = 'API';
        $this->api_log->method = 'Create Account';
        $this->api_log->message = 'Hedgehogs rock';
        $this->api_log->success = 1;
        $this->api_log->create();

        $this->assertTrue( !is_null( $this->api_log->id ) );

        // Make sure it's in the database
        $method = $this->phactory->get_var( 'SELECT `method` FROM `api_log` WHERE `api_log_id` = ' . (int) $this->api_log->id );

        $this->assertEquals( $this->api_log->method, $method );

        // Delete the attribute
        $this->phactory->delete( 'api_log', array( 'api_log_id' => $this->api_log->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->api_log = null;
    }
}
