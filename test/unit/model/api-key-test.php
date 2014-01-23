<?php

require_once 'test/base-database-test.php';

class ApiKeyTest extends BaseDatabaseTest {
    /**
     * @var ApiKey
     */
    private $api_key;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->api_key = new ApiKey();
    }

    /**
     * Test Getting all attributes
     */
    public function testGetByKey() {
        // Declare variables
        $key = md5('googoo dolls');

        // Insert a key
        $this->phactory->insert( 'api_keys', array( 'key' => $key, 'status' => 1 ), 's' );

        // Get the API Key
        $this->api_key->get_by_key( $key );

        // Should have found it
        $this->assertEquals( $this->api_key->key, $key );

        // Now delete it
        $this->phactory->delete( 'api_keys', array( 'key' => $key ), 's' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->api_key = null;
    }
}
