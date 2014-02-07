<?php

require_once 'test/base-database-test.php';

class ChecklistWebsiteItemTest extends BaseDatabaseTest {
    const CHECKLIST_ID = 3;
    const CHECKLIST_ITEM_ID = 7;

    /**
     * @var ChecklistWebsiteItem
     */
    private $checklist_website_item;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_website_item = new ChecklistWebsiteItem();

        // Define
        $this->phactory->define( 'checklist_website_items', array( 'checklist_id' => self::CHECKLIST_ID, 'checklist_item_id' => self::CHECKLIST_ITEM_ID ) );
        $this->phactory->define( 'checklist_items', array( 'status' => ChecklistItem::STATUS_ACTIVE ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_checklist_website_item = $this->phactory->create('checklist_website_items');

        // Get
        $this->checklist_website_item->get( $ph_checklist_website_item->checklist_website_item_id );

        // Assert
        $this->assertEquals( self::CHECKLIST_ITEM_ID, $this->checklist_website_item->checklist_item_id );
    }

    /**
     * Test adding all the checklist items to a checklist
     */
    public function testAddAllToChecklist() {
        // Create
        $ph_checklist_item = $this->phactory->create('checklist_items');

        // Add to a checklist
        $this->checklist_website_item->add_all_to_checklist( self::CHECKLIST_ID );

        // Get item
        $checklist_website_item = $this->phactory->get( 'checklist_website_items', array( 'checklist_id' => self::CHECKLIST_ID ) );

        // Assert
        $this->assertEquals( $ph_checklist_item->checklist_item_id, $checklist_website_item->checklist_item_id );
    }

    /**
     * Test update
     */
    public function testUpdate() {
        // Create
        $ph_checklist_website_item = $this->phactory->create('checklist_website_items');

        // Update
        $this->checklist_website_item->id = $ph_checklist_website_item->checklist_website_item_id;
        $this->checklist_website_item->date_checked = '2014-01-08 00:00:00';
        $this->checklist_website_item->save();

        // Get item
        $checklist_website_item = $this->phactory->get( 'checklist_website_items', array( 'checklist_website_item_id' => $ph_checklist_website_item->checklist_website_item_id ) );

        // Assert
        $this->assertEquals( $this->checklist_website_item->date_checked, $checklist_website_item->date_checked );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_website_item = null;
    }
}
