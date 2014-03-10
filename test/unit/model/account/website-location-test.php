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
     * Test Get by Website
     */
    public function testGetByWebsite() {
        // Create
        $ph_website_location = $this->phactory->create( 'website_location' );
        
        // Get
        $locations = $this->website_location->get_by_website( self::WEBSITE_ID );
        $location = array_pop($locations);
        
        // Assert
        $this->assertEquals( self::NAME, $location->name );
    }

    /**
     * Test Count
     */
    public function testCount() {
        // Create
        $ph_website_location = $this->phactory->create( 'website_location' );
        
        // Get
        $count = $this->website_location->count( self::WEBSITE_ID );
        
        // Assert
        $this->assertEquals( $count, 1 );        
    }
    
    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->website_location->website_id = self::WEBSITE_ID;
        $this->website_location->name = self::NAME;
        $this->website_location->sequence = 0;
        $this->website_location->create();
        
        // Get
        $this->website_location->get( $this->website_location->id, self::WEBSITE_ID );
        
        // Assert
        $this->assertEquals( $this->website_location->name, self::NAME );
    }

    /**
     * Test Save
     */
    public function testSave() {
        // Create
        $ph_website_location = $this->phactory->create( 'website_location' );
        
        // Get
        $this->website_location->get( $ph_website_location->id, self::WEBSITE_ID );

        // Update & Save
        $new_name = "NY Location " . rand(1, 1000);
        $this->website_location->name = $new_name;
        $this->website_location->save();
        
        // Get
        $this->website_location->get( $ph_website_location->id, self::WEBSITE_ID );
        
        // Assert
        $this->assertEquals( $this->website_location->name, $new_name );        
    }
    
    /**
     * Test Remove
     */
    public function testRemove() {
        // Create
        $ph_website_location = $this->phactory->create( 'website_location' );
        
        // Get
        $this->website_location->get( $ph_website_location->id, self::WEBSITE_ID );

        // Remove
        $this->website_location->remove();
        
        // Get
        $locations = $this->website_location->get_by_website( self::WEBSITE_ID );
        $count = count( $locations );
        
        // Assert
        $this->assertEquals( $count , 0 );
    }
    
    /**
     * Test Update Sequence
     */
    public function testUpdateSequence() {
        $expected_sequence = array();
        $sequence = array();
        $sequence_limit = 10;
        
        // Create
        for ( $i=0 ; $i<$sequence_limit ; $i++ ) {
            $ph_website_location = $this->phactory->create( 'website_location' );
            
            $expected_sequence[$ph_website_location->id] = $i;
            $sequence[] = $ph_website_location->id;
        }
        
        // Update Sequence
        $this->website_location->update_sequence( self::WEBSITE_ID , $sequence );
        
        // Get
        $ordered_website_locations = $this->website_location->get_by_website( self::WEBSITE_ID );
        
        // Assert
        foreach ( $ordered_website_locations as $website_location ) {
            $this->assertEquals( $expected_sequence[$website_location->id] , $website_location->sequence );
        }
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_location = null;
    }
}
