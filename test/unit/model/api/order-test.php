<?php

require_once 'test/base-database-test.php';

class OrderTest extends BaseDatabaseTest {
    const TOTAL_AMOUNT = 499;
    const TYPE = 'GSR Website';

    /**
     * @var Order
     */
    private $order;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->order = new Order();

        // Define
        $this->phactory->define( 'orders', array( 'total_amount' => self::TOTAL_AMOUNT, 'type' => self::TYPE ) );
        $this->phactory->recall();
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->order->type = self::TYPE;
        $this->order->create();

        // Assert
        $this->assertNotNull( $this->order->id );

        // Get
        $ph_order = $this->phactory->get( 'orders', array( 'order_id' => $this->order->id ) );

        // Assert
        $this->assertEquals( self::TYPE, $ph_order->type );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->order = null;
    }
}
