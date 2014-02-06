<?php

require_once 'test/base-database-test.php';

class WebsiteProductGroupTest extends BaseDatabaseTest {
    const NAME = 'Ceiling Collection';

    // Website Product Group Relations
    const WEBSITE_PRODUCT_GROUP_ID = 11;
    const PRODUCT_ID = 13;

    /**
     * @var WebsiteProductGroup
     */
    private $website_product_group;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->website_product_group = new WebsiteProductGroup();

        // Define
        $this->phactory->define( 'website_product_groups', array( 'website_id' => self::WEBSITE_ID, 'name' => self::NAME ) );
        $this->phactory->define( 'website_product_group_relations', array( 'website_product_group_id' => self::WEBSITE_PRODUCT_GROUP_ID, 'product_id' => self::PRODUCT_ID ) );
        $this->phactory->recall();
    }

    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_product_group = $this->phactory->create('website_product_groups');

        // Get
        $this->website_product_group->get( $ph_website_product_group->website_product_group_id, self::WEBSITE_ID );

        // Assert
        $this->assertEquals( self::NAME, $this->website_product_group->name );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->website_product_group->website_id = self::WEBSITE_ID;
        $this->website_product_group->name = self::NAME;
        $this->website_product_group->create();

        // Assert
        $this->assertNotNull( $this->website_product_group->id );

        // Get
        $ph_website_product_group = $this->phactory->get( 'website_product_groups', array( 'website_id' => self::WEBSITE_ID ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_website_product_group->name );
    }

    /**
     * Add Relations
     */
    public function testAddRelations() {
        // Add relations
        $this->website_product_group->id = self::WEBSITE_PRODUCT_GROUP_ID;
        $this->website_product_group->add_relations( array( self::PRODUCT_ID ) );

        // Get
        $ph_website_product_group_relation = $this->phactory->get( 'website_product_group_relations', array( 'website_product_group_id' => self::WEBSITE_PRODUCT_GROUP_ID ) );

        // Assert
        $this->assertEquals( self::PRODUCT_ID, $ph_website_product_group_relation->product_id );
    }

    /**
     * Save
     */
    public function testSave() {
        // Create
        $ph_website_product_group = $this->phactory->create('website_product_groups');

        // Save
        $this->website_product_group->id = $ph_website_product_group->website_product_group_id;
        $this->website_product_group->name = 'Bedroom Group';
        $this->website_product_group->save();

        // Get
        $ph_website_product_group = $this->phactory->get( 'website_product_groups', array( 'website_product_group_id' => $ph_website_product_group->website_product_group_id ) );

        // Assert
        $this->assertEquals( $this->website_product_group->name, $ph_website_product_group->name );
    }

    /**
     * Get Product Relation IDs
     *
     * @depends testAddRelations
     */
    public function testGetProductRelationIds() {
        // Create
        $ph_website_product_group = $this->phactory->create('website_product_groups');
        $this->phactory->create( 'website_product_group_relations', array( 'website_product_group_id' => $ph_website_product_group->website_product_group_id ) );

        // Get
        $this->website_product_group->id = $ph_website_product_group->website_product_group_id;
        $retrieved_product_ids = $this->website_product_group->get_product_relation_ids();
        $expected_product_ids = array( self::PRODUCT_ID );

        // Assert
        $this->assertEquals( $expected_product_ids, $retrieved_product_ids );
    }

    /**
     * Remove
     */
    public function testRemove() {
        // Create
        $ph_website_product_group = $this->phactory->create('website_product_groups');

        // Delete
        $this->website_product_group->id = $ph_website_product_group->website_product_group_id;
        $this->website_product_group->remove();

        // Get
        $ph_website_product_group = $this->phactory->get( 'website_product_groups', array( 'website_product_group_id' => $ph_website_product_group->website_product_group_id ) );

        // Assert
        $this->assertNull( $ph_website_product_group );
    }

    /**
     * Remove Relations
     */
    public function testRemoveRelations() {
        // Create
        $this->phactory->create('website_product_group_relations');

        // Remove
        $this->website_product_group->id = self::WEBSITE_PRODUCT_GROUP_ID;
        $this->website_product_group->remove_relations();

        // Get
        $ph_website_product_group_relation = $this->phactory->get( 'website_product_group_relations', array( 'website_product_group_id' => self::WEBSITE_PRODUCT_GROUP_ID ) );

        // Assert
        $this->assertNull( $ph_website_product_group_relation );
    }

    /**
     * List All
     */
    public function testListAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_product_groups');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`' );

        // Get
        $website_product_groups = $this->website_product_group->list_all( $dt->get_variables() );
        $website_product_group = current( $website_product_groups );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'WebsiteProductGroup', $website_product_groups );
        $this->assertEquals( self::NAME, $website_product_group->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        // Stub
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('website_product_groups');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 0;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`' );

        // Get
        $count = $this->website_product_group->count_all( $dt->get_count_variables() );

        // Assert
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->website_product_group = null;
    }
}
