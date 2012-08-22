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
     * Test getting all the categories
     */
    public function testGetAll() {
        $categories = $this->category->get_all();

        $this->assertTrue( $categories[0] instanceof Category );
    }

    /**
     * Test getting all the children
     */
    public function testGetAllChildren() {
        $categories = $this->category->get_all_children(559);

        $this->assertTrue( $categories[0] instanceof Category );
        $this->assertEquals( count( $categories ), 3 );
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