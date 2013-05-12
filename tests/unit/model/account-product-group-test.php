<?php

require_once 'base-database-test.php';

class AccountProductGroupTest extends BaseDatabaseTest {
    /**
     * @var AccountProductGroup
     */
    private $account_product_group;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_product_group = new AccountProductGroup();
    }

    /**
     * Test Get By Name
     */
    public function testGetByName() {
        // Declare variables
        $website_id = -3;
        $name = 'Long board';

        // Create
        $website_product_group_id = $this->db->insert( 'website_product_groups', compact( 'website_id', 'name' ), 'is' );

        // Get
        $this->account_product_group->get_by_name( $website_id, $name );

        $this->assertEquals( $website_product_group_id, $this->account_product_group->id );

        // Delete the attribute
        $this->db->delete( 'website_product_groups', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Create
     */
    public function testCreate() {
        // Declare variables
        $website_id = -3;
        $name = 'ABC 0123';

        // Create
        $this->account_product_group->website_id = -3;
        $this->account_product_group->name = $name;
        $this->account_product_group->create();

        // Make sure it's in the database
        $fetched_name = $this->db->get_var( "SELECT `name` FROM `website_product_groups` WHERE `website_id` = $website_id" );

        $this->assertEquals( $name, $fetched_name );

        // Delete the attribute
        $this->db->delete( 'website_product_groups', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Add Relations By Series
     */
    public function testAddRelationsBySeries() {
        // Declare variables
        $website_product_group_id = $this->account_product_group->id = -3;
        $website_id = $this->account_product_group->website_id = -5;
        $sku = $series = '-B105';
        $active = 1;

        // Insert products
        $product_id = $this->db->insert( 'products', compact( 'sku' ), 's' );
        $this->db->insert( 'website_products', compact( 'website_id', 'product_id', 'active' ) , 'iii' );

        // Add Relations
        $this->account_product_group->add_relations_by_series( $series );

        // Get
        $fetched_product_id = $this->db->get_var( "SELECT `product_id` FROM `website_product_group_relations` WHERE `website_product_group_id` = $website_product_group_id ORDER BY `product_id` DESC" );

        $this->assertEquals( $product_id, $fetched_product_id );

        // Clean Up
        $this->db->delete( 'website_product_group_relations', compact( 'product_id' ), 'i' );
        $this->db->delete( 'products', compact( 'product_id' ), 'i' );
        $this->db->delete( 'website_products', compact( 'website_id' ), 'i' );
    }

    /**
     * Test Add Relations
     */
    public function testAddRelations() {
        // Declare variables
        $website_product_group_id = $this->account_product_group->id = -3;
        $product_ids = array( -1, -2, -3 );

        // Add Relations
        $this->account_product_group->add_relations( $product_ids );

        // Get
        $fetched_product_ids = $this->db->get_col( "SELECT `product_id` FROM `website_product_group_relations` WHERE `website_product_group_id` = $website_product_group_id ORDER BY `product_id` DESC" );

        $this->assertEquals( $product_ids, $fetched_product_ids );

        // Clean Up
        $this->db->delete( 'website_product_group_relations', compact( 'website_product_group_id' ), 'i' );
    }

    /**
     * Test Remove
     *
     * @depends testCreate
     */
    public function testRemove() {
        // Declare variables
        $website_id = -3;
        $name = 'ABC 0123';

        // Create
        $this->account_product_group->website_id = -3;
        $this->account_product_group->name = $name;
        $this->account_product_group->create();

        // Remove
        $this->account_product_group->remove();

        // Make sure it's in the database
        $fetched_name = $this->db->get_var( "SELECT `name` FROM `website_product_groups` WHERE `website_id` = $website_id" );

        $this->assertFalse( $fetched_name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_product_group = null;
    }
}
