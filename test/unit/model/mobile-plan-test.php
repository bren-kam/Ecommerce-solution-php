<?php

require_once 'test/base-database-test.php';

class MobilePlanTest extends BaseDatabaseTest {
    const NAME = 'Level 1';

    /**
     * @var MobilePlan
     */
    private $mobile_plan;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_plan = new MobilePlan();

        // Define
        $this->phactory->define( 'mobile_plans', array( 'name' => self::NAME ) );
        $this->phactory->recall();
    }

    /**
     * Test get
     */
    public function testGet() {
        // Create
        $ph_mobile_plan = $this->phactory->create('mobile_plans');

        // Get
        $this->mobile_plan->get( $ph_mobile_plan->mobile_plan_id );

        // Assert
        $this->assertEquals( self::NAME, $this->mobile_plan->name );
    }

    /**
     * Test get all
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('mobile_plans');

        // Get
        $mobile_plans = $this->mobile_plan->get_all();
        $mobile_plan = current( $mobile_plans );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'MobilePlan', $mobile_plans );
        $this->assertEquals( self::NAME, $mobile_plan->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_plan = null;
    }
}
