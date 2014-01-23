<?php
require_once 'test/base-database-test.php';

class CompanyTest extends BaseDatabaseTest {
    /**
     * @var Company
     */
    private $company;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->company = new Company();
    }

    /**
     * Test getting the company
     */
    public function testGet() {
        // Declare variables
        $company_id = 4;

        // Get company
        $this->company->get( $company_id );

        $this->assertEquals( 'Grey Suit Retail', $this->company->name );
    }

    /**
     * Test getting the companies as a class
     */
    public function testGetAllClass() {
        $companies = $this->company->get_all();

        $this->assertTrue( $companies[0] instanceof Company );
    }

    /**
     * Test getting the companies as an array
     */
    public function testGetAllArray() {
        $companies = $this->company->get_all( PDO::FETCH_ASSOC );

        $this->assertTrue( is_array( $companies[0] ) );
    }

    /**
     * Test creating a company
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->company->name = 'Master Hoppers';
        $this->company->create();

        $this->assertTrue( !is_null( $this->company->id ) );

        // Make sure it's in the database
        $this->company->get( $this->company->id );

        $this->assertEquals( 'Master Hoppers', $this->company->name );

        // Delete the company
        $this->phactory->delete( 'companies', array( 'company_id' => $this->company->id ), 'i' );
    }

    /**
     * Test updating a company
     *
     * @depends testCreate
     */
    public function testSave() {
        // Create test
        $this->company->name = 'Master Hoppers';
        $this->company->create();

        // Update test
        $this->company->name = 'Mister Hoppers';
        $this->company->domain = 'misterhoppers.com';
        $this->company->save();

        // Make sure we have an ID still
        $this->assertTrue( !is_null( $this->company->id ) );

        // Now check it!
        $this->company->get( $this->company->id );

        $this->assertEquals( 'misterhoppers.com', $this->company->domain );

        // Delete the company
        $this->phactory->delete( 'companies', array( 'company_id' => $this->company->id ), 'i' );
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
        $dt->order_by( '`name`', '`domain`', '`date_created`' );
        $dt->search( array( '`name`' => true, '`domain`' => true ) );

        $companies = $this->company->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( is_array( $companies ) );

        // Grey Suit Retail company with ID 4
        $grey_suit_retail_exists = false;

        if ( is_array( $companies ) )
        foreach ( $companies as $company ) {
            if ( 4 == $company->id ) {
                $grey_suit_retail_exists = true;
                break;
            }
        }

        // Make sure they exist
        $this->assertTrue( $grey_suit_retail_exists );

        // Get rid of everything
        unset( $user, $_GET, $dt, $companies, $account, $grey_suit_retail_exists );
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
        $dt->order_by( '`name`', '`domain`', '`date_created`' );
        $dt->search( array( '`name`' => true, '`domain`' => true ) );

        $count = $this->company->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->company = null;
    }
}