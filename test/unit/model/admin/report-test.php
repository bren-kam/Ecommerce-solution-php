<?php

require_once 'test/base-database-test.php';

class ReportTest extends BaseDatabaseTest {
    const TITLE = 'Winfrey Furniture';

    // Products
    const PRODUCT_NAME = 'Cecile 4x4 Chair';

    // Brands
    const BRAND_NAME = "Cecile's Chairs";

    // Users
    const CONTACT_NAME = 'Will Young';

    // Companies
    const COMPANY_NAME = 'Inventive Marketing';

    // Company Packages
    const COMPANY_PACKAGE_NAME = 'E-Commerce Solution';

    /**
     * @var Report
     */
    private $report;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->report = new Report();

        // Define
        $this->phactory->define( 'websites', array( 'title' => self::TITLE, 'status' => Account::STATUS_ACTIVE ) );
        $this->phactory->define( 'products', array( 'name' => self::PRODUCT_NAME ) );
        $this->phactory->define( 'brands', array( 'name' => self::BRAND_NAME ) );
        $this->phactory->define( 'website_products' );
        $this->phactory->define( 'users', array( 'contact_name' => self::CONTACT_NAME, 'role' => User::ROLE_ONLINE_SPECIALIST, 'status' => User::STATUS_ACTIVE ) );
        $this->phactory->define( 'companies', array( 'name' => self::COMPANY_NAME ) );
        $this->phactory->define( 'company_packages', array( 'name' => self::COMPANY_PACKAGE_NAME ) );
        $this->phactory->recall();
    }


    /**
     * Test search
     */
    public function testSearch() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $ph_brand = $this->phactory->create('brands');
        $ph_product = $this->phactory->create( 'products', array( 'brand_id' => $ph_brand->brand_id ) );
        $this->phactory->create( 'website_products', array( 'website_id' => $ph_website->website_id, 'product_id' => $ph_product->product_id ) );

        // Declare
        $where = ' AND ( p.`brand_id` IN( ' . $ph_brand->brand_id . ' ) )';

        // Get
        $accounts = $this->report->search( $where );

        // Assert
        $this->assertEquals( self::TITLE, $accounts[0]->title );
    }

    /**
     * Test Autocomplete for brands
     */
    public function testAutocompleteBrands() {
        // Create
        $this->phactory->create('brands');

        // Declare
        $query = substr( self::BRAND_NAME, 0 , 3 );

        // Get
        $entries = $this->report->autocomplete_brands( $query );

        // Assert
        $this->assertEquals( self::BRAND_NAME, $entries[0]['brand'] );
    }

    /**
     * Test Autocomplete for online specialists
     */
    public function testAutocompleteOnlineSpecialists() {
        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create( 'websites', array( 'os_user_id' => $ph_user->user_id ) );

        // Declare
        $query = substr( self::CONTACT_NAME, 0, 3 );

        // Get
        $entries = $this->report->autocomplete_online_specialists( $query );

        // Assert
        $this->assertEquals( self::CONTACT_NAME, $entries[0]['online_specialist'] );
    }

    /**
     * Test Autocomplete for Marketing Specialists
     */
    public function testAutocompleteMarketingSpecialists() {
        // Create
        $this->phactory->create( 'users', array( 'role' => User::ROLE_MARKETING_SPECIALIST ) );

        // Declare
        $query = substr( self::CONTACT_NAME, 0, 3 );

        // Get
        $entries = $this->report->autocomplete_marketing_specialists( $query );

        // Assert
        $this->assertEquals( self::CONTACT_NAME, $entries[0]['marketing_specialist'] );
    }

    /**
     * Test Autocomplete for Companies
     */
    public function testAutocompleteCompanies() {
        // Create
        $this->phactory->create('companies');

        // Declare
        $query = substr( self::COMPANY_NAME, 0, 3 );

        // Get
        $entries = $this->report->autocomplete_companies( $query );

        // Assert
        $this->assertEquals( self::COMPANY_NAME, $entries[0]['company'] );
    }

    /**
     * Test Autocomplete for Company Packages
     */
    public function testAutocompleteCompanyPackages() {
        // Create
        $this->phactory->create('company_packages');

        // Declare
        $query = substr( self::COMPANY_PACKAGE_NAME, 0, 3 );

        // Get
        $entries = $this->report->autocomplete_company_packages( $query );

        // Assert
        $this->assertEquals( self::COMPANY_PACKAGE_NAME, $entries[0]['package'] );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->report = null;
    }
}
