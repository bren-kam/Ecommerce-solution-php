<?php
require_once 'test/base-database-test.php';

class AttributeTest extends BaseDatabaseTest {
    const TITLE = 'Colours';
    const NAME = 'Colorful Colours';

    // Attribute Relations
    const ATTRIBUTE_ID = 13;
    const CATEGORY_ID = 19;

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->attribute = new Attribute();

        // Define
        $this->phactory->define( 'attributes', array( 'title' => self::TITLE, 'name' => self::NAME ) );
        $this->phactory->define( 'attribute_relations', array( 'attribute_id' => self::ATTRIBUTE_ID, 'category_id' => self::CATEGORY_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test Getting an attribute
     */
    public function testGet() {
        // Create
        $ph_attribute = $this->phactory->create( 'attributes' );

        // Get
        $this->attribute->get( $ph_attribute->attribute_id );

        // Assert Equals
        $this->assertEquals( self::TITLE, $this->attribute->title );
    }

    /**
     * Test Getting all attributes
     */
    public function testGetAll() {
        // Create
        $this->phactory->create( 'attributes' );

        // Get
        $attributes = $this->attribute->get_all();
        $attribute = current( $attributes );

        $this->assertContainsOnlyInstancesOf( 'Attribute', $attributes );
        $this->assertEquals( self::TITLE, $attribute->title );
    }

    /**
     * Test creating an attribute
     */
    public function testCreate() {
        // Create
        $this->attribute->title = self::TITLE;
        $this->attribute->name = self::NAME;
        $this->attribute->create();

        $this->assertTrue( !is_null( $this->attribute->id ) );

        // Make sure it's in the database
        $ph_attribute = $this->phactory->get( 'attributes', array( 'attribute_id' => $this->attribute->id ) );

        $this->assertEquals( self::TITLE, $ph_attribute->title );
    }

    /**
     * Test updating an attribute
     */
    public function testUpdate() {
        // Create
        $ph_attribute = $this->phactory->create('attributes');

        // Update test
        $this->attribute->id = $ph_attribute->attribute_id;
        $this->attribute->title = 'noitceS - eetseT';
        $this->attribute->save();

        // Make sure it's in the database
        $ph_attribute = $this->phactory->get( 'attributes', array( 'attribute_id' => $ph_attribute->attribute_id ) );

        $this->assertEquals( $this->attribute->title, $ph_attribute->title );
    }

    /**
     * Add Category relations
     */
    public function testAddCategoryRelations() {
        // Reset
        $this->phactory->recall();

        // Add the relations
        $this->attribute->add_category_relations( self::CATEGORY_ID, array( self::ATTRIBUTE_ID ) );

        // Make sure it's in the database
        $ph_attribute_relation = $this->phactory->get( 'attribute_relations', array( 'category_id' => self::CATEGORY_ID ) );

        $this->assertEquals( self::ATTRIBUTE_ID, $ph_attribute_relation->attribute_id );
    }

    /**
     * Delete Category Relations
     *
     * @depends testAddCategoryRelations
     */
    public function testDeleteCategoryRelations() {
        // Create
        $this->phactory->create( 'attribute_relations' );

        // Delete the relations
        $this->attribute->delete_category_relations( self::CATEGORY_ID );

        // Make sure it's in the database
        $ph_attribute_relation = $this->phactory->get( 'attribute_relations', array( 'category_id' => self::CATEGORY_ID ) );

        $this->assertNull( $ph_attribute_relation );
    }

    /**
     * Get Category Attribute IDs
     */
    public function testGetCategoryAttributeIds() {
        // Reset
        $this->phactory->recall();

        // Create
        $ph_attribute = $this->phactory->create('attributes');
        $this->phactory->create( 'attribute_relations', array( 'attribute_id' => $ph_attribute->attribute_id ) );

        // Let's see if it matches
        $attribute_ids = $this->attribute->get_category_attribute_ids( self::CATEGORY_ID );
        $expected_attribute_ids = array( $ph_attribute->attribute_id );

        // Make sure they match
        $this->assertEquals( $expected_attribute_ids, $attribute_ids );
    }

    /**
     * Test Deleting an attribute
\     */
    public function testDelete() {
        // Create attribute
        $ph_attribute = $this->phactory->create('attributes');

        // Delete
        $this->attribute->id = $ph_attribute->attribute_id;
        $this->attribute->delete();

        // Make sure it doesn't exist
        $ph_attribute = $this->phactory->get( 'attributes', array( 'attribute_id' => $ph_attribute->attribute_id ) );

        $this->assertNull( $ph_attribute );
    }

    /**
     * Test Listing all the attributes
     */
    public function testListAll() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('attributes');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        $attributes = $this->attribute->list_all( $dt->get_variables() );
        $attribute = current( $attributes );

        // Make sure they exist
        $this->assertContainsOnlyInstancesOf( 'Attribute', $attributes );
        $this->assertEquals( self::TITLE, $attribute->title );

        // Get rid of everything
        unset( $user, $_GET, $dt, $attributes );
    }

    /**
     * Test counting all the attributes
     */
    public function testCountAll() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('attributes');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        $count = $this->attribute->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->attribute = null;
    }
}
