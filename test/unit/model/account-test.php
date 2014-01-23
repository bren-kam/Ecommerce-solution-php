<?php

require_once 'test/base-database-test.php';

class AccountTest extends BaseDatabaseTest {
    const TITLE = 'Phantom Furniture';
    const USER_ID = 1;

    // Website Products
    const PRODUCT_ID = 25;

    // Website settings
    const WEBSITE_SETTINGS_KEY = 'silver-bells';
    const WEBSITE_SETTINGS_VALUE = 'chime through the night';

    // Website Industry
    const INDUSTRY_ID = 45;

    /**
     * @var Account
     */
    private $account;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account = new Account();

        // Define
        $this->phactory->define( 'websites', array( 'user_id' => self::USER_ID, 'title' => self::TITLE, 'status' => Account::STATUS_ACTIVE ) );
        $this->phactory->define( 'auth_user_websites', array( 'website_id' => self::WEBSITE_ID, 'user_id' => self::USER_ID ) );
        $this->phactory->define( 'website_products', array( 'website_id' => self::WEBSITE_ID, 'product_id' => self::PRODUCT_ID ) );
        $this->phactory->define( 'website_settings', array( 'website_id' => self::WEBSITE_ID, 'key' => self::WEBSITE_SETTINGS_KEY, 'value' => self::WEBSITE_SETTINGS_VALUE ) );
        $this->phactory->define( 'website_industries', array( 'website_id' => self::WEBSITE_ID, 'industry_id' => self::INDUSTRY_ID ) );
        $this->phactory->define( 'users', array( 'role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE ) );
        $this->phactory->recall();
    }

    /**
     * Test getting an account
     */
    public function testGet() {
        // Create
        $ph_website = $this->phactory->create('websites');

        // Get the account
        $this->account->get( $ph_website->website_id );

        $this->assertEquals( self::TITLE, $this->account->title );
    }

    /**
     * Test getting accounts by a user
     */
    public function testGetByUser() {
        // Create
        $this->phactory->create('websites');

        // Get the account
        $accounts = $this->account->get_by_user( self::USER_ID );
        $account = current( $accounts );

        $this->assertContainsOnlyInstancesOf( 'Account', $accounts );
        $this->assertEquals( self::TITLE, $account->title );
    }

    /**
     * Test getting accounts by a user
     */
    public function testGetByAuthorizedUser() {
        // Reset
        $this->phactory->recall();

        // Create website/product
        $ph_website = $this->phactory->create('websites');
        $this->phactory->create( 'auth_user_websites', array( 'website_id' => $ph_website->website_id ) );

        // Get the accounts
        $accounts = $this->account->get_by_authorized_user( self::USER_ID );
        $account = current( $accounts );

        $this->assertContainsOnlyInstancesOf( 'Account', $accounts );
        $this->assertEquals( self::TITLE, $account->title );
    }

    /**
     * Test getting accounts by a product
     */
    public function testGetByProduct() {
        // Create
        $ph_website = $this->phactory->create('websites');
        $this->phactory->create( 'website_products', array( 'website_id' => $ph_website->website_id ) );

        // Get the account
        $accounts = $this->account->get_by_product( self::PRODUCT_ID );
        $account = current( $accounts );

        $this->assertContainsOnlyInstancesOf( 'Account', $accounts );
        $this->assertEquals( self::TITLE, $account->title );
    }

    /**
     * Test creating an account
     */
    public function testCreate() {
        // Create
        $this->account->title = self::TITLE;
        $this->account->create();

        $this->assertTrue( !is_null( $this->account->id ) );

        // Make sure it's in the database
        $ph_website = $this->phactory->get( 'websites', array( 'website_id' => $this->account->id ) );

        $this->assertEquals( self::TITLE, $ph_website->title );
    }

    /**
     * Test updating an account
     */
    public function testUpdate() {
        // Create
        $ph_website = $this->phactory->create('websites');

        // Update test
        $this->account->id = $ph_website->website_id;
        $this->account->title = 'Piglatin';
        $this->account->save();

        // Make sure it's in the database
        $ph_website = $this->phactory->get( 'websites', array( 'website_id' => $this->account->id ) );

        $this->assertEquals( $this->account->title, $ph_website->title );
    }

    /**
     * Test listing all accounts
     */
    public function testListAll() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('websites');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'b.`company_id`', 'a.`title`', 'b.`contact_name`', 'c.`contact_name`' );
        $dt->add_where( ' AND a.`status` = 1' );

        $accounts = $this->account->list_all( $dt->get_variables() );
        $account = current( $accounts );

        $this->assertContainsOnlyInstancesOf( 'Account', $accounts );
        $this->assertEquals( self::TITLE, $account->title );

        // Get rid of everything
        unset( $user, $_GET, $dt, $accounts, $account, $rent_king_exists );
    }

    /**
     * Test counting the accounts
     */
    public function testCountAll() {
        // Get mock
        $stub_user = $this->getMock('User');

        // Create
        $this->phactory->create('websites');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $stub_user );
        $dt->order_by( 'b.`company_id`', 'a.`title`', 'b.`contact_name`', 'c.`contact_name`' );
        $dt->add_where( ' AND a.`status` = 1' );

        $count = $this->account->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $accounts_count );
    }

    /**
     * Test get settings array
     */
    public function testGetSettingsArray() {
        // Make it possible to call this function
        $class = new ReflectionClass('account');
        $method = $class->getMethod( 'get_settings_array' );
        $method->setAccessible(true);

        // Create
        $this->phactory->create('website_settings');

        // remove all discontinued products
        $this->account->id = self::WEBSITE_ID;
        $website_settings = $method->invokeArgs( $this->account, array( array( self::WEBSITE_SETTINGS_KEY ) ) );
        $expected_settings = array( array( 'key' => self::WEBSITE_SETTINGS_KEY, 'value' => self::WEBSITE_SETTINGS_VALUE ) );

        $this->assertEquals( $expected_settings, $website_settings );
    }

    /**
     * Test get setting
     */
    public function testGetSetting() {
        // Make it possible to call this function
        $class = new ReflectionClass('account');
        $method = $class->getMethod( 'get_setting' );
        $method->setAccessible(true);

        // Create
        $this->phactory->create('website_settings');

        // remove all discontinued products
        $this->account->id = self::WEBSITE_ID;
        $website_settings_value = $method->invokeArgs( $this->account, array( self::WEBSITE_SETTINGS_KEY ) );

        $this->assertEquals( self::WEBSITE_SETTINGS_VALUE, $website_settings_value );
    }

    /**
     * Test get settings
     *
     * @depends testGetSettingsArray
     * @depends testGetSetting
     */
    public function testGetSettings() {
        // declare
        $key = 'monty python';
        $value = 'holy hand grenade';

        // Create
        $this->phactory->create('website_settings');
        $this->phactory->create( 'website_settings', compact( 'key', 'value' ) );

        // Get the account
        $this->account->id = self::WEBSITE_ID;
        $website_settings_value = $this->account->get_settings( self::WEBSITE_SETTINGS_KEY );

        $this->assertEquals( self::WEBSITE_SETTINGS_VALUE, $website_settings_value );

        // Get multiple values
        $website_settings = $this->account->get_settings( self::WEBSITE_SETTINGS_KEY, $key );
        $expected_settings = array( self::WEBSITE_SETTINGS_KEY => self::WEBSITE_SETTINGS_VALUE, $key => $value );

        $this->assertEquals( $expected_settings, $website_settings );
    }

    /**
     * Test setting a setting
     */
    public function testSetSettings() {
        // Reset
        $this->phactory->recall();

        // Declare
        $new_value = '3.14159265';

        // Set it with the method
        $this->account->id = self::WEBSITE_ID;
        $this->account->set_settings( array( self::WEBSITE_SETTINGS_KEY => self::WEBSITE_SETTINGS_VALUE ) );

        // Get the value
        $ph_website_setting = $this->phactory->get( 'website_settings', array( 'website_id' => self::WEBSITE_ID, 'key' => self::WEBSITE_SETTINGS_KEY ) );

        $this->assertEquals( self::WEBSITE_SETTINGS_VALUE, $ph_website_setting->value );

        // Set the method again over the top of itself
        $this->account->set_settings( array( self::WEBSITE_SETTINGS_KEY => $new_value ) );

        // Get the value
        $ph_website_setting = $this->phactory->get( 'website_settings', array( 'website_id' => self::WEBSITE_ID, 'key' => self::WEBSITE_SETTINGS_KEY ) );

        $this->assertEquals( $new_value, $ph_website_setting->value );
    }

    /**
     * Test getting industries by account
     */
    public function testGetIndustries() {
        // Create
        $this->phactory->create( 'website_industries' );

        // Get the industries
        $this->account->id = self::WEBSITE_ID;
        $industries = $this->account->get_industries();
        $expected_array = array( self::INDUSTRY_ID );

        $this->assertEquals( $expected_array, $industries );
    }

    /**
     * Test copying industries
     *
     * @depends testGetIndustries
     */
    public function testCopyIndustriesByAccount() {
        // Declare
        $new_website_id = 55;

        // Create
        $this->phactory->create( 'website_industries' );

        // Copy industries
        $this->account->copy_industries_by_account( self::WEBSITE_ID, $new_website_id );

        // Get industry
        $ph_website_industry = $this->phactory->get( 'website_industries', array( 'website_id' => $new_website_id ) );

        // Get industries and see if they match
        $this->assertEquals( self::INDUSTRY_ID, $ph_website_industry->industry_id );
    }

    /**
     * Delete Industries
     */
    public function testDeleteIndustries() {
        // Create
        $this->phactory->create( 'website_industries' );

        // Delete
        $this->account->id = self::WEBSITE_ID;
        $this->account->delete_industries();

        // Get industry
        $ph_website_industry = $this->phactory->get( 'website_industries', array( 'website_id' => self::WEBSITE_ID ) );

        $this->assertNull( $ph_website_industry );
    }

    /**
     * Test Adding industries
     */
    public function testAddIndustries() {
        // Reset
        $this->phactory->recall();

        // Add
        $this->account->id = self::WEBSITE_ID;
        $this->account->add_industries( array( self::INDUSTRY_ID ) );

        // Get industry
        $ph_website_industry = $this->phactory->get( 'website_industries', array( 'website_id' => self::WEBSITE_ID ) );

        $this->assertEquals( self::INDUSTRY_ID, $ph_website_industry->industry_id );
    }

    /**
     * Test Autocomplete as an Online Specialist
     */
    public function testAutocompleteA() {
        // Declare
        $query = 'Phantom';
        $field = 'title';
        $stub_user = $this->getMock('User');
        // Create
        $ph_user = $this->phactory->create('users');
        $this->phactory->create('websites', array( 'user_id' => $ph_user->user_id ) );

        $accounts = $this->account->autocomplete( $query, $field, $user );

        $this->assertTrue( stristr( $accounts[0]['title'], 'Connel' ) !== false );
    }
//
//    /**
//     * Test Autocomplete as a Reseller
//     */
//    public function testAutocompleteB() {
//        $user = new User();
//        $user->role = 7;
//        $user->company_id = 2;
//
//        $accounts = $this->account->autocomplete( 'Connel', 'title', $user );
//
//        $this->assertTrue( stristr( $accounts[0]['title'], 'Connel' ) !== false );
//    }
//
//    /**
//     * Test Autocomplete with a change in status
//     */
//    public function testAutocompleteC() {
//        $user = new User();
//        $user->role = 8;
//
//        $accounts = $this->account->autocomplete( 'Connel', 'title', $user, 1 );
//
//        $this->assertTrue( stristr( $accounts[0]['title'], 'Connel' ) !== false );
//    }
//
//    /**
//     * Test Copy Top Brands by Account
//     */
//    public function testCopyTopBrandsByAccount() {
//        // Declare variables
//        $template_account_id = 96;
//        $account_id = -5;
//        $brand_id = 151;
//
//        // Do the copying
//        $this->account->copy_top_brands_by_account( $template_account_id, $account_id );
//
//        // Get brand ids
//        $brand_ids = $this->phactory->get_col( "SELECT `brand_id` FROM `website_top_brands` WHERE `website_id` = $account_id" );
//
//        $this->assertGreaterThan( 1, count( $brand_ids ) );
//
//        // Delete
//        $this->phactory->delete( 'website_top_brands', array( 'website_id' => $account_id ) , 'i' );
//    }
//
//    /**
//     * Test Copy settings by Account
//     */
//    public function testCopySettingsByAccount() {
//        // Declare variables
//        $template_account_id = 96;
//        $account_id = -5;
//        $settings = array( 'banner-background-color', 'banner-effect', 'banner-height', 'banner-loading-color', 'banner-speed', 'banner-width' );
//
//        // Do the copying
//        $this->account->copy_settings_by_account( $template_account_id, $account_id, $settings );
//
//        // Get brand ids
//        $copied_settings = ar::assign_key( $this->phactory->get_results( "SELECT `key`, `value` FROM `website_settings` WHERE `website_id` = $account_id ORDER BY `key`", PDO::FETCH_ASSOC ), 'key', true );
//
//        $this->assertEquals( array_keys( $copied_settings ), $settings );
//        $this->assertEquals( '4C86B0', $copied_settings['banner-loading-color'] );
//
//        // Delete
//        $this->phactory->delete( 'website_settings', array( 'website_id' => $account_id ) , 'i' );
//    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account = null;
    }
}
