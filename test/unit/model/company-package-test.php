<?php

require_once 'test/base-database-test.php';

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
        $website_id = -7;
        $email = md5(time()) . '@weet.com';
        $company_id = -1; // Imagine Retailer

        // Create website/user
        $user_id = $this->phactory->insert( 'users', compact( 'company_id', 'email' ), 'is' );
        $website_id = $this->phactory->insert( 'websites', compact( 'user_id' ), 'ii' );
        $this->phactory->insert( 'company_packages', compact( 'company_id', 'website_id' ), 'ii' );

        // Get all packages
        $packages = $this->company_package->get_all( $website_id );

        $this->assertTrue( current( $packages ) instanceof CompanyPackage );

        $this->phactory->delete( 'company_packages', compact( 'company_id' ), 'i' );
        $this->phactory->delete( 'websites', compact( 'user_id' ), 'i' );
        $this->phactory->delete( 'users', compact( 'company_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->company_package = null;
    }
}