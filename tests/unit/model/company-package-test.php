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
        $this->company_package = new CompanyPackage();
    }

    /**
     * Test getting the company packages
     */
    public function testGetAll() {
        // WHFA
        $packages = $this->company_package->get_all( 248 );

        $this->assertTrue( $packages[0] instanceof CompanyPackage );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->company_package = null;
    }
}