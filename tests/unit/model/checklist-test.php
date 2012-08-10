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
     * Test listing all companies
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'days_left', 'b.`title`', 'a.`type`', 'a.`date_created`' );
        $dt->search( array( 'b.`title`' => false ) );

        $checklists = $this->checklist->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( $checklists[0] instanceof Checklist );

        // Get rid of everything
        unset( $user, $_GET, $dt, $checklists );
    }

    /**
     * Test counting the companies
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'days_left', 'b.`title`', 'a.`type`', 'a.`date_created`' );
        $dt->search( array( 'b.`title`' => false ) );

        $count = $this->checklist->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->checklist = null;
    }
}
