<?php

require_once 'base-database-test.php';

class OrderTest extends BaseDatabaseTest {
    /**
     * @var Order
     */
    private $order;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->order = new Order();
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->order->user_id = -5;
        $this->order->total_amount = 449;
        $this->order->total_monthly = 449;
        $this->order->type = 'GSR Website';
        $this->order->status = 1;
        $this->order->create();

        $this->assertTrue( !is_null( $this->order->id ) );

        // Make sure it's in the database
        $type = $this->db->get_var( 'SELECT `type` FROM `orders` WHERE `order_id` = ' . (int) $this->order->id );

        $this->assertEquals( $this->order->type, $type );

        // Delete the attribute
        $this->db->delete( 'orders', array( 'order_id' => $this->order->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->order = null;
    }
}
