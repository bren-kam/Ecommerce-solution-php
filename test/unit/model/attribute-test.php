<?php
require_once 'test/base-database-test.php';

class AttributeTest extends BaseDatabaseTest {
    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->attribute = new Attribute();
    }

    /**
     * Test Getting an attribute
     */
    public function testGet() {
        $this->attribute->get(137);

        $this->assertEquals( $this->attribute->title, 'Color - Leather' );
    }

    /**
     * Test Getting all attributes
     */
    public function testGetAll() {
        $attributes = $this->attribute->get_all();

        $this->assertTrue( array_shift( $attributes ) instanceof Attribute );
    }

    /**
     * Test creating an attribute
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->attribute->title = 'Testee - Section';
        $this->attribute->name = 'Testee';
        $this->attribute->create();

        $this->assertTrue( !is_null( $this->attribute->id ) );

        // Make sure it's in the database
        $this->attribute->get( $this->attribute->id );

        $this->assertEquals( 'Testee - Section', $this->attribute->title );

        // Delete the attribute
        $this->phactory->delete( 'attributes', array( 'attribute_id' => $this->attribute->id ), 'i' );
    }

    /**
     * Test updating an attribute
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $this->attribute->title = 'Testee - Section';
        $this->attribute->name = 'Testee';
        $this->attribute->create();

        // Update test
        $this->attribute->title = 'noitceS - eetseT';
        $this->attribute->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->attribute->id ) );

        // Now check it!
        $this->attribute->get( $this->attribute->id );

        $this->assertEquals( 'noitceS - eetseT', $this->attribute->title );

        // Delete the attribute
        $this->phactory->delete( 'attributes', array( 'attribute_id' => $this->attribute->id ), 'i' );
    }

    /**
     * Add Category relations
     */
    public function testAddCategoryRelations() {
        $category_id = 0;
        $attribute_ids = array( '-1', '-2', '-3', '-4', '-5' );

        // Delete any that may have been created before the test
        $this->phactory->query( "DELETE FROM `attribute_relations` WHERE `category_id` = $category_id" );

        // Add the relations
        $this->attribute->add_category_relations( $category_id, $attribute_ids );

        // Get them for testing
        $fetched_attribute_ids = $this->phactory->get_col( "SELECT `attribute_id` FROM `attribute_relations` WHERE `category_id` = $category_id ORDER BY `attribute_id` DESC" );

        // Should be the same
        $this->assertEquals( $attribute_ids, $fetched_attribute_ids );

        // Delete them
        $this->phactory->query( "DELETE FROM `attribute_relations` WHERE `category_id` = $category_id" );
    }

    /**
     * Delete Category Relations
     *
     * @depends testAddCategoryRelations
     */
    public function testDeleteCategoryRelations() {
        // Declare variables
        $category_id = 0;
        $attribute_ids = array( '-1', '-2', '-3', '-4', '-5' );

        // Add relations
        $this->attribute->add_category_relations( $category_id, $attribute_ids );

        // Delete the relations
        $this->attribute->delete_category_relations( $category_id );

        // See if we can get it
        $attribute_id = $this->phactory->get_var( "SELECT `attribute_id` FROM `attribute_relations` WHERE `category_id` = $category_id" );

        $this->assertFalse( $attribute_id );
    }

    /**
     * Get Category Attribute IDs
     *
     * @depends testAddCategoryRelations
     */
    public function testGetCategoryAttributeIds() {
        // Declare variables
        $category_id = 0;
        $attribute_ids = array( '2', '15', '13', '12' );

        // Add relations
        $this->attribute->add_category_relations( $category_id, $attribute_ids );

        // Let's see if it matches
        $fetched_attribute_ids = $this->attribute->get_category_attribute_ids( $category_id );

        // Make sure they match
        $this->assertEquals( $attribute_ids, $fetched_attribute_ids );

        // Delete them
        $this->phactory->query( "DELETE FROM `attribute_relations` WHERE `category_id` = $category_id" );
    }

    /**
     * Test Deleting an attribute
     *
     * @depends testGet
     */
    public function testDelete() {
        // Create attribute
        $this->phactory->insert( 'attributes', array( 'title' => 'Temp Test', 'name' => 'Temp' ), 'iss' );

        $attribute_id = $this->phactory->get_insert_id();

        // Get it
        $this->attribute->get( $attribute_id );

        // Delete
        $this->attribute->delete();

        // Make sure it doesn't exist
        $title = $this->phactory->get_var( "SELECT `title` FROM `attributes` WHERE `attribute_id` = $attribute_id" );

        $this->assertFalse( $title );
    }

    /**
     * Test Listing all the attributes
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        $attributes = $this->attribute->list_all( $dt->get_variables() );

        // Make sure they exist
        $this->assertTrue( $attributes[0] instanceof Attribute );

        // Get rid of everything
        unset( $user, $_GET, $dt, $attributes );
    }

    /**
     * Test counting all the attributes
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 0;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        $count = $this->attribute->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

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
