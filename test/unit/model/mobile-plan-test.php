<?php

require_once 'test/base-database-test.php';

class MobilePlanTest extends BaseDatabaseTest {
    /**
     * @var MobilePlan
     */
    private $mobile_plan;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_plan = new MobilePlan();
    }

    /**
     * Test get
     */
    public function testGet() {
        // Declare variables
        $mobile_plan_id = 1;

        $this->mobile_plan->get( $mobile_plan_id );

        $this->assertEquals( $this->mobile_plan->name, 'Level 1' );
    }

    /**
     * Test get all
     */
    public function testGetAll() {
        $mobile_plans = $this->mobile_plan->get_all();

        $this->assertTrue( current( $mobile_plans ) instanceof MobilePlan );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_plan = null;
    }
}
