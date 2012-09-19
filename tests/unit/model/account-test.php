<?php

require_once 'base-database-test.php';

class AccountTest extends BaseDatabaseTest {
    /**
     * @var Account
     */
    private $account;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account = new Account();
    }

    /**
     * Test getting an account
     */
    public function testGet() {
        // Get the account
        $this->account->get( 160 );

        $this->assertEquals( 160, $this->account->id );
    }

    /**
     * Test getting acounts by a user
     */
    public function testGetByUser() {
        // Get the account
        $accounts = $this->account->get_by_user( 1 );

        $this->assertTrue( $accounts[0] instanceof Account );

        $testing_account_exists = false;

        foreach ( $accounts as $account ) {
            if ( 96 == $account->id ) {
                $testing_account_exists = true;
                break;
            }
        }

        $this->asserttrue( $testing_account_exists );
    }

    /**
     * Test creating an account
     *
     * @depends testGet
     */
    public function testCreate() {
        $this->account->title = 'Meridith Retail';
        $this->account->create();

        $this->assertTrue( !is_null( $this->account->id ) );

        // Make sure it's in the database
        $this->account->get( $this->account->id );

        $this->assertTrue( !is_null( $this->account->user_id ) );

        // Delete the account
        $this->db->delete( 'websites', array( 'website_id' => $this->account->id ), 'i' );
    }

    /**
     * Test updating an account
     *
     * @depends testCreate
     */
    public function testUpdate() {
        // Get test account
        $this->account->get(96);

        // Update test
        $this->account->title = 'Piglatin';
        $this->account->update();

        // Get title
        $title = $this->db->get_var( 'SELECT `title` FROM `websites` WHERE `website_id` = 96' );

        $this->assertEquals( 'Piglatin', $title );

        // Rename the account
        $this->db->update( 'websites', array( 'title' => 'Testing' ), array( 'website_id' => 96 ), 's', 'i' );
    }

    /**
     * Test listing all accounts
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
        $dt->order_by( 'b.`company_id`', 'a.`title`', 'b.`contact_name`', 'c.`contact_name`' );
        $dt->add_where( ' AND a.`status` = 1' );

        $accounts = $this->account->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( is_array( $accounts ) );

        // Rent King is an account with ID 160
        $rent_king_exists = false;

        if ( is_array( $accounts ) )
        foreach ( $accounts as $account ) {
            if ( 163 == $account->id ) {
                $rent_king_exists = true;
                break;
            }
        }

        // Make sure they exist
        $this->assertTrue( $rent_king_exists );

        // Get rid of everything
        unset( $user, $_GET, $dt, $accounts, $account, $rent_king_exists );
    }

    /**
     * Test counting the accounts
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
        $dt->order_by( 'b.`company_id`', 'a.`title`', 'b.`contact_name`', 'c.`contact_name`' );
        $dt->add_where( ' AND a.`status` = 1' );

        $accounts_count = $this->account->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $accounts_count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $accounts_count );
    }

    /**
     * Test getting one setting
     *
     * @depends testGet
     */
    public function testGetOneSetting() {
        // Get the account
        $this->account->get( 160 );

        $email_receipt = $this->account->get_settings( 'email-receipt' );

        $this->assertEquals( 'info@connells.com', $email_receipt );
    }

    /**
     * Test setting a setting
     */
    public function testSetSettings() {
        // Get the account
        $this->account->get( 160 );

        // Set it wrong in the first place
        $this->db->query( "INSERT INTO `website_settings` ( `website_id`, `key`, `value` ) VALUES ( 160, 'test-settings', '' ) ON DUPLICATE KEY UPDATE `value` = VALUES( `value` ) " );

        // Set it with the method
        $this->account->set_settings( array( 'test-settings' => '3.14159' ) );

        // Get the value
        $setting_value = $this->db->get_var( "SELECT `value` FROM `website_settings` WHERE `website_id` = 160 AND `key` = 'test-settings'" );

        // Make sure they equal each other
        $this->assertEquals( '3.14159', $setting_value );
    }

    /**
     * Test getting multiple setting
     *
     * @depends testGet
     */
    public function testGettingMultipleSettings() {
        // Get the account
        $this->account->get( 160 );

        $settings = $this->account->get_settings( 'ga-password', 'ga-username' );

        $this->assertTrue( is_array( $settings ) );
        $this->assertEquals( count( $settings ), 2 );
        $this->assertEquals( 'ODqMw5JF97ke9qGphfDiPsN/xOftzziAcv1ZEdM=', $settings['ga-username'] );
    }

    /**
     * Test getting multiple setting
     *
     * @depends testGet
     */
    public function testGettingMultipleSettingsFromArray() {
        // Get the account
        $this->account->get( 160 );

        $settings = $this->account->get_settings( 'ga-password', 'ga-username' );

        $this->assertTrue( is_array( $settings ) );
        $this->assertEquals( count( $settings ), 2 );
        $this->assertEquals( 'ODqMw5JF97ke9qGphfDiPsN/xOftzziAcv1ZEdM=', $settings['ga-username'] );
    }

    /**
     * Test getting industries by account
     *
     * @depends testGet
     */
    public function testGetIndustries() {
        // Get the testing account
        $this->account->get(96);

        // Get the industries
        $industries = $this->account->get_industries();

        // House Plans industry
        $this->assertTrue( in_array( 5, $industries ) );
    }

    /**
     * Delete Industries
     *
     * @depends testGet
     */
    public function testDeleteIndustries() {
        // Declare variables
        $account_id = 96;

        // Get test account
        $this->account->get( $account_id );

        // Delete
        $this->account->delete_industries();

        // See if we have industries right now
        $count_industries = $this->db->get_var( 'SELECT COUNT(`industry_id`) FROM `website_industries` WHERE `website_id` = 96' );

        $this->assertEquals( 0, $count_industries );
    }

    /**
     * Test Adding industries
     *
     * @depends testGet
     * @depends testDeleteIndustries
     */
    public function testAddIndustries() {
        // Get test account
        $this->account->get(96);

        $this->account->add_industries( array( 1, 2, 3, 4, 5, 6, 7, 8, 10, 11 ) );

        // See if we have industries right now
        $count_industries = $this->db->get_var( 'SELECT COUNT(`industry_id`) FROM `website_industries` WHERE `website_id` = 96' );

        $this->assertEquals( 10, $count_industries );
    }

    /**
     * Test Autocomplete as an Online Specialist
     */
    public function testAutocompleteA() {
        $user = new User();
        $user->role = 8;

        $accounts = $this->account->autocomplete( 'Connel', 'title', $user );

        $this->assertTrue( stristr( $accounts[0]['title'], 'Connel' ) !== false );
    }

    /**
     * Test Autocomplete as a Reseller
     */
    public function testAutocompleteB() {
        $user = new User();
        $user->role = 7;
        $user->company_id = 2;

        $accounts = $this->account->autocomplete( 'Connel', 'title', $user );

        $this->assertTrue( stristr( $accounts[0]['title'], 'Connel' ) !== false );
    }

    /**
     * Test Autocomplete with a change in status
     */
    public function testAutocompleteC() {
        $user = new User();
        $user->role = 8;

        $accounts = $this->account->autocomplete( 'Connel', 'title', $user, 1 );

        $this->assertTrue( stristr( $accounts[0]['title'], 'Connel' ) !== false );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account = null;
    }
}
