<?php

require_once 'test/base-database-test.php';

class ChecklistSectionTest extends BaseDatabaseTest {
    const NAME = 'Initial Setup';

    /**
     * @var ChecklistSection
     */
    private $checklist_section;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist_section = new ChecklistSection();

        // Define
        $this->phactory->define( 'checklist_sections', array( 'name' => self::NAME ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_checklist_section = $this->phactory->create('checklist_sections');

        // Get
        $this->checklist_section->get( $ph_checklist_section->checklist_section_id );

        // Assert
        $this->assertEquals( self::NAME, $this->checklist_section->name );
    }

    /**
     * Test Get All
     */
    public function testGetAll() {
        // Create
        $this->phactory->create('checklist_sections');

        // Get
        $checklist_sections = $this->checklist_section->get_all();
        $checklist_section = current( $checklist_sections );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'ChecklistSection', $checklist_sections );
        $this->assertEquals( self::NAME, $checklist_section->name );
    }

    /**
     * Test creating
     */
    public function testCreate() {
        // Create
        $this->checklist_section->status = ChecklistSection::STATUS_ACTIVE;
        $this->checklist_section->create();

        // Assert
        $this->assertNotNull( $this->checklist_section->id );

        // Make sure it's in the database
        $ph_checklist_section = $this->phactory->get( 'checklist_sections', array( 'checklist_section_id' => $this->checklist_section->id ) );

        // Assert
        $this->assertEquals( ChecklistSection::STATUS_ACTIVE, $ph_checklist_section->status );
    }

    /**
     * Test updating
     */
    public function testUpdate() {
        // Create
        $ph_checklist_section = $this->phactory->create('checklist_sections');

        // Update test
        $this->checklist_section->id = $ph_checklist_section->checklist_section_id;
        $this->checklist_section->name = "Sweet jumpin' jambalaya";
        $this->checklist_section->save();

        // Get
        $ph_checklist_section = $this->phactory->get( 'checklist_sections', array( 'checklist_section_id' => $ph_checklist_section->checklist_section_id ) );

        $this->assertEquals( $this->checklist_section->name, $ph_checklist_section->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist_section = null;
    }
}
