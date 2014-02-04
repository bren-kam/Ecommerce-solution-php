<?php

require_once 'test/base-database-test.php';

class WebsiteTopBrandTest extends BaseDatabaseTest {
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
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }
//
//    /**
//     * Test create
//     */
//    public function testCreate() {
//        // Declare variables
//        $website_id = -5;
//        $brand_id = -3;
//
//        // Create
//        $this->website_top_brand->website_id = $website_id;
//        $this->website_top_brand->brand_id = $brand_id;
//        $this->website_top_brand->create();
//
//        // Make sure it's in the database
//        $retrieved_brand_id = $this->phactory->get_var( "SELECT `brand_id` FROM `website_top_brands` WHERE `website_id` = $website_id" );
//
//        $this->assertEquals( $brand_id, $retrieved_brand_id );
//
//        // Delete
//        $this->phactory->delete( 'website_top_brand', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Get By Account
//     */
//    public function testGetByAccount() {
//        // Declare Variables
//        $website_id = -5;
//        $brand_id = -3;
//        $name = 'Ooh, la la!';
//
//        // Create
//        $this->phactory->insert( 'brands', compact( 'brand_id', 'name' ), 'is' );
//        $this->phactory->insert( 'website_top_brands', compact( 'website_id', 'brand_id' ), 'ii' );
//
//        // Get all
//        $brands = $this->website_top_brand->get_by_account( $website_id );
//
//        $this->assertTrue( current( $brands ) instanceof Brand );
//
//        // Clean up
//        $this->phactory->delete( 'brands', compact( 'website_id' ), 'i' );
//        $this->phactory->delete( 'website_top_brands', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Update the sequence of categories
//     */
//    public function testUpdateSequence() {
//        // Setup Variables
//        $website_id = -9;
//        $brand_id = -5;
//        $brand_id2 = -7;
//
//        // Create Categories
//        $this->phactory->insert( 'website_top_brands', compact( 'website_id', 'brand_id' ), 'ii' );
//        $this->phactory->insert( 'website_top_brands', array( 'website_id' => $website_id, 'brand_id' => $brand_id2 ), 'ii' );
//
//        // Adjust it properly
//        $this->website_top_brand->update_sequence( $website_id, array( $brand_id, $brand_id2 ) );
//
//        // Let's get the sequence and check
//        $sequence = $this->phactory->get_var( "SELECT `sequence` FROM `website_top_brands` WHERE `website_id` = $website_id AND `sequence` > 0" );
//
//        // Should be 0;
//        $this->assertEquals( 1, $sequence );
//
//        // Delete
//        $this->phactory->delete( 'website_top_brands', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Remove
//     */
//    public function testRemove() {
//        // Set variables
//        $website_id = -9;
//        $brand_id = -7;
//
//        // Create
//        $this->phactory->insert( 'website_top_brands', compact( 'website_id', 'brand_id' ), 'ii' );
//
//        // Get
//        $this->website_top_brand->remove( $website_id, $brand_id );
//
//        $retrieved_brand_id = $this->phactory->get_var( "SELECT `brand_id` FROM `website_top_brands` WHERE `website_id` = $website_id" );
//
//        $this->assertFalse( $retrieved_brand_id );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_top_brand = null;
    }
}
