<?php

require_once 'base-database-test.php';

class IndustryTest extends BaseDatabaseTest {
    /**
     * @var Industry
     */
    private $industry;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->industry = new Industry();
    }

    /**
     * Test Get
     */
    public function testGet() {
        $industry_id = 1;

        $this->industry->get( $industry_id );

        $this->assertEquals( $this->industry->name, 'furniture' );
    }

    /**
     * Test getting all the industries
     */
    public function testGetAll() {
        $industries = $this->industry->get_all();

        $this->assertTrue( $industries[0] instanceof Industry );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->industry = null;
    }
}
