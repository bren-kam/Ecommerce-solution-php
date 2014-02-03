<?php

require_once 'test/base-database-test.php';

class IndustryTest extends BaseDatabaseTest {
    const NAME = 'furniture';

    /**
     * @var Industry
     */
    private $industry;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->industry = new Industry();

        // Define
        $this->phactory->define( 'industries', array( 'name' => self::NAME ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_industry = $this->phactory->create('industries');

        // Get
        $this->industry->get( $ph_industry->industry_id );

        // Assert
        $this->assertEquals( self::NAME, $this->industry->name );
    }

    /**
     * Test getting all the industries
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('industries');

        // Get
        $industries = $this->industry->get_all();
        $industry = current( $industries );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Industry', $industries );
        $this->assertEquals( self::NAME, $industry->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->industry = null;
    }
}
