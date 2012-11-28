<?php

require_once 'base-database-test.php';

class CompanyPackageTest extends BaseDatabaseTest {
    /**
     * @var CompanyPackage
     */
    private $company_package;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->company_package = new CompanyPackage();
    }

    /**
     * Test getting the company packages
     */
    public function testGet() {
        // Declare variables
        $company_package_id = 1;

        $this->company_package->get( $company_package_id );

        $this->assertEquals( $this->company_package->name, 'Furnish123 Gallery' );
    }

    /**
     * Test getting the company packages
     */
    public function testGetAll() {
        // Declare Variables
        $account_id = 248; // WHFA

        // Get all packages
        $packages = $this->company_package->get_all( $account_id );

        $this->assertTrue( $packages[0] instanceof CompanyPackage );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->company_package = null;
    }
}