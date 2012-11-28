<?php

require_once 'base-database-test.php';

class OrderItemTest extends BaseDatabaseTest {
    /**
     * @var OrderItem
     */
    private $order_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->order_item = new OrderItem();
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->order_item->order_id = -3;
        $this->order_item->item = 'iKidzRooms';
        $this->order_item->quantity = 1;
        $this->order_item->amount = 199;
        $this->order_item->monthly = 199;
        $this->order_item->create();

        $this->assertTrue( !is_null( $this->order_item->id ) );

        // Make sure it's in the database
        $item = $this->db->get_var( 'SELECT `item` FROM `order_items` WHERE `order_item_id` = ' . (int) $this->order_item->id );

        $this->assertEquals( $this->order_item->item, $item );

        // Delete the attribute
        $this->db->delete( 'order_items', array( 'order_item_id' => $this->order_item->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->order_item = null;
    }
}
