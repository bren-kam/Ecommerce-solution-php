<?php

require_once 'test/base-database-test.php';

class KnowledgeBaseCategoryTest extends BaseDatabaseTest {
    const NAME = 'Products';

    /**
     * @var KnowledgeBaseCategory
     */
    private $kb_category;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->kb_category = new KnowledgeBaseCategory( KnowledgeBaseCategory::SECTION_ADMIN );

        // Define
        $this->phactory->define( 'kb_category', array( 'section' => KnowledgeBaseCategory::SECTION_ADMIN, 'name' => self::NAME ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_kb_category = $this->phactory->create('kb_category');

        // Get
        $this->kb_category->get( $ph_kb_category->id );

        // Assert
        $this->assertEquals( self::NAME, $this->kb_category->name );
    }

    /**
     * Test getting all the children
     */
    public function testGetAllChildren() {
        // Declare
        $name = 'Pricing Tools (Child)';

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $this->phactory->create( 'kb_category', array( 'parent_id' => $ph_kb_category->id, 'name' => $name ) );

        // Get
        $categories = $this->kb_category->get_all_children( $ph_kb_category->id );
        $category = current( $categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBaseCategory', $categories );
        $this->assertEquals( $name, $category->name );
    }

    /**
     * Test getting all the parents
     */
    public function testGetAllParents() {
        // Declare
        $name = 'Pricing Tools (Child)';

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $ph_kb_category_child = $this->phactory->create( 'kb_category', array( 'parent_id' => $ph_kb_category->id, 'name' => $name ) );

        // Get all categories
        $categories = $this->kb_category->get_all_parents( $ph_kb_category_child->id );
        $category = current( $categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBaseCategory', $categories );
        $this->assertEquals( self::NAME, $category->name );
    }

    /**
     * Test getting all the categories by a parent category ID
     */
    public function testGetByParent() {
        // Declare
        $name = 'Pricing Tools (Child)';

        // Create
        $ph_kb_category = $this->phactory->create('kb_category');
        $this->phactory->create( 'kb_category', array( 'parent_id' => $ph_kb_category->id, 'name' => $name ) );

        // Get the categories
        $categories = $this->kb_category->get_by_parent( $ph_kb_category->id );
        $category = current( $categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBaseCategory', $categories );
        $this->assertEquals( $name, $category->name );
    }

    /**
     * Test getting all the categories
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('kb_category');

        // Get
        $categories = $this->kb_category->get_all();
        $category = current( $categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBaseCategory', $categories );
        $this->assertEquals( self::NAME, $category->name );
    }

    /**
     * Create a category
     *
     * @depends testGet
     */
    public function testCreate() {
        // Create
        $this->kb_category->name = self::NAME;
        $this->kb_category->create();

        // Assert
        $this->assertNotNull( $this->kb_category->id );

        // Get
        $ph_kb_category = $this->phactory->get( 'kb_category', array( 'id' => $this->kb_category->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_kb_category->name );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_kb_category = $this->phactory->create('kb_category');

        // Save
        $this->kb_category->id = $ph_kb_category->id;
        $this->kb_category->name = 'Reaches';
        $this->kb_category->save();

        // Get
        $ph_kb_category = $this->phactory->get( 'kb_category', array( 'id' => $ph_kb_category->id ) );

        // Assert
        $this->assertEquals( $this->kb_category->name, $ph_kb_category->name );
    }

    /**
     * Test Delete
     *
     * @depends testCreate
     */
    public function testDelete() {
        // Create
        $ph_kb_category = $this->phactory->create('kb_category');

        // Delete
        $this->kb_category->id = $ph_kb_category->id;
        $this->kb_category->delete();

        // Get
        $ph_kb_category = $this->phactory->get( 'kb_category', array( 'id' => $ph_kb_category->id ) );

        // Assert
        $this->assertNull( $ph_kb_category );
    }

    /**
     * Test getting all the categories by a parent category ID
     *
     * @depends testGetByParent
     * @depends testGetAll
     */
    public function testSortByHierarchy() {
        // Reset
        $this->phactory->recall();

        // Create
        $this->phactory->create('kb_category');

        // Sort them
        $categories = $this->kb_category->sort_by_hierarchy();
        $category = current( $categories );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'KnowledgeBaseCategory', $categories );
        $this->assertNotNull( $category->depth );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->kb_category = KnowledgeBaseCategory::$categories_by_parent = KnowledgeBaseCategory::$categories = null;
    }
}