<?php
require_once 'test/base-database-test.php';

class AttributeItemTest extends BaseDatabaseTest {
    const ATTRIBUTE_ID = 7;
    const ATTRIBUTE_ITEM_NAME = 'Horse Hair';

    // Attribute item relations
    const ATTRIBUTE_ITEM_ID = 15;
    const PRODUCT_ID = 13;

    // Attributes
    const TITLE = 'Colours';
    const CATEGORY_ID = 43;

    /**
     * @var AttributeItem
     */
    private $attribute_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->attribute_item = new AttributeItem();

        // Define
        $this->phactory->define( 'attribute_items', array( 'attribute_id' => self::ATTRIBUTE_ID, 'attribute_item_name' => self::ATTRIBUTE_ITEM_NAME ) );
        $this->phactory->define( 'attribute_item_relations', array( 'attribute_item_id' => self::ATTRIBUTE_ITEM_ID, 'product_id' => self::PRODUCT_ID ) );
        $this->phactory->define( 'attributes', array( 'title' => self::TITLE ) );
        $this->phactory->define( 'attribute_relations', array( 'attribute_id' => self::ATTRIBUTE_ID, 'category_id' => self::CATEGORY_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test add relations
     */
    public function testAddRelations() {
        // Add the relations
        $this->attribute_item->add_relations( self::PRODUCT_ID, array( self::ATTRIBUTE_ITEM_ID ) );

        // Get them for testing
        $ph_attribute_item_relation = $this->phactory->get( 'attribute_item_relations', array( 'product_id' => self::PRODUCT_ID ) );

        // Should be the same
        $this->assertEquals( self::ATTRIBUTE_ITEM_ID, $ph_attribute_item_relation->attribute_item_id );
    }

    /**
     * Test Getting an attribute item
     */
    public function testGet() {
        // Create
        $ph_attribute_item = $this->phactory->create( 'attribute_items' );

        // Black Attribute Item
        $this->attribute_item->get( $ph_attribute_item->attribute_item_id );

        $this->assertEquals( self::ATTRIBUTE_ID, $this->attribute_item->attribute_id );
    }

    /**
     * Test Getting all attribute items for an attribute
     */
    public function testGetByAttribute() {
        // Create
        $this->phactory->create( 'attribute_items' );

        // Get
        $attribute_items = $this->attribute_item->get_by_attribute( self::ATTRIBUTE_ID );
        $attribute_item = current( $attribute_items );

        $this->assertContainsOnlyInstancesOf( 'AttributeItem', $attribute_items );
        $this->assertEquals( self::ATTRIBUTE_ITEM_NAME, $attribute_item->name );
    }

    /**
     * Test Getting all attribute items for a category
     */
    public function testGetByCategory() {
        // Create
        $ph_attribute = $this->phactory->create('attributes');
        $this->phactory->create( 'attribute_items', array( 'attribute_id' => $ph_attribute->attribute_id ) );
        $this->phactory->create( 'attribute_relations', array( 'attribute_id' => $ph_attribute->attribute_id ) );

        // Get
        $attribute_items = $this->attribute_item->get_by_category( self::CATEGORY_ID );
        $attribute_item = current( $attribute_items );

        $this->assertContainsOnlyInstancesOf( 'AttributeItem', $attribute_items );
        $this->assertEquals( self::ATTRIBUTE_ITEM_NAME, $attribute_item->name );
    }

    /**
     * Test Getting all attribute items for a product
     */
    public function testGetByProduct() {
        // Create
        $ph_attribute = $this->phactory->create('attributes');
        $ph_attribute_item = $this->phactory->create( 'attribute_items', array( 'attribute_id' => $ph_attribute->attribute_id ) );
        $this->phactory->create( 'attribute_item_relations', array( 'attribute_item_id' => $ph_attribute_item->attribute_item_id ) );

        // Get
        $attribute_items = $this->attribute_item->get_by_product( self::PRODUCT_ID );
        $attribute_item = current( $attribute_items );

        $this->assertContainsOnlyInstancesOf( 'AttributeItem', $attribute_items );
        $this->assertEquals( self::ATTRIBUTE_ITEM_NAME, $attribute_item->name );
    }

    /**
     * Test creating an attribute item
     */
    public function testCreate() {
        // Create
        $this->attribute_item->name = self::ATTRIBUTE_ITEM_NAME;
        $this->attribute_item->create();

        $this->assertNotNull( $this->attribute_item->id );

        // Make sure it's in the database
        $ph_attribute_item = $this->phactory->get( 'attribute_items', array( 'attribute_item_id' => $this->attribute_item->id ) );

        $this->assertEquals( self::ATTRIBUTE_ITEM_NAME, $ph_attribute_item->attribute_item_name );
    }

    /**
     * Test updating the attribute item
     */
    public function testUpdate() {
        // Create
        $ph_attribute_item = $this->phactory->create('attribute_items');

        // Update test
        $this->attribute_item->id = $ph_attribute_item->attribute_item_id;
        $this->attribute_item->name = 'eetseT';
        $this->attribute_item->save();

        // Make sure it's in the database
        $ph_attribute_item = $this->phactory->get( 'attribute_items', array( 'attribute_item_id' => $this->attribute_item->id ) );

        $this->assertEquals( $this->attribute_item->name, $ph_attribute_item->attribute_item_name );
    }

    /**
     * Test Deleting an attribute
     */
    public function testDelete() {
        // Create
        $ph_attribute_item = $this->phactory->create('attribute_items');

        // Delete
        $this->attribute_item->id = $ph_attribute_item->attribute_item_id;
        $this->attribute_item->delete();

        // Should not be there
        $ph_attribute_item = $this->phactory->get( 'attribute_items', array( 'attribute_item_id' => $this->attribute_item->id ) );

        $this->assertNull( $ph_attribute_item );
    }

    /**
     * Delete Relations
     *
     * @depends testAddRelations
     */
    public function testDeleteRelations() {
        // Create
        $this->phactory->create('attribute_item_relations');

        // Delete the relations
        $this->attribute_item->delete_relations( self::PRODUCT_ID );

        // Make sure it's in the database
        $ph_attribute_item = $this->phactory->get( 'attribute_item_relations', array( 'product_id' => self::PRODUCT_ID ) );

        $this->assertNull( $ph_attribute_item );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->attribute_item = null;
    }
}
