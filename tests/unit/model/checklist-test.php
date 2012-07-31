<?php

require_once 'base-database-test.php';

class ChecklistTest extends BaseDatabaseTest {
    /**
     * @var Checklist
     */
    private $checklist;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->checklist = new Checklist();
    }

    /**
     * Tests getting incomplete checklists
     */
    public function testGetIncomplete() {
        $incomplete_checklists = $this->checklist->get_incomplete();

        $this->assertTrue( is_array( $incomplete_checklists ) );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist = null;
    }
}
