<?php

require_once 'base-database-test.php';

class ChecklistSectionTest extends BaseDatabaseTest {
    /**
     * @var ChecklistSection
     */
    private $checklist_section;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_section = new ChecklistSection();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Declare variables
        $checklist_section_id = 1;

        $this->checklist_section->get( $checklist_section_id );

        $this->assertEquals( $this->checklist_section->name, 'Initial Setup' );
    }

    /**
     * Test Get All
     */
    public function testGetAll() {
        $checklist_sections = $this->checklist_section->get_all();

        $this->assertTrue( current( $checklist_sections ) instanceof ChecklistSection );
    }

    /**
     * Test creating
     *
     * @depends testGet
     */
    public function testCreate() {
        // Create
        $this->checklist_section->status = 5;
        $this->checklist_section->create();

        $this->assertTrue( !is_null( $this->checklist_section->id ) );

        // Make sure it's in the database
        $this->checklist_section->get( $this->checklist_section->id );

        $this->assertEquals( 5, $this->checklist_section->status );

        // Delete
        $this->db->delete( 'checklist_sections', array( 'checklist_section_id' => $this->checklist_section->id ), 'i' );
    }

    /**
     * Test updating
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Create test
        $this->checklist_section->status = 0;
        $this->checklist_section->create();

        // Update test
        $this->checklist_section->name = "Sweet jumpin' jambalaya";
        $this->checklist_section->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->checklist_section->id ) );

        // Now check it!
        $this->checklist_section->get( $this->checklist_section->id );

        $this->assertEquals( "Sweet jumpin' jambalaya", $this->checklist_section->name );

        // Delete
        $this->db->delete( 'checklist_sections', array( 'checklist_section_id' => $this->checklist_section->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_section = null;
    }
}
