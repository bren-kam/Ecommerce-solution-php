<?php

require_once 'test/base-database-test.php';

class WebsiteTopBrandTest extends BaseDatabaseTest {
    const BRAND_ID = 9;
    const SEQUENCE = 3;

    // Brands
    const NAME = 'Constantine Imperial Furniture';

    /**
     * @var WebsiteTopBrand
     */
    private $website_top_brand;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_top_brand = new WebsiteTopBrand();

        // Define
        $this->phactory->define( 'website_top_brands', array( 'website_id' => self::WEBSITE_ID, 'brand_id' => self::BRAND_ID, 'sequence' => self::SEQUENCE ) );
        $this->phactory->define( 'brands', array( 'name' => self::NAME ) );
        $this->phactory->recall();
    }


    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->website_top_brand->website_id = self::WEBSITE_ID;
        $this->website_top_brand->brand_id = self::BRAND_ID;
        $this->website_top_brand->create();

        // Get
        $ph_website_top_brand = $this->phactory->get( 'website_top_brands', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEquals( self::BRAND_ID, $ph_website_top_brand->brand_id );
    }

    /**
     * Get By Account
     */
    public function testGetByAccount() {
        // Create
        $ph_brand = $this->phactory->create('brands');
        $this->phactory->create( 'website_top_brands', array( 'brand_id' => $ph_brand->brand_id ) );

        // Get
        $brands = $this->website_top_brand->get_by_account( self::WEBSITE_ID );
        $brand = current( $brands );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Brand', $brands );
        $this->assertEquals( self::NAME, $brand->name );
    }

    /**
     * Update the sequence of categories
     */
    public function testUpdateSequence() {
        // Create
        $this->phactory->create('website_top_brands');

        // Update Sequence
        $this->website_top_brand->update_sequence( self::WEBSITE_ID, array( self::BRAND_ID ) );

        // Get
        $ph_website_top_brand = $this->phactory->get( 'website_top_brands', array( 'website_id' => self::WEBSITE_ID ) );
        $expected_sequence = 0;

        // Assert
        $this->assertEquals( $expected_sequence, $ph_website_top_brand->sequence );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $this->phactory->create('website_top_brands');

        // Remove
        $this->website_top_brand->remove( self::WEBSITE_ID, self::BRAND_ID );

        // Get
        $ph_website_top_brand = $this->phactory->get( 'website_top_brands', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertNull( $ph_website_top_brand );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_top_brand = null;
    }
}
