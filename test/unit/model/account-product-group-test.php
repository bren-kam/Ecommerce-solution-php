<?php

require_once 'test/base-database-test.php';

class AccountProductGroupTest extends BaseDatabaseTest {
    const WEBSITE_PRODUCT_GROUP_ID = 5;
    const NAME = 'Long board';

    // Products
    const PRODUCT_ID = 15;
    const SKU = '-B105';

    // Website products
    const ACTIVE = 1;

    /**
     * @var AccountProductGroup
     */
    private $account_product_group;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_product_group = new AccountProductGroup();

        // Define
        $this->phactory->define( 'website_product_groups', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME ) );
        $this->phactory->define( 'website_product_group_relations', array( 'website_product_group_id' => self::WEBSITE_PRODUCT_GROUP_ID, 'product_id' => self::PRODUCT_ID ) );
        $this->phactory->define( 'products', array( 'sku' => self::SKU ) );
        $this->phactory->define( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'active' => self::ACTIVE ) );
    }

    /**
     * Test Get By Name
     */
    public function testGetByName() {
        // Create
        $ph_website_product_group = $this->phactory->create( 'website_product_groups' );

        // Get
        $this->account_product_group->get_by_name( self::WEBSITE_ID, self::NAME );

        $this->assertEquals( $ph_website_product_group->website_product_group_id, $this->account_product_group->id );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Create
        $this->account_product_group->website_id = self::WEBSITE_ID;
        $this->account_product_group->name = self::NAME;
        $this->account_product_group->create();

        $this->assertNotNull( $this->account_product_group->id );

        // Make sure it's in the database
        $ph_website_product_group = $this->phactory->get( 'website_product_groups', array( 'website_product_group_id' => $this->account_product_group->id ) );

        $this->assertEquals( self::NAME, $ph_website_product_group->name );
    }

    /**
     * Test Add Relations By Series
     */
    public function testAddRelationsBySeries() {
        // Declare
        $series = self::SKU;

        // Insert products
        $ph_product = $this->phactory->create( 'products' );
        $this->phactory->create( 'website_products', array( 'product_id' => $ph_product->product_id ) );

        // Add Relations
        $this->account_product_group->id = self::WEBSITE_PRODUCT_GROUP_ID;
        $this->account_product_group->website_id = self::WEBSITE_ID;
        $this->account_product_group->add_relations_by_series( $series );

        // Get
        $ph_website_product_group_relation = $this->phactory->get( 'website_product_group_relations', array( 'website_product_group_id' => self::WEBSITE_PRODUCT_GROUP_ID ) );

        // Assert
        $this->assertEquals( $ph_product->product_id, $ph_website_product_group_relation->product_id );
    }

    /**
     * Test Add Relations
     */
    public function testAddRelations() {
        // Declare variables
        $product_ids = array( self::PRODUCT_ID );

        // Add Relations
        $this->account_product_group->id = self::WEBSITE_PRODUCT_GROUP_ID;
        $this->account_product_group->add_relations( $product_ids );

        // Get
        $ph_website_product_group_relation = $this->phactory->get( 'website_product_group_relations', array( 'website_product_group_id' => self::WEBSITE_PRODUCT_GROUP_ID ) );

        // Assert
        $this->assertEquals( self::PRODUCT_ID, $ph_website_product_group_relation->product_id );
    }

    /**
     * Test Remove
     */
    public function testRemove() {
        // Insert
        $ph_website_product_group = $this->phactory->create( 'website_product_groups' );

        // Remove
        $this->account_product_group->id = $ph_website_product_group->website_product_group_id;
        $this->account_product_group->remove();

        // Shouldn't exist
        $ph_website_product_group = $this->phactory->get( 'website_product_groups', array( 'website_product_group_id' => $ph_website_product_group->website_product_group_id ) );

        $this->assertNull( $ph_website_product_group );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product_group = null;
    }
}
