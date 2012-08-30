<?php
require_once 'base-database-test.php';

class ProductOptionListItemTest extends BaseDatabaseTest {
    /**
     * @var ProductOptionListItem
     */
    private $product_option_list_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->product_option_list_item = new ProductOptionListItem();
    }

    /**
     * Test Getting an product option list item
     */
    public function testGet() {
        // Black Attribute Item
        $product_option_list_item_id = 1;
        $this->product_option_list_item->get($product_option_list_item_id);

        $this->assertEquals( $this->product_option_list_item->value, 'Brown' );
    }

    /**
     * Test Getting all product option list items for a product option
     */
    public function testGetAll() {
        $product_option_id = 1;
        $product_option_list_items = $this->product_option_list_item->get_all( $product_option_id );

        $this->assertTrue( current( $product_option_list_items ) instanceof ProductOptionListItem );
        $this->assertEquals( count( $product_option_list_items ), 17 );
    }

    /**
     * Test create
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->product_option_list_item->product_option_id = 0;
        $this->product_option_list_item->value = 'Testee';
        $this->product_option_list_item->sequence = 0;
        $this->product_option_list_item->create();

        $this->assertTrue( !is_null( $this->product_option_list_item->id ) );

        // Make sure it's in the database
        $this->product_option_list_item->get( $this->product_option_list_item->id );

        $this->assertEquals( 'Testee', $this->product_option_list_item->value );

        // Delete the product option list item
        $this->db->delete( 'product_option_list_items', array( 'product_option_list_item_id' => $this->product_option_list_item->id ), 'i' );
    }

    /**
     * Test updating the product option list item
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $this->product_option_list_item->product_option_id = 0;
        $this->product_option_list_item->value = 'Testee';
        $this->product_option_list_item->sequence = 0;
        $this->product_option_list_item->create();

        // Update test
        $this->product_option_list_item->value = 'eetseT';
        $this->product_option_list_item->update();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->product_option_list_item->id ) );

        // Now check it!
        $this->product_option_list_item->get( $this->product_option_list_item->id );

        $this->assertEquals( 'eetseT', $this->product_option_list_item->value );

        // Delete the product option list item
        $this->db->delete( 'product_option_list_items', array( 'product_option_list_item_id' => $this->product_option_list_item->id ), 'i' );
    }

    /**
     * Test Delete
     *
     * @depends testCreate
     * @depends testGet
     */
    public function testDelete() {
        // Create test
        $this->product_option_list_item->product_option_id = 0;
        $this->product_option_list_item->value = 'Testee';
        $this->product_option_list_item->sequence = 0;
        $this->product_option_list_item->create();

        // Get it
        $this->product_option_list_item->get( $this->product_option_list_item->id );

        // Delete
        $this->product_option_list_item->delete();

        // Make sure it doesn't exist
        $name = $this->db->get_var( "SELECT `name` FROM `product_option_list_items` WHERE `product_option_list_item_id` = " . (int) $this->product_option_list_item->id );

        $this->assertFalse( $name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->product_option_list_item = null;
    }
}
