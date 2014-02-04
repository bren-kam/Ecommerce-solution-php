<?php
require_once 'test/base-database-test.php';

class ProductOptionListItemTest extends BaseDatabaseTest {
    const PRODUCT_OPTION_ID = 5;
    const VALUE = 'Brown';

    /**
     * @var ProductOptionListItem
     */
    private $product_option_list_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->product_option_list_item = new ProductOptionListItem();

        // Define
        $this->phactory->define( 'product_option_list_items', array( 'product_option_id' => self::PRODUCT_OPTION_ID, 'value' => self::VALUE ) );
        $this->phactory->recall();
    }


    /**
     * Test Getting an product option list item
     */
    public function testGet() {
        // Create
        $ph_product_option_list_item = $this->phactory->create('product_option_list_items');

        // Get
        $this->product_option_list_item->get( $ph_product_option_list_item->product_option_list_item_id );

        // Assert
        $this->assertEquals( self::VALUE, $this->product_option_list_item->value );
    }

    /**
     * Test Getting all product option list items for a product option
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('product_option_list_items');

        // Get
        $product_option_list_items = $this->product_option_list_item->get_all( self::PRODUCT_OPTION_ID );
        $product_option_list_item = current( $product_option_list_items );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ProductOptionListItem', $product_option_list_items );
        $this->assertEquals( self::VALUE, $product_option_list_item->value );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->product_option_list_item->value = self::VALUE;
        $this->product_option_list_item->create();

        // Assert
        $this->assertNotNull( $this->product_option_list_item->id );

        // Get
        $ph_product_option_list_item = $this->phactory->get( 'product_option_list_items', array( 'product_option_list_item_id' => $this->product_option_list_item->id ) );

        // Assert
        $this->assertEquals( self::VALUE, $ph_product_option_list_item->value );
    }

    /**
     * Test updating the product option list item
     */
    public function testUpdate() {
        // Create
        $ph_product_option_list_item = $this->phactory->create('product_option_list_items');

        // Update test
        $this->product_option_list_item->id = $ph_product_option_list_item->product_option_list_item_id;
        $this->product_option_list_item->value = 'Blue';
        $this->product_option_list_item->save();

        // Get
        $ph_product_option_list_item = $this->phactory->get( 'product_option_list_items', array( 'product_option_list_item_id' => $ph_product_option_list_item->product_option_list_item_id ) );

        // Assert
        $this->assertEquals( $this->product_option_list_item->value, $ph_product_option_list_item->value );
    }

    /**
     * Test Delete
     *
     * @depends testCreate
     * @depends testGet
     */
    public function testDelete() {
        // Create
        $ph_product_option_list_item = $this->phactory->create('product_option_list_items');

        // Delete
        $this->product_option_list_item->id = $ph_product_option_list_item->product_option_list_item_id;
        $this->product_option_list_item->remove();

        // Get
        $ph_product_option_list_item = $this->phactory->get( 'product_option_list_items', array( 'product_option_list_item_id' => $ph_product_option_list_item->product_option_list_item_id ) );

        // Assert
        $this->assertNull( $ph_product_option_list_item );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->product_option_list_item = null;
    }
}
