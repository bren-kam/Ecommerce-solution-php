<?php

require_once 'test/base-database-test.php';

class ChecklistItemTest extends BaseDatabaseTest {
    const CHECKLIST_SECTION_ID = 7;
    const NAME = '1st Contact';
    const STATUS = 1;

    // Checklist Website Item
    const CHECKLIST_ID = 15;

    // Checklist Section
    const CHECKLIST_SECTION_STATUS = 1;

    /**
     * @var ChecklistItem
     */
    private $checklist_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_item = new ChecklistItem();

        // Define
        $this->phactory->define( 'checklist_items', array( 'checklist_section_id' => self::CHECKLIST_SECTION_ID, 'name' => self::NAME, 'status' => self::STATUS ) );
        $this->phactory->define( 'checklist_website_items', array( 'checklist_id' => self::CHECKLIST_ID ) );
        $this->phactory->define( 'checklist_sections', array( 'status' => self::CHECKLIST_SECTION_STATUS ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_checklist_item = $this->phactory->create( 'checklist_items' );

        // Get
        $this->checklist_item->get( $ph_checklist_item->checklist_item_id );

        // Assert
        $this->assertEquals( self::NAME, $this->checklist_item->name );
    }

    /**
     * Test Get All
     */
    public function testGetAll() {
        // Create
        $this->phactory->create( 'checklist_items' );

        // Get
        $checklist_items = $this->checklist_item->get_all();
        $checklist_item = current( $checklist_items );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ChecklistItem', $checklist_items );
        $this->assertEquals( self::NAME, $checklist_item->name );
    }

    /**
     * Tests getting incomplete checklists
     */
    public function testGetByChecklist() {
        // Create
        $ph_checklist_section = $this->phactory->create( 'checklist_sections' );
        $ph_checklist_item = $this->phactory->create( 'checklist_items', array( 'checklist_section_id' => $ph_checklist_section->checklist_section_id ) );
        $this->phactory->create( 'checklist_website_items', array( 'checklist_item_id' => $ph_checklist_item->checklist_item_id ) );

        // Get
        $checklist_items = $this->checklist_item->get_by_checklist( self::CHECKLIST_ID );
        $checklist_item = current( $checklist_items );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ChecklistItem', $checklist_items );
        $this->assertEquals( self::NAME, $checklist_item->name );
    }

    /**
     * Test creating
     *
     * @depends testGet
     */
    public function testCreate() {
        // Create
        $this->checklist_item->checklist_section_id = self::CHECKLIST_SECTION_ID;
        $this->checklist_item->create();

        // Assert
        $this->assertNotNull( $this->checklist_item->id );

        // Make sure it's in the database
        $ph_checklist_item = $this->phactory->get( 'checklist_items', array( 'checklist_item_id' => $this->checklist_item->id ) );

        // Assert
        $this->assertEquals( self::CHECKLIST_SECTION_ID, $ph_checklist_item->checklist_section_id );
    }

    /**
     * Test updating
     */
    public function testUpdate() {
        // Create
        $ph_checklist_item = $this->phactory->create( 'checklist_items' );

        // Update test
        $this->checklist_item->id = $ph_checklist_item->checklist_item_id;
        $this->checklist_item->name = 'Bloom';
        $this->checklist_item->save();

        // Make sure it's in the database
        $ph_checklist_item = $this->phactory->get( 'checklist_items', array( 'checklist_item_id' => $this->checklist_item->id ) );

        // Assert
        $this->assertEquals( $this->checklist_item->name, $ph_checklist_item->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_item = null;
    }
}
