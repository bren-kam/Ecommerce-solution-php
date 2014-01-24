<?php

require_once 'test/base-database-test.php';

class CategoryTest extends BaseDatabaseTest {
    const NAME = 'Ovens';

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
        $this->phactory->define( 'categories', array( 'name' => self::NAME ) );
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
        // Reset
        $this->phactory->recall();

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

//    /**
//     * Test getting all the parents
//     */
//    public function testGetAllParents() {
//        // Setup Variables
//        $category_id = -59;
//        $sub_category_id = -80;
//
//        // Create Categories
//        $this->phactory->insert( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );
//
//        // Get all categories
//        $categories = $this->category->get_all_parents( $sub_category_id );
//
//        $this->assertEquals( $category_id, $categories[0]->id );
//
//        // Delete
//        $this->phactory->delete( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
//    }
//
//    /**
//     * Test getting top categories
//     *
//     * @depends testGet
//     */
//    public function testGetTop() {
//        // Setup Variables
//        $category_id = -59;
//        $sub_category_id = -80;
//
//        // Create Categories
//        $this->phactory->insert( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );
//
//        // Get all categories
//        $this->category->get_top( $sub_category_id );
//
//        $this->assertEquals( $category_id, $this->category->id );
//
//        // Delete
//        $this->phactory->delete( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
//    }
//
//    /**
//     * Test getting all the parnets
//     */
//    public function testGetAllParentCategoryIds() {
//        // Setup Variables
//        $category_id = -59;
//        $sub_category_id = -80;
//
//        // Create Categories
//        $this->phactory->insert( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );
//
//        // Get all categories
//        $category_ids = $this->category->get_all_parent_category_ids( $sub_category_id );
//
//        $this->assertTrue( in_array( $category_id, $category_ids ) );
//
//        // Delete
//        $this->phactory->delete( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
//    }
//
//
//
//    /**
//     * Test getting url
//     *
//     * @depends testGetAll
//     * @depends testGetAllParents
//     */
//    public function testGetUrl() {
//        // Setup Variables
//        $category_id = -59;
//        $slug = 'parent';
//        $sub_category_id = -80;
//        $sub_slug = 'sub-name';
//        $url = '/parent/sub-name/';
//
//        // Create Categories
//        $this->phactory->insert( 'categories', compact( 'category_id', 'slug' ), 'is' );
//        $this->phactory->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id, 'slug' => $sub_slug ), 'iis' );
//
//        // Get the categories
//        $this->category->id = $sub_category_id;
//        $fetched_url = $this->category->get_url();
//
//        $this->assertEquals( $url, $fetched_url );
//
//        // Delete
//        $this->phactory->delete( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
//    }
//

//
//    /**
//     * Create a category
//     *
//     * @depends testGet
//     */
//    public function testCreate() {
//        $this->category->name = 'Test Cat';
//        $this->category->slug = 'test-cat';
//        $this->category->parent_category_id = 0;
//        $this->category->create();
//
//        $this->assertNotNull( $this->category->id ) );
//
//        // Make sure it's in the database
//        $this->category->get( $this->category->id );
//
//        $this->assertEquals( 'Test Cat', $this->category->name );
//
//        // Delete the category
//        $this->phactory->delete( 'categories', array( 'category_id' => $this->category->id ), 'i' );
//    }
//
//    /**
//     * Update a category
//     *
//     * @depends testCreate
//     */
//    public function testUpdate() {
//        $this->category->name = 'Test Cat';
//        $this->category->slug = 'test-cat';
//        $this->category->parent_category_id = 0;
//        $this->category->create();
//
//        // Update test
//        $this->category->name = 'Cat Test';
//        $this->category->slug = 'cat-test';
//        $this->category->save();
//
//        // Make sure we have an ID still
//        $this->assertNotNull( $this->category->id ) );
//
//        // Now check it!
//        $this->category->get( $this->category->id );
//
//        $this->assertEquals( 'cat-test', $this->category->slug );
//
//        // Delete the category
//        $this->phactory->delete( 'categories', array( 'category_id' => $this->category->id ), 'i' );
//    }
//
//    /**
//     * Update the sequence of categories
//     */
//    public function testUpdateSequence() {
//        // Setup Variables
//        $category_id = -59;
//        $sub_category_id = -80;
//        $sub_category_id2 = -90;
//
//        // Create Categories
//        $this->phactory->insert( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );
//        $this->phactory->insert( 'categories', array( 'category_id' => $sub_category_id2, 'parent_category_id' => $category_id ), 'ii' );
//
//        // Update the sequence
//        $this->phactory->update( 'categories', array( 'sequence' => -5 ), array( 'category_id' => $category_id ), 'i', 'i' );
//
//        // Adjust it properly
//        $this->category->update_sequence( $category_id, array( $sub_category_id2, $sub_category_id ) );
//
//        // Let's get the sequence and check
//        $sequence = $this->phactory->get_var( 'SELECT `sequence` FROM `categories` WHERE `category_id` = ' . (int) $sub_category_id2 );
//
//        // Should be 0;
//        $this->assertEquals( 0, $sequence );
//
//        // Delete
//        $this->phactory->delete( 'categories', compact( 'category_id' ), 'i' );
//        $this->phactory->delete( 'categories', array( 'parent_category_id' => $category_id ), 'i' );
//    }
//
//    /**
//     * Test Delete
//     *
//     * @depends testCreate
//     * @depends testGet
//     */
//    public function testDelete() {
//        $this->category->name = 'Test Cat';
//        $this->category->slug = 'test-cat';
//        $this->category->parent_category_id = 0;
//        $this->category->create();
//
//        // Get it
//        $this->category->get( $this->category->id );
//
//        // Delete
//        $this->category->delete();
//
//        // Make sure it doesn't exist
//        $name = $this->phactory->get_var( "SELECT `name` FROM `categories` WHERE `category_id` = " .(int) $this->category->id );
//
//        $this->assertFalse( $name );
//    }
//
//    /**
//     * Test getting all the categories by a parent category ID
//     */
//    public function testSortByHierarchy() {
//        $this->category->get_all();
//        $categories = $this->category->sort_by_hierarchy();
//
//        $this->assertTrue( reset( $categories ) instanceof Category );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        Category::$categories = null;
        Category::$categories_by_parent = null;
        $this->category = null;
    }
}