<?php
require_once 'test/base-database-test.php';

class ServerTest extends BaseDatabaseTest {
    const NAME = 'Jeebz Server 1';
    const IP = '123.456.789.012';

    /**
     * @var Server
     */
    private $server;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->server = new Server();

        // Define
        $this->phactory->define( 'server', array( 'name' => self::NAME, 'ip' => self::IP ) );
        $this->phactory->recall();
    }

    /**
     * Test getting the server
     */
    public function testGet() {
        // Create
        $ph_server = $this->phactory->create('server');

        // Get
        $this->server->get( $ph_server->id );

        // Assert
        $this->assertEquals( self::NAME, $this->server->name );
    }

    /**
     * Test getting the companies as a class
     */
    public function testGetAll() {
        // Create
        $ph_server = $this->phactory->create('server');

        // Get
        $servers = $this->server->get_all();
        $server = current( $servers );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Server', $servers );
        $this->assertEquals( self::NAME, $server->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->server = null;
    }
}