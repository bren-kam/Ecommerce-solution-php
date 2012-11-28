<?php

require_once 'base-database-test.php';

class ApiKeyTest extends BaseDatabaseTest {
    /**
     * @var ApiKey
     */
    private $api_key;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->api_key = new ApiKey();
    }

    /**
     * Test Getting all attributes
     */
    public function testGetByKey() {
        // Declare variables
        $key = md5('googoo dolls');

        // Insert a key
        $this->db->insert( 'api_keys', array( 'key' => $key, 'status' => 1 ), 's' );

        // Get the API Key
        $this->api_key->get_by_key( $key );

        // Should have found it
        $this->assertEquals( $this->api_key->key, $key );

        // Now delete it
        $this->db->delete( 'api_keys', array( 'key' => $key ), 's' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->api_key = null;
    }
}
