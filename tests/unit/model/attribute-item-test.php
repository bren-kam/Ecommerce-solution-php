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
        $this->attribute_item->update();

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
     * Will be executed after every test
     */
    public function tearDown() {
        $this->attribute_item = null;
    }
}
