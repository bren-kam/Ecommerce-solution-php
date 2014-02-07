<?php
require_once 'test/base-database-test.php';

class CompanyTest extends BaseDatabaseTest {
    const NAME = 'Billy Bobs Furniture';

    /**
     * @var Company
     */
    private $company;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->company = new Company();

        // Define
        $this->phactory->define( 'companies', array( 'name' => self::NAME ) );
        $this->phactory->recall();
    }

    /**
     * Test getting the company
     */
    public function testGet() {
        // Create
        $ph_company = $this->phactory->create('companies');

        // Get
        $this->company->get( $ph_company->company_id );

        // Assert
        $this->assertEquals( self::NAME, $this->company->name );
    }

    /**
     * Test getting the companies as a class
     */
    public function testGetAll() {
        // Create
        $ph_company = $this->phactory->create('companies');

        // Get
        $companies = $this->company->get_all();
        $company = current( $companies );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'Company', $companies );
        $this->assertEquals( self::NAME, $company->name );

        // Get as an array
        $companies = $this->company->get_all( PDO::FETCH_ASSOC );
        $expected_array = array( array( 'company_id' => $ph_company->company_id, 'name' => $ph_company->name ) );

        // Assert
        $this->assertEquals( $expected_array, $companies );
    }

    /**
     * Test creating a company
     */
    public function testCreate() {
        // Create
        $this->company->name = self::NAME;
        $this->company->create();

        // Assert
        $this->assertNotNull( $this->company->id );

        // Make sure it's in the database
        $ph_company = $this->phactory->get( 'companies', array( 'company_id' => $this->company->id ) );

        // Assert
        $this->assertEquals( self::NAME, $ph_company->name );
    }

    /**
     * Test updating a company
     */
    public function testSave() {
        // Create test
        $ph_company = $this->phactory->create('companies');

        // Update test
        $this->company->id = $ph_company->company_id;
        $this->company->name = 'Mister Hoppers';
        $this->company->save();

        // Make sure it's in the database
        $ph_company = $this->phactory->get( 'companies', array( 'company_id' => $this->company->id ) );

        // Assert
        $this->assertEquals( $this->company->name, $ph_company->name );
    }

    /**
     * Test listing all companies
     */
    public function testListAll() {
        // Mock user
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('companies');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`domain`', '`date_created`' );
        $dt->search( array( '`name`' => true, '`domain`' => true ) );

        // Assert
        $companies = $this->company->list_all( $dt->get_variables() );
        $company = current( $companies );

        // Make sure we have an array
        $this->assertContainsOnlyInstancesOf( 'Company', $companies );
        $this->assertEquals( self::NAME, $company->name );

        // Get rid of everything
        unset( $user, $_GET, $dt, $companies, $account, $grey_suit_retail_exists );
    }

    /**
     * Test counting the companies
     */
    public function testCountAll() {
        // Mock user
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('companies');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( '`name`', '`domain`', '`date_created`' );
        $dt->search( array( '`name`' => true, '`domain`' => true ) );

        // Get
        $count = $this->company->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

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