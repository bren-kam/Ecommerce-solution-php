<?php

require_once 'base-database-test.php';

class FeedTest extends BaseDatabaseTest {
    /**
     * @var Feed
     */
    private $feed;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->feed = new Feed();
    }

    /**
     * Test Getting Products
     */
    public function testGetProducts() {
        // Declare variables
        $start_date = $end_date = $starting_point = NULL;
        $limit = 100;

        $products = $this->feed->get_products( $start_date, $end_date, $starting_point, $limit );

        $this->assertTrue( isset( $products[0]['brand_id'] ) );
        $this->assertEquals( $limit, count( $products ) );
    }

    /**
     * Test Getting Brands
     */
    public function testGetBrands() {
        $brands = $this->feed->get_brands();

        $this->assertTrue( isset( $brands[0]['brand_id'] ) );
    }

    /**
     * Test Getting Categories
     */
    public function testGetCategories() {
        $categories = $this->feed->get_categories();

        $this->assertTrue( isset( $categories[0]['category_id'] ) );
    }

    /**
     * Test Getting Industries
     */
    public function testGetIndustries() {
        $industries = $this->feed->get_industries();

        $this->assertTrue( isset( $industries[0]['industry_id'] ) );
    }

    /**
     * Test Getting Attributes
     */
    public function testGetAttributes() {
        $attributes = $this->feed->get_attributes();

        $this->assertTrue( isset( $attributes[0]['attribute_id'] ) );
    }

    /**
     * Test Getting Attribute Items
     */
    public function testGetAttributeItems() {
        $attribute_items = $this->feed->get_attribute_items();

        $this->assertTrue( isset( $attribute_items[0]['attribute_item_id'] ) );
    }

    /**
     * Test Getting Product Groups
     */
    public function testGetProductGroups() {
        $product_groups = $this->feed->get_product_groups();

        $this->assertTrue( isset( $product_groups[0]['product_group_id'] ) );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->feed = null;
    }
}
