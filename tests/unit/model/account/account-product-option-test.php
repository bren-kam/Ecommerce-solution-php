<?php

require_once 'base-database-test.php';

class AccountProductOptionTest extends BaseDatabaseTest {
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
    }

    /**
     * Get With List Items
     */
    public function testGetWithListItems() {
        // Declare variables
        $website_id = -5;
        $product_id = -3;
        $option_type = 'checkbox';
        $value = 'Extra Help';

        // Create everything
        $product_option_id = $this->db->insert( 'product_options', compact( 'option_type' ), 's' );
        $product_option_list_item_id = $this->db->insert( 'product_option_list_items', compact( 'product_option_id', 'value' ), 'is' );
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id' ), 'iii' );
        $this->db->insert( 'website_product_option_list_items', compact( 'website_id', 'product_id', 'product_option_id', 'product_option_list_item_id' ), 'iiii' );

        // Get the notes
        $account_product_options = $this->account_product_option->get_with_list_items( $website_id, $product_id );

        $this->assertTrue( current( $account_product_options ) instanceof AccountProductOption );

        // Clean up
        $this->db->delete( 'product_options', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'product_option_list_items', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'website_product_options', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'website_product_option_list_items', compact( 'product_option_id' ), 'i' );
    }

    /**
     * Get Without List Items
     */
    public function testGetWithoutListItems() {
        // Declare variables
        $website_id = -5;
        $product_id = -3;
        $option_type = 'text';

        // Create everything
        $product_option_id = $this->db->insert( 'product_options', compact( 'option_type' ), 's' );
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id' ), 'iii' );

        // Get the notes
        $account_product_options = $this->account_product_option->get_without_list_items( $website_id, $product_id );

        $this->assertTrue( current( $account_product_options ) instanceof AccountProductOption );

        // Clean up
        $this->db->delete( 'product_options', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'website_product_options', compact( 'product_option_id' ), 'i' );
    }

    /**
     * Get All
     *
     * @depends testGetWithListItems
     * @depends testGetWithoutListItems
     */
    public function testGetAll() {
        // Declare variables
        $website_id = -5;
        $product_id = -3;
        $option_type = 'checkbox';
        $value = 'Extra Help';
        $price = -5;

        // Create everything
        $product_option_id = $this->db->insert( 'product_options', compact( 'option_type' ), 's' );
        $product_option_list_item_id = $this->db->insert( 'product_option_list_items', compact( 'product_option_id', 'value' ), 'is' );
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'iiii' );
        $this->db->insert( 'website_product_option_list_items', compact( 'website_id', 'product_id', 'product_option_id', 'product_option_list_item_id' ), 'iiii' );

        // Get the notes
        $account_product_options = $this->account_product_option->get_all( $website_id, $product_id );
        $account_product_option = current( $account_product_options );

        $this->assertEquals( $price, $account_product_option['price'] );

        // Clean up
        $this->db->delete( 'product_options', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'product_option_list_items', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'website_product_options', compact( 'product_option_id' ), 'i' );
        $this->db->delete( 'website_product_option_list_items', compact( 'product_option_id' ), 'i' );
    }

    /**
     * Add Bulk
     */
    public function testAddBulk() {
        // Declare Variables
        $website_id = -5;
        $product_id = -3;
        $product_options = array(
            array(
                'product_option_id' => -2
                , 'price' => -10
                , 'required' => 0
            ), array(
                'product_option_id' => -4
                , 'price' => -12
                , 'required' => 1
            ), array(
                'product_option_id' => -6
                , 'price' => -14
                , 'required' => 0
            )
        );

        $product_option_ids = array( -2, -4, -6 );

        // Insert
        $this->account_product_option->add_bulk( $website_id, $product_id, $product_options );

        // Check if it's there
        $retrieved_product_option_ids = $this->db->get_col( "SELECT `product_option_id` FROM `website_product_options` WHERE `website_id` = $website_id AND `product_id` = $product_id ORDER BY `product_option_id` DESC" );

        $this->assertEquals( $product_option_ids, $retrieved_product_option_ids );

        // Clean Up
        $this->db->delete( 'website_product_options', compact( 'website_id' ), 'i' );
    }

    /**
     * Add Bulk List Items
     */
    public function testAddBulkListItems() {
        // Declare Variables
        $website_id = -5;
        $product_id = -3;
        $product_option_list_items = array(
            array(
                'product_option_id' => -2
                , 'product_option_list_item_id' => -10
                , 'price' => -20
            ), array(
                'product_option_id' => -4
                , 'product_option_list_item_id' => -12
                , 'price' => -22
            ), array(
                'product_option_id' => -6
                , 'product_option_list_item_id' => -14
                , 'price' => -24
            )
        );

        $product_option_list_item_ids = array( -10, -12, -14 );

        // Insert
        $this->account_product_option->add_bulk_list_items( $website_id, $product_id, $product_option_list_items );

        // Check if it's there
        $retrieved_product_option_list_item_ids = $this->db->get_col( "SELECT `product_option_list_item_id` FROM `website_product_option_list_items` WHERE `website_id` = $website_id AND `product_id` = $product_id ORDER BY `product_option_list_item_id` DESC" );

        $this->assertEquals( $product_option_list_item_ids, $retrieved_product_option_list_item_ids );

        // Clean Up
        $this->db->delete( 'website_product_option_list_items', compact( 'website_id' ), 'i' );
    }

    /**
     * Delete Website Product Options
     */
    public function testDeleteWebsiteProductOptions() {
        // Declare Variables
        $website_id = -5;
        $product_id = -3;
        $product_option_id = -7;
        $price = -10;

        // Insert
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );

        // One more
        $product_option_id = -9;
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );

        // Access refactory
        $class = new ReflectionClass('AccountProductOption');
        $method = $class->getMethod('delete_website_product_options');
        $method->setAccessible(true);

        // Delete them
        $method->invokeArgs( $this->account_product_option, array( $website_id, $product_id ) );

        // Check
        $price = $this->db->get_var( "SELECT `price` FROM `website_product_options` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertFalse( $price );
    }

    /**
     * Delete Website Product Option List Items
     */
    public function testDeleteWebsiteProductOptionListItems() {
        // Declare Variables
        $website_id = -5;
        $product_id = -3;
        $product_option_id = -7;
        $price = -10;

        // Insert
        $this->db->insert( 'website_product_option_list_items', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );

        // One more
        $product_option_id = -9;
        $this->db->insert( 'website_product_option_list_items', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );

        // Access refactory
        $class = new ReflectionClass('AccountProductOption');
        $method = $class->getMethod('delete_website_product_option_list_items');
        $method->setAccessible(true);

        // Delete them
        $method->invokeArgs( $this->account_product_option, array( $website_id, $product_id ) );

        // Check
        $price = $this->db->get_var( "SELECT `price` FROM `website_product_option_list_items` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertFalse( $price );
    }

    /**
     * Delete By Product
     *
     * @depends testDeleteWebsiteProductOptions
     * @depends testDeleteWebsiteProductOptionListItems
     */
    public function testDeleteByProduct() {
        // Declare Variables
        $website_id = -5;
        $product_id = -3;
        $product_option_id = -7;
        $price = -10;

        // Insert
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );
        $this->db->insert( 'website_product_option_list_items', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );

        // One more
        $product_option_id = -9;
        $this->db->insert( 'website_product_options', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );
        $this->db->insert( 'website_product_option_list_items', compact( 'website_id', 'product_id', 'product_option_id', 'price' ), 'i' );

        // Delete everything!
        $this->account_product_option->delete_by_product( $website_id, $product_id );

        // Check both
        $website_product_option_list_items_price = $this->db->get_var( "SELECT `price` FROM `website_product_option_list_items` WHERE `website_id` = $website_id AND `product_id` = $product_id" );
        $website_product_options_price = $this->db->get_var( "SELECT `price` FROM `website_product_options` WHERE `website_id` = $website_id AND `product_id` = $product_id" );

        $this->assertFalse( $website_product_option_list_items_price );
        $this->assertFalse( $website_product_options_price );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->account_product_option = null;
    }
}
