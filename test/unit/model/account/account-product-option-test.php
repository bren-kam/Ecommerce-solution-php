<?php

require_once 'test/base-database-test.php';

class AccountProductOptionTest extends BaseDatabaseTest {
    const PRODUCT_ID = 3;
    const PRODUCT_OPTION_ID = 7;
    const PRICE = 50;

    // Product Options
    const OPTION_TYPE = 'checkbox';

    // Product Option List Item
    const VALUE = 'Extra Help';

    // Website Product Option List Item
    const WEBSITE_PRODUCT_OPTION_LIST_ITEM_PRICE = 20;

    /**
     * @var AccountProductOption
     */
    private $account_product_option;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->account_product_option = new AccountProductOption();

        // Define
        $this->phactory->define( 'website_product_options', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID, 'product_option_id' => self::PRODUCT_OPTION_ID, 'price' => self::PRICE ) );
        $this->phactory->define( 'product_options', array( 'option_type' => self::OPTION_TYPE ) );
        $this->phactory->define( 'product_option_list_items', array( 'product_option_id' => self::PRODUCT_OPTION_ID, 'value' => self::VALUE ) );
        $this->phactory->define( 'website_product_option_list_items', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID, 'product_option_id' => self::PRODUCT_OPTION_ID, 'price' => self::WEBSITE_PRODUCT_OPTION_LIST_ITEM_PRICE ) );
        $this->phactory->recall();
    }

    /**
     * Get With List Items
     */
    public function testGetWithListItems() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');
        $ph_product_option_list_item = $this->phactory->create('product_option_list_items', array( 'product_option_id' => $ph_product_option->product_option_id ) );
        $this->phactory->create( 'website_product_options', array( 'product_option_id' => $ph_product_option->product_option_id ) );
        $this->phactory->create( 'website_product_option_list_items', array( 'product_option_id' => $ph_product_option->product_option_id, 'product_option_list_item_id' => $ph_product_option_list_item->product_option_list_item_id ) );

        // Get
        $account_product_options = $this->account_product_option->get_with_list_items( self::WEBSITE_ID, self::PRODUCT_ID );
        $account_product_option = current( $account_product_options );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'AccountProductOption', $account_product_options );
        $this->assertEquals( self::VALUE, $account_product_option->value );
    }

    /**
     * Get Without List Items
     */
    public function testGetWithoutListItems() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');
        $this->phactory->create( 'website_product_options', array( 'product_option_id' => $ph_product_option->product_option_id ) );

        // Get
        $account_product_options = $this->account_product_option->get_without_list_items( self::WEBSITE_ID, self::PRODUCT_ID );
        $account_product_option = current( $account_product_options );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'AccountProductOption', $account_product_options );
        $this->assertEquals( self::PRICE, $account_product_option->price );
    }

    /**
     * Get All
     *
     * @depends testGetWithListItems
     * @depends testGetWithoutListItems
     */
    public function testGetAll() {
        // Create
        $ph_product_option = $this->phactory->create('product_options');
        $ph_product_option_list_item = $this->phactory->create('product_option_list_items', array( 'product_option_id' => $ph_product_option->product_option_id ) );
        $this->phactory->create( 'website_product_options', array( 'product_option_id' => $ph_product_option->product_option_id ) );
        $this->phactory->create( 'website_product_option_list_items', array( 'product_option_id' => $ph_product_option->product_option_id, 'product_option_list_item_id' => $ph_product_option_list_item->product_option_list_item_id ) );

        // Get
        $account_product_options = $this->account_product_option->get_all( self::WEBSITE_ID, self::PRODUCT_ID );
        $account_product_option = current( $account_product_options );

        // Assert
        $this->assertEquals( self::PRICE, $account_product_option['price'] );
        $this->assertEquals( self::WEBSITE_PRODUCT_OPTION_LIST_ITEM_PRICE, $account_product_option['list_items'][$ph_product_option_list_item->product_option_list_item_id] );
    }

    /**
     * Add Bulk
     */
    public function testAddBulk() {
        // Declare
        $product_options = array(
            array(
                'product_option_id' => self::PRODUCT_OPTION_ID
                , 'price' => self::PRICE
                , 'required' => 0
            )
        );

        // Insert
        $this->account_product_option->add_bulk( self::WEBSITE_ID, self::PRODUCT_ID, $product_options );

        // Get
        $ph_website_product_option = $this->phactory->get( 'website_product_options', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertEquals( self::PRICE, $ph_website_product_option->price );
    }

    /**
     * Add Bulk List Items
     */
    public function testAddBulkListItems() {
        // Declare
        $product_option_list_items = array(
            array(
                'product_option_id' => self::PRODUCT_OPTION_ID
                , 'product_option_list_item_id' => 2
                , 'price' => self::WEBSITE_PRODUCT_OPTION_LIST_ITEM_PRICE
                , 'alt_price' => 30
                , 'alt_price2' => 40
            )
        );

        // Insert
        $this->account_product_option->add_bulk_list_items( self::WEBSITE_ID, self::PRODUCT_ID, $product_option_list_items );

        // Get
        $ph_website_product_option = $this->phactory->get( 'website_product_option_list_items', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertEquals( self::WEBSITE_PRODUCT_OPTION_LIST_ITEM_PRICE, $ph_website_product_option->price );
    }

    /**
     * Delete Website Product Options
     */
    public function testDeleteWebsiteProductOptions() {
        // Access refactory
        $class = new ReflectionClass('AccountProductOption');
        $method = $class->getMethod('delete_website_product_options');
        $method->setAccessible(true);

        // Create
        $this->phactory->create('website_product_options');

        // Delete them
        $method->invokeArgs( $this->account_product_option, array( self::WEBSITE_ID, self::PRODUCT_ID ) );

        // Get
        $ph_website_product_option = $this->phactory->get( 'website_product_option_list_items', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertNull( $ph_website_product_option );
    }

    /**
     * Delete Website Product Option List Items
     */
    public function testDeleteWebsiteProductOptionListItems() {
        // Access refactory
        $class = new ReflectionClass('AccountProductOption');
        $method = $class->getMethod('delete_website_product_option_list_items');
        $method->setAccessible(true);

        // Create
        $this->phactory->create('website_product_option_list_items');

        // Delete them
        $method->invokeArgs( $this->account_product_option, array( self::WEBSITE_ID, self::PRODUCT_ID ) );

        // Get
        $ph_website_product_option_list_item = $this->phactory->get( 'website_product_option_list_items', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertNull( $ph_website_product_option_list_item );
    }

    /**
     * Delete By Product
     *
     * @depends testDeleteWebsiteProductOptions
     * @depends testDeleteWebsiteProductOptionListItems
     */
    public function testDeleteByProduct() {
        // Create
        $this->phactory->create('website_product_options');
        $this->phactory->create('website_product_option_list_items');

        // Delete
        $this->account_product_option->delete_by_product( self::WEBSITE_ID, self::PRODUCT_ID );

        // Assert
        $ph_website_product_option = $this->phactory->get( 'website_product_option_list_items', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );
        $ph_website_product_option_list_item = $this->phactory->get( 'website_product_option_list_items', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );

        // Assert
        $this->assertNull( $ph_website_product_option );
        $this->assertNull( $ph_website_product_option_list_item );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->account_product_option = null;
    }
}
