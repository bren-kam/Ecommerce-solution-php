<?php

require_once 'test/base-database-test.php';

class ApiKeyTest extends BaseDatabaseTest {
    const COMPANY_ID = 3;
    const KEY = '39ae2599688ecf10fdd9bd036ed7e73d';
    const STATUS = 1;

    /**
     * @var ApiKey
     */
    private $api_key;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->api_key = new ApiKey();

        // Define
        $this->phactory->define( 'api_keys', array( 'company_id' => self::COMPANY_ID, 'key' => self::KEY, 'status' => ApiKey::STATUS_ACTIVE ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting all attributes
     */
    public function testGetByKey() {
        // Create
        $this->phactory->create('api_keys');

        // Get the API Key
        $this->api_key->get_by_key( self::KEY );

        // Should have found it
        $this->assertEquals( self::COMPANY_ID, $this->api_key->company_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->api_key = null;
    }
}
