<?php

require_once 'test/base-database-test.php';

class CategoryTest extends BaseDatabaseTest {
    const NAME = 'Ovens';
    const SLUG = 'ovens';
    const SEQUENCE = 1;
    const PARENT_CATEGORY_ID = 0;

    /**
     * @var Category
     */
    private $category;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->category = new Category();

        // Define
        $this->phactory->define( 'categories', array( 'name' => self::NAME, 'slug' => self::SLUG, 'sequence' => self::SEQUENCE, 'parent_category_id' => self::PARENT_CATEGORY_ID ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_category = $this->phactory->create( 'categories' );

        // Get category
        $this->category->get( $ph_category->category_id );

        // Should be a category
        $this->assertEquals( self::NAME, $this->category->name );

        // Test getting from cache
        $this->category->name = null;
        $this->category->get( $ph_category->category_id );

        // Should be a category
        $this->assertEquals( self::NAME, $this->category->name );
    }

    /**
     * Test getting all the categories
     */
    public function testGetAll() {
        // Create
        $this->phactory->create( 'categories' );

        // Get
        $categories = $this->category->get_all();
        $category = current( $categories );

        $this->assertContainsOnlyInstancesOf( 'Category', $categories );
        $this->assertEquals( self::NAME, $category->name );
        $this->assertEquals( $categories, Category::$categories );
    }

    /**
     * Sort By Parent
     *
     * @depends testGetAll
     */
    public function testSortByParent() {
         // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Make it possible to call this function
        $class = new ReflectionClass('Category');
        $method = $class->getMethod( 'sort_by_parent' );
        $method->setAccessible(true);

        // Sort by parent
        $method->invoke( $this->category );
        $category = current( Category::$categories_by_parent[$ph_category->category_id] );

        $this->assertContainsOnlyInstancesOf( 'Category', Category::$categories_by_parent[$ph_category->category_id] );
        $this->assertEquals( $childs_name, $category->name );
    }

    /**
     * Test has children
     *
     * @depends testSortByParent
     */
    public function testHasChildren() {
        // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Check
        $this->category->id = $ph_category->category_id;
        $has_children = $this->category->has_children();

        // Assert
        $this->assertTrue( $has_children );

        // Try another method
        $has_children = $this->category->has_children( $ph_category->category_id );

        // Assert
        $this->assertTrue( $has_children );
    }

    /**
     * Test getting all the categories by a parent category ID
     *
     * @depends testSortByParent
     * @depends testHasChildren
     */
    public function testGetByParent() {
        // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Get the categories
        $empty = '';
        $categories = $this->category->get_by_parent( $empty );

        // Assert
        $this->assertEquals( array(), $categories );

        // Try an actual id
        $categories = $this->category->get_by_parent( $ph_category->category_id );
        $category = current( $categories );

        $this->assertContainsOnlyInstancesOf( 'Category', $categories );
        $this->assertEquals( $childs_name, $category->name );
    }

    /**
     * Test getting all the children
     *
     * @depends testGetByParent
     */
    public function testGetAllChildren() {
        // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Get all children
        $categories = $this->category->get_all_children( $ph_category->category_id );
        $category = current( $categories );

        $this->assertContainsOnlyInstancesOf( 'Category', $categories );
        $this->assertEquals( $ph_category->category_id, $category->parent_category_id );
    }

    /**
     * Test getting all the parents
     */
    public function testGetAllParents() {
        // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $ph_child_category = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Get all categories
        $categories = $this->category->get_all_parents( $ph_child_category->category_id );
        $category = current( $categories );

       $this->assertContainsOnlyInstancesOf( 'Category', $categories );
       $this->assertEquals( $ph_category->category_id, $category->category_id );
    }

    /**
     * Test getting top categories
     *
     * @depends testGet
     */
    public function testGetTop() {
        // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $ph_child_category = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Get all categories
        $this->category->get_top( $ph_child_category->category_id );

        // Assert
        $this->assertEquals( $ph_category->category_id, $this->category->id );
    }

    /**
     * Test getting all the parents
     *
     * @depends testGetAllParents
     */
    public function testGetAllParentCategoryIds() {
        // Declare
        $childs_name = 'Speakeasy Ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $ph_child_category = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'name' => $childs_name ) );

        // Get all categories
        $category_ids = $this->category->get_all_parent_category_ids( $ph_child_category->category_id );
        $expected_category_ids = array( $ph_category->category_id );

        // Assert
        $this->assertEquals( $expected_category_ids, $category_ids );
    }

    /**
     * Test getting url
     *
     * @depends testGetAll
     * @depends testGetAllParents
     */
    public function testGetUrl() {
        // Declare
        $childs_slug = 'speakeasy-ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $ph_child_category = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'slug' => $childs_slug ) );

        // Get the categories
        $this->category->id = $ph_child_category->category_id;
        $category_url = $this->category->get_url();
        $expected_url = '/' . self::SLUG . '/' . $childs_slug . '/';

        $this->assertEquals( $expected_url, $category_url );
    }

    /**
     * Create a category
     */
    public function testCreate() {
        // Create
        $this->category->name = self::NAME;
        $this->category->create();

        // Assert
        $this->assertNotNull( $this->category->id );

        // Make sure it's in the database
        $ph_category = $this->phactory->get( 'categories', array( 'category_id' => $this->category->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_category->name );
    }

    /**
     * Update a category
     */
    public function testUpdate() {
        // Create
        $ph_category = $this->phactory->create('categories');

        // Update test
        $this->category->id = $ph_category->category_id;
        $this->category->name = 'Cat Test';
        $this->category->save();

        // Now check it!
        $ph_category = $this->phactory->get( 'categories', array( 'category_id' => $ph_category->category_id ) );

        $this->assertEquals( $this->category->name, $ph_category->name );
    }

    /**
     * Update the sequence of categories
     */
    public function testUpdateSequence() {
        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $ph_child_category = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id ) );
        $ph_child_category_2 = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id ) );

        // Adjust it properly
        $this->category->update_sequence( $ph_category->category_id, array( $ph_child_category->category_id, $ph_child_category_2->category_id ) );

        // Let's get the sequence and check
        $ph_category = $this->phactory->get( 'categories', array( 'category_id' => $ph_child_category->category_id ) );
        $expected_sequence = 0;

        // Assert
        $this->assertEquals( $expected_sequence, $ph_category->sequence );
    }

    /**
     * Test Delete
     */
    public function testDelete() {
        // Create
        $ph_category = $this->phactory->create('categories');

        // Delete
        $this->category->id = $ph_category->category_id;
        $this->category->remove();

        // Get
        $ph_category = $this->phactory->get( 'categories', array( 'category_id' => $ph_category->category_id ) );

        $this->assertNull( $ph_category );
    }

    /**
     * Test getting all the categories by a parent category ID
     *
     * depends testGetByParent
     */
    public function testSortByHierarchy() {
        // Declare
        $childs_slug = 'speakeasy-ovens';

        // Create
        $ph_category = $this->phactory->create( 'categories' );
        $ph_child_category = $this->phactory->create( 'categories', array( 'parent_category_id' => $ph_category->category_id, 'slug' => $childs_slug ) );

        // Get
        $categories = $this->category->sort_by_hierarchy();
        $category = current( $categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Category', $categories );
        $this->assertNotNull( $category->depth );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        Category::$categories = null;
        Category::$categories_by_parent = null;
        $this->category = null;
    }
}