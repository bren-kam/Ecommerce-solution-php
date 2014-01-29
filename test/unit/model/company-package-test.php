<?php

require_once 'test/base-database-test.php';

class CompanyPackageTest extends BaseDatabaseTest {
    const COMPANY_ID = 3;
    const NAME = 'Refined Jewlery';

    /**
     * @var CompanyPackage
     */
    private $company_package;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->company_package = new CompanyPackage();

        // Define
        $this->phactory->define( 'company_packages', array( 'website_id' => self::WEBSITE_ID, 'company_id' => self::COMPANY_ID, 'name' => self::NAME ) );
        $this->phactory->define( 'users', array( 'company_id' => self::COMPANY_ID ) );
        $this->phactory->define( 'websites' );
        $this->phactory->recall();
    }

    /**
     * Test getting the company packages
     */
    public function testGet() {
        // Create
        $ph_company_package = $this->phactory->create('company_packages');

        // Get
        $this->company_package->get( $ph_company_package->company_package_id );

        // Assert
        $this->assertEquals( self::NAME, $this->company_package->name );
    }

    /**
     * Test getting the company packages
     */
    public function testGetAll() {
        // Create
        $ph_user = $this->phactory->create('users');
        $ph_website = $this->phactory->create( 'websites', array( 'user_id' => $ph_user->user_id ) );
        $this->phactory->create( 'company_packages', array( 'website_id' => $ph_website->website_id ) );

        // Get all packages
        $packages = $this->company_package->get_all( $ph_website->website_id );
        $package = current( $packages );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'CompanyPackage', $packages );
        $this->assertEquals( self::NAME, $package->name );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->company_package = null;
    }
}