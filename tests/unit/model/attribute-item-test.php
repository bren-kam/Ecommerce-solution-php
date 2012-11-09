<?php
require_once 'base-database-test.php';

class AttributeItemTest extends BaseDatabaseTest {
    /**
     * @var AttributeItem
     */
    private $attribute_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->attribute_item = new AttributeItem();
    }

    /**
     * Test add relations
     */
    public function testAddRelations() {
        // Declare variables
        $product_id = 0;
        $attribute_item_ids = array( '-1', '-2', '-3', '-4', '-5' );

        // Delete any that may have been created before the test
        $this->db->query( "DELETE FROM `attribute_item_relations` WHERE `product_id` = $product_id" );

        // Add the relations
        $this->attribute_item->add_relations( $product_id, $attribute_item_ids );

        // Get them for testing
        $fetched_attribute_item_ids = $this->db->get_col( "SELECT `attribute_item_id` FROM `attribute_item_relations` WHERE `product_id` = $product_id ORDER BY `attribute_item_id` DESC" );

        // Should be the same
        $this->assertEquals( $attribute_item_ids, $fetched_attribute_item_ids );

        // Delete them
        $this->db->query( "DELETE FROM `attribute_item_relations` WHERE `product_id` = $product_id" );
    }

    /**
     * Test Getting an attribute item
     */
    public function testGet() {
        // Black Attribute Item
        $attribute_item_id = 6;
        $this->attribute_item->get($attribute_item_id);

        $this->assertEquals( $this->attribute_item->name, 'Black' );
    }

    /**
     * Test Getting all attribute items for an attribute
     */
    public function testGetByAttribute() {
        $attribute_id = 2;
        $attribute_items = $this->attribute_item->get_by_attribute( $attribute_id );

        $this->assertTrue( current( $attribute_items ) instanceof AttributeItem );
        $this->assertEquals( count( $attribute_items ), 13 );
    }

    /**
     * Test Getting all attribute items for a category
     */
    public function testGetByCategory() {
        $category_id = 99;
        $attribute_items = $this->attribute_item->get_by_category( $category_id );

        $this->assertTrue( current( $attribute_items ) instanceof AttributeItem );
        $this->assertEquals( count( $attribute_items ), 36 );
    }

    /**
     * Test Getting all attribute items for a product
     */
    public function testGetByProduct() {
        $product_id = 147;
        $attribute_items = $this->attribute_item->get_by_product( $product_id );

        $this->assertTrue( current( $attribute_items ) instanceof AttributeItem );
        $this->assertEquals( count( $attribute_items ), 4 );
    }

    /**
     * Test creating an attribute item
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->attribute_item->attribute_id = 0;
        $this->attribute_item->name = 'Testee';
        $this->attribute_item->sequence = 0;
        $this->attribute_item->create();

        $this->assertTrue( !is_null( $this->attribute_item->id ) );

        // Make sure it's in the database
        $this->attribute_item->get( $this->attribute_item->id );

        $this->assertEquals( 'Testee', $this->attribute_item->name );

        // Delete the attribute item
        $this->db->delete( 'attribute_items', array( 'attribute_item_id' => $this->attribute_item->id ), 'i' );
    }

    /**
     * Test updating the attribute item
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $this->attribute_item->attribute_id = 0;
        $this->attribute_item->name = 'Testee';
        $this->attribute_item->sequence = 0;
        $this->attribute_item->create();

        // Update test
        $this->attribute_item->name = 'eetseT';
        $this->attribute_item->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->attribute_item->id ) );

        // Now check it!
        $this->attribute_item->get( $this->attribute_item->id );

        $this->assertEquals( 'eetseT', $this->attribute_item->name );

        // Delete the attribute item
        $this->db->delete( 'attribute_items', array( 'attribute_item_id' => $this->attribute_item->id ), 'i' );
    }

    /**
     * Test Deleting an attribute
     *
     * @depends testCreate
     * @depends testGet
     */
    public function testDelete() {
        // Create test
        $this->attribute_item->attribute_id = 0;
        $this->attribute_item->name = 'Testee';
        $this->attribute_item->sequence = 0;
        $this->attribute_item->create();

        // Get it
        $this->attribute_item->get( $this->attribute_item->id );

        // Delete
        $this->attribute_item->delete();

        // Make sure it doesn't exist
        $name = $this->db->get_var( "SELECT `name` FROM `attribute_items` WHERE `attribute_item_id` = " . (int) $this->attribute_item->id );

        $this->assertFalse( $name );
    }



    /**
     * Delete Relations
     *
     * @depends testAddRelations
     */
    public function testDeleteRelations() {
        // Declare variables
        $product_id = 0;
        $attribute_item_ids = array( '-1', '-2', '-3', '-4', '-5' );

         // Add the relations
        $this->attribute_item->add_relations( $product_id, $attribute_item_ids );

        // Delete the relations
        $this->attribute_item->delete_relations( $product_id );

        // See if we can get it
        $attribute_item_id = $this->db->get_var( "SELECT `attribute_item_id` FROM `attribute_item_relations` WHERE `product_id` = $product_id" );

        $this->assertFalse( $attribute_item_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->attribute_item = null;
    }
}
