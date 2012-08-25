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
        $category_id = 559;

        // Get category
        $category = $this->category->get( $category_id );

        // Should be a category
        $this->assertTrue( $category instanceof Category );
    }

    /**
     * Test getting all the categories
     */
    public function testGetAll() {
        $categories = $this->category->get_all();

        $this->assertTrue( array_shift( $categories ) instanceof Category );
    }

    /**
     * Test getting all the children
     */
    public function testGetAllChildren() {
        $categories = $this->category->get_all_children(559);

        $this->assertEquals( count( $categories ), 3 );
        $this->assertTrue( array_shift( $categories ) instanceof Category );
    }

    /**
     * Test getting all the parnets
     */
    public function testGetAllParents() {
        $categories = $this->category->get_all_parents(562);

        $this->assertEquals( count( $categories ), 2 );
        $this->assertTrue( array_shift( $categories ) instanceof Category );
    }

    /**
     * Test getting all the categories by a parent category ID
     */
    public function testGetByParent() {
        $categories = $this->category->get_by_parent(559);

        $this->assertTrue( $categories[0] instanceof Category );
        $this->assertEquals( count( $categories ), 2 );
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
        $this->category->update();

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
        // Define variables
        $parent_category_id = 559;
        $sequences = array( 560, 561 );
        $category_id = 560;

        // Update the sequence
        $this->db->update( 'categories', array( 'sequence' => -5 ), array( 'category_id' => $category_id ), 'i', 'i' );

        // Adjust it properly
        $this->category->update_sequence( $parent_category_id, $sequences );

        // Let's get the sequence and check
        $sequence = $this->db->get_var( 'SELECT `sequence` FROM `categories` WHERE `category_id` = ' . (int) $sequences[0] );

        // Should be 0;
        $this->assertEquals( $sequence, 0 );
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

        $this->assertTrue( $categories[0] instanceof Category );
        $this->assertEquals( $categories[0]->name, 'Test Parent' );
        $this->assertEquals( $categories[1]->name, 'Test Child One' );
        $this->assertEquals( $categories[1]->depth, 1 );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->category = null;
    }
}