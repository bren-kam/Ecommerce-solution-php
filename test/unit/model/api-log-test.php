<?php

require_once 'test/base-database-test.php';

class ApiLogTest extends BaseDatabaseTest {
    const COMPANY_ID = 3;
    const TYPE = 'API';
    /**
     * @var ApiLog
     */
    private $api_log;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->api_log = new ApiLog();

        // Define
        $this->phactory->define( 'api_log', array( 'company_id' => self::COMPANY_ID, 'type' => self::TYPE ) );
        $this->phactory->recall();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->api_log->company_id = self::COMPANY_ID;
        $this->api_log->type = self::TYPE;
        $this->api_log->create();

        $this->assertNotNull( $this->api_log->id );

        // Make sure it's in the database
        $ph_api_log = $this->phactory->get( 'api_log', array( 'api_log_id' => (int) $this->api_log->id ) );

        $this->assertEquals( self::TYPE, $ph_api_log->type );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->api_log = null;
    }
}
