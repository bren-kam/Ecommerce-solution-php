<?php

require_once 'test/base-database-test.php';

class OrderItemTest extends BaseDatabaseTest {
    const ORDER_ID = 3;
    const ITEM = 'Newson Room';

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

        // Define
        $this->phactory->define( 'order_items', array( 'order_id' => self::ORDER_ID, 'item' => self::ITEM ) );
        $this->phactory->recall();
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->order_item->order_id = self::ORDER_ID;
        $this->order_item->item = self::ITEM;
        $this->order_item->create();

        // Assert
        $this->assertNotNull( $this->order_item->id );

        // Get
        $ph_order_item = $this->phactory->get( 'order_items', array( 'order_item_id' => $this->order_item->id ) );

        // Assert
        $this->assertEquals( self::ITEM, $ph_order_item->item );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->order_item = null;
    }
}
