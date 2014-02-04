<?php

require_once 'test/base-database-test.php';

class WebsiteProductGroupTest extends BaseDatabaseTest {
    /**
     * @var WebsiteProductGroup
     */
    private $website_product_group;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_product_group = new WebsiteProductGroup();
    }
    /**
     * Test Get
     */
    public function testReplace() {
        // Do Stuff
    }

//    /**
//     * Get
//     */
//    public function testGet() {
//        // Set variables
//        $website_id = -7;
//        $name = 'Ceiling Collection';
//
//        // Create
//        $website_product_group_id = $this->phactory->insert( 'website_product_groups', compact( 'website_id', 'name' ), 'is' );
//
//        // Get
//        $this->website_product_group->get( $website_product_group_id, $website_id );
//
//        // Make sure we grabbed the right one
//        $this->assertEquals( $name, $this->website_product_group->name );
//
//        // Clean up
//        $this->phactory->delete( 'website_product_groups', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Test create
//     */
//    public function testCreate() {
//        // Declare variables
//        $website_id = -9;
//        $name = 'Ceiling Collection';
//
//        // Create
//        $this->website_product_group->website_id = $website_id;
//        $this->website_product_group->name = $name;
//        $this->website_product_group->create();
//
//        // Make sure it's in the database
//        $retrieved_name = $this->phactory->get_var( "SELECT `name` FROM `website_product_groups` WHERE `website_id` = $website_id" );
//
//        $this->assertEquals( $name, $retrieved_name );
//
//        // Delete
//        $this->phactory->delete( 'website_product_groups', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Add Relations
//     */
//    public function testAddRelations() {
//        // Declare variables
//        $website_product_group_id = -5;
//        $product_ids = array( -2, -4, -6 );
//
//        // Set ID
//        $this->website_product_group->id = $website_product_group_id;
//
//        // Add relations
//        $this->website_product_group->add_relations( $product_ids );
//
//        // See if they are still there
//        $retrieved_product_ids = $this->phactory->get_col( "SELECT `product_id` FROM `website_product_group_relations` WHERE `website_product_group_id` = $website_product_group_id ORDER BY `product_id` DESC" );
//
//        // Make sure they are equal
//        $this->assertEquals( $product_ids, $retrieved_product_ids );
//
//        // Clean up
//        $this->phactory->delete( 'website_product_group_relations', compact( 'website_product_group_id' ), 'i' );
//    }
//
//    /**
//     * Save
//     *
//     * @depends testGet
//     */
//    public function testSave() {
//        // Declare variables
//        $website_id = -5;
//        $name = 'Ceiling Collection';
//
//        // Create
//        $website_product_group_id = $this->phactory->insert( 'website_product_groups', compact( 'website_id' ), 'i' );
//
//        // Get
//        $this->website_product_group->get( $website_product_group_id, $website_id );
//
//        // Save
//        $this->website_product_group->name = $name;
//        $this->website_product_group->save();
//
//        // Make sure it's in the database
//        $retrieved_name = $this->phactory->get_var( "SELECT `name` FROM `website_product_groups` WHERE `website_id` = $website_id" );
//
//        $this->assertEquals( $name, $retrieved_name );
//
//        // Clean up
//        $this->phactory->delete( 'website_product_groups', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Get Product Relation IDs
//     *
//     * @depends testAddRelations
//     */
//    public function testGetProductRelationIds() {
//        // Declare variables
//        $website_id = -3;
//        $product_ids = array( -2, -4, -6 );
//
//        // Create
//        $this->website_product_group->id = $this->phactory->insert( 'website_product_groups', compact( 'website_id' ), 'i' );
//        $this->website_product_group->add_relations( $product_ids );
//
//        // See if they are still there
//        $retrieved_product_ids = $this->website_product_group->get_product_relation_ids();
//
//        // Make sure they are equal
//        $this->assertEquals( $product_ids, $retrieved_product_ids );
//
//        // Clean up
//        $this->phactory->delete( 'website_product_group_relations', array( 'website_product_group_id' => $this->website_product_group->id ), 'i' );
//        $this->phactory->delete( 'website_product_groups', compact( 'website_id' ), 'i' );
//    }
//
//    /**
//     * Remove
//     *
//     * @depends testCreate
//     */
//    public function testRemove() {
//        // Declare variables
//        $website_id = -7;
//        $name = 'Ceiling Collection';
//
//        // Create
//        $this->website_product_group->website_id = $website_id;
//        $this->website_product_group->name = $name;
//        $this->website_product_group->create();
//
//        // Remove/Delete
//        $this->website_product_group->remove();
//
//        $retrieved_name = $this->phactory->get_var( "SELECT `name` FROM `website_product_groups` WHERE `website_id` = $website_id" );
//
//        $this->assertFalse( $retrieved_name );
//    }
//
//    /**
//     * Remove Relations
//     *
//     * @depends testAddRelations
//     */
//    public function testRemoveRelations() {
//        // Declare variables
//        $website_id = -3;
//        $product_ids = array( -2, -4, -6 );
//
//        // Create
//        $this->website_product_group->id = $this->phactory->insert( 'website_product_groups', compact( 'website_id' ), 'i' );
//        $this->website_product_group->add_relations( $product_ids );
//
//        // See if they are still there
//        $this->website_product_group->remove_relations();
//
//        // See if they are still there
//        $retrieved_product_ids = $this->phactory->get_col( "SELECT `product_id` FROM `website_product_group_relations` WHERE `website_product_group_id` = " . (int) $this->website_product_group->id );
//
//        // Make sure they are equal
//        $this->assertEquals( array(), $retrieved_product_ids );
//    }
//
//    /**
//     * List All
//     */
//    public function testListAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 0;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( '`name`' );
//
//        $website_product_groups = $this->website_product_group->list_all( $dt->get_variables() );
//
//        // Make sure we have an array
//        $this->assertTrue( current( $website_product_groups ) instanceof WebsiteProductGroup );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $emails );
//    }
//
//    /**
//     * Count All
//     */
//    public function testCountAll() {
//        $user = new User();
//        $user->get_by_email('test@greysuitretail.com');
//
//        // Determine length
//        $_GET['iDisplayLength'] = 30;
//        $_GET['iSortingCols'] = 0;
//        $_GET['iSortCol_0'] = 1;
//        $_GET['sSortDir_0'] = 'asc';
//
//        $dt = new DataTableResponse( $user );
//        $dt->order_by( '`name`' );
//
//        $count = $this->website_product_group->count_all( $dt->get_count_variables() );
//
//        // Make sure they exist
//        $this->assertGreaterThan( 0, $count );
//
//        // Get rid of everything
//        unset( $user, $_GET, $dt, $count );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_product_group = null;
    }
}
