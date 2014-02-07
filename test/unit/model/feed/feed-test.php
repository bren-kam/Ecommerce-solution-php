<?php

require_once 'test/base-database-test.php';

class FeedTest extends BaseDatabaseTest {
    const SLUG = 'goat-cheese';

    // Brands
    const BRAND_NAME = 'Whitmore Cheese';

    // Categories
    const CATEGORY_NAME = 'American Cheeses';

    // Industries
    const INDUSTRY_NAME = 'Fromagerie';

    // Attributes
    const ATTRIBUTE_NAME = 'Age';

    // Attribute Item name
    const ATTRIBUTE_ITEM_NAME = '5 years and under';

    // Website Product Group
    const PRODUCT_GROUP_NAME = 'Henry Cheese Collection';

    /**
     * @var Feed
     */
    private $feed;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->feed = new Feed();

        // Define
        $this->phactory->define( 'products', array( 'slug' => self::SLUG, 'publish_visibility' => Product::PUBLISH_VISIBILITY_PUBLIC ) );
        $this->phactory->define( 'brands', array( 'name' => self::BRAND_NAME ) );
        $this->phactory->define( 'categories', array( 'name' => self::CATEGORY_NAME ) );
        $this->phactory->define( 'industries', array( 'name' => self::INDUSTRY_NAME ) );
        $this->phactory->define( 'attributes', array( 'name' => self::ATTRIBUTE_NAME ) );
        $this->phactory->define( 'attribute_items', array( 'attribute_item_name' => self::ATTRIBUTE_ITEM_NAME ) );
        $this->phactory->define( 'product_groups', array( 'name' => self::PRODUCT_GROUP_NAME ) );
        $this->phactory->recall();
    }


    /**
     * Test Getting Products
     */
    public function testGetProducts() {
        // Declare
        $start_date = $end_date = $starting_point = NULL;
        $limit = 100;

        // Create
        $this->phactory->create('products');

        // Get
        $products = $this->feed->get_products( $start_date, $end_date, $starting_point, $limit );

        // Assert
        $this->assertEquals( self::SLUG, $products[0]['slug']  );
    }

    /**
     * Test Getting Brands
     */
    public function testGetBrands() {
        // Create
        $this->phactory->create('brands');

        // Get
        $brands = $this->feed->get_brands();

        // Assert
        $this->assertEquals( self::BRAND_NAME, $brands[0]['name'] );
    }

    /**
     * Test Getting Categories
     */
    public function testGetCategories() {
        // Create
        $this->phactory->create('categories');

        // Get
        $categories = $this->feed->get_categories();

        // Assert
        $this->assertEquals( self::CATEGORY_NAME, $categories[0]['name'] );
    }

    /**
     * Test Getting Industries
     */
    public function testGetIndustries() {
        // Create
        $this->phactory->create('industries');

        // Get
        $industries = $this->feed->get_industries();

        // Assert
        $this->assertEquals( self::INDUSTRY_NAME, $industries[0]['name'] );
    }

    /**
     * Test Getting Attributes
     */
    public function testGetAttributes() {
        // Create
        $this->phactory->create('attributes');

        // Get
        $attributes = $this->feed->get_attributes();

        // Assert
        $this->assertEquals( self::ATTRIBUTE_NAME, $attributes[0]['name'] );
    }

    /**
     * Test Getting Attribute Items
     */
    public function testGetAttributeItems() {
        // Create
        $this->phactory->create('attribute_items');

        // Get
        $attribute_items = $this->feed->get_attribute_items();

        // Assert
        $this->assertEquals( self::ATTRIBUTE_ITEM_NAME, $attribute_items[0]['attribute_item_name'] );
    }

    /**
     * Test Getting Product Groups
     */
    public function testGetProductGroups() {
        // Create
        $this->phactory->create('product_groups');

        // Get
        $product_groups = $this->feed->get_product_groups();

        // Assert
        $this->assertEquals( self::PRODUCT_GROUP_NAME, $product_groups[0]['name'] );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->feed = null;
    }
}
