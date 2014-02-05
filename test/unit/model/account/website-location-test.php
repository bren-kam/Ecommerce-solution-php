<?php

require_once 'test/base-database-test.php';

class WebsiteLocationTest extends BaseDatabaseTest {
    const NAME = 'Brooklyn Location';

    /**
     * @var WebsiteLocation
     */
    private $website_location;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_location = new WebsiteLocation();

        // Define
        $this->phactory->define( 'website_location', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_website_location = $this->phactory->create('website_location');

        // Get
        $this->website_location->get( $ph_website_location->id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->website_location->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_location = null;
    }
}
