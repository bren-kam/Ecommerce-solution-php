<?php

require_once 'base-database-test.php';

class CategoryTest extends BaseDatabaseTest {
    /**
     * @var Category
     */
    private $category;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->category = new Category();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $category_id = -59;
        $name = 'Test Parent';

        // Create Category
        $this->db->insert( 'categories', compact( 'category_id', 'name' ), 'is' );

        // Get category
        $this->category->get( $category_id );

        // Should be a category
        $this->assertEquals( $this->category->name, 'Test Parent' );

        // Test getting from cache
        $this->category->name = null;
        $this->category->get( $category_id );

        // Should be a category
        $this->assertEquals( $this->category->name, 'Test Parent' );

        // Delete Category
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
    }

    /**
     * Test getting all the categories
     */
    public function testGetAll() {
        $categories = $this->category->get_all();

        $this->assertTrue( current( $categories ) instanceof Category );
    }

    /**
     * Test getting all the children
     */
    public function testGetAllChildren() {
        // Setup Variables
        $category_id = 283; // Appliances

        $categories = $this->category->get_all_children( $category_id );

        $this->assertEquals( $category_id, $categories[0]->parent_category_id );
    }

    /**
     * Test getting all the parents
     */
    public function testGetAllParents() {
        // Setup Variables
        $category_id = -59;
        $sub_category_id = -80;

        // Create Categories
        $this->db->insert( 'categories', compact( 'category_id' ), 'i' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );

        // Get all categories
        $categories = $this->category->get_all_parents( $sub_category_id );

        $this->assertEquals( $category_id, $categories[0]->id );

        // Delete
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
    }

    /**
     * Test getting top categories
     *
     * @depends testGet
     */
    public function testGetTop() {
        // Setup Variables
        $category_id = -59;
        $sub_category_id = -80;

        // Create Categories
        $this->db->insert( 'categories', compact( 'category_id' ), 'i' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );

        // Get all categories
        $this->category->get_top( $sub_category_id );

        $this->assertEquals( $category_id, $this->category->id );

        // Delete
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
    }

    /**
     * Test getting all the parnets
     */
    public function testGetAllParentCategoryIds() {
        // Setup Variables
        $category_id = -59;
        $sub_category_id = -80;

        // Create Categories
        $this->db->insert( 'categories', compact( 'category_id' ), 'i' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );

        // Get all categories
        $category_ids = $this->category->get_all_parent_category_ids( $sub_category_id );

        $this->assertTrue( in_array( $category_id, $category_ids ) );

        // Delete
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
    }

    /**
     * Test getting all the categories by a parent category ID
     */
    public function testGetByParent() {
        // Setup Variables
        $category_id = 283; // Appliances

        // Get the categories
        $categories = $this->category->get_by_parent( $category_id );

        $this->assertEquals( $category_id, $categories[0]->parent_category_id );
    }

    /**
     * Test getting url
     *
     * @depends testGetAll
     * @depends testGetAllParents
     */
    public function testGetUrl() {
        // Setup Variables
        $category_id = -59;
        $slug = 'parent';
        $sub_category_id = -80;
        $sub_slug = 'sub-name';
        $url = '/parent/sub-name/';

        // Create Categories
        $this->db->insert( 'categories', compact( 'category_id', 'slug' ), 'is' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id, 'slug' => $sub_slug ), 'iis' );

        // Get the categories
        $this->category->id = $sub_category_id;
        $fetched_url = $this->category->get_url();

        $this->assertEquals( $url, $fetched_url );

        // Delete
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
    }

    /**
     * Test has children
     *
     * @depends testGetAll
     * @depends testGetAllParents
     */
    public function testHasChildren() {
        // Setup Variables
        $category_id = -59;
        $sub_category_id = -80;

        // Create Categories
        $this->db->insert( 'categories', compact( 'category_id' ), 'i' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );

        // Get the categories
        $this->category->id = $category_id;
        $has_children = $this->category->has_children();

        $this->assertTrue( $has_children );

        // Delete
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'categories', array( 'category_id' => $sub_category_id ), 'i' );
    }

    /**
     * Create a category
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->category->name = 'Test Cat';
        $this->category->slug = 'test-cat';
        $this->category->parent_category_id = 0;
        $this->category->create();

        $this->assertTrue( !is_null( $this->category->id ) );

        // Make sure it's in the database
        $this->category->get( $this->category->id );

        $this->assertEquals( 'Test Cat', $this->category->name );

        // Delete the category
        $this->db->delete( 'categories', array( 'category_id' => $this->category->id ), 'i' );
    }

    /**
     * Update a category
     *
     * @depends testCreate
     */
    public function testUpdate() {
        $this->category->name = 'Test Cat';
        $this->category->slug = 'test-cat';
        $this->category->parent_category_id = 0;
        $this->category->create();

        // Update test
        $this->category->name = 'Cat Test';
        $this->category->slug = 'cat-test';
        $this->category->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->category->id ) );

        // Now check it!
        $this->category->get( $this->category->id );

        $this->assertEquals( 'cat-test', $this->category->slug );

        // Delete the category
        $this->db->delete( 'categories', array( 'category_id' => $this->category->id ), 'i' );
    }

    /**
     * Update the sequence of categories
     */
    public function testUpdateSequence() {
        // Setup Variables
        $category_id = -59;
        $sub_category_id = -80;
        $sub_category_id2 = -90;

        // Create Categories
        $this->db->insert( 'categories', compact( 'category_id' ), 'i' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id, 'parent_category_id' => $category_id ), 'ii' );
        $this->db->insert( 'categories', array( 'category_id' => $sub_category_id2, 'parent_category_id' => $category_id ), 'ii' );

        // Update the sequence
        $this->db->update( 'categories', array( 'sequence' => -5 ), array( 'category_id' => $category_id ), 'i', 'i' );

        // Adjust it properly
        $this->category->update_sequence( $category_id, array( $sub_category_id2, $sub_category_id ) );

        // Let's get the sequence and check
        $sequence = $this->db->get_var( 'SELECT `sequence` FROM `categories` WHERE `category_id` = ' . (int) $sub_category_id2 );

        // Should be 0;
        $this->assertEquals( 0, $sequence );

        // Delete
        $this->db->delete( 'categories', compact( 'category_id' ), 'i' );
        $this->db->delete( 'categories', array( 'parent_category_id' => $category_id ), 'i' );
    }

    /**
     * Test Delete
     *
     * @depends testCreate
     * @depends testGet
     */
    public function testDelete() {
        $this->category->name = 'Test Cat';
        $this->category->slug = 'test-cat';
        $this->category->parent_category_id = 0;
        $this->category->create();

        // Get it
        $this->category->get( $this->category->id );

        // Delete
        $this->category->delete();

        // Make sure it doesn't exist
        $name = $this->db->get_var( "SELECT `name` FROM `categories` WHERE `category_id` = " .(int) $this->category->id );

        $this->assertFalse( $name );
    }

    /**
     * Test getting all the categories by a parent category ID
     */
    public function testSortByHierarchy() {
        $this->category->get_all();
        $categories = $this->category->sort_by_hierarchy();

        $this->assertTrue( reset( $categories ) instanceof Category );
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