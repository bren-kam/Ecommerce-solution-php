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
        // Fill up a user
        $user = new User();
        $user->role = 8;

        // Get the account
        $this->account->get( $user, 160 );

        $this->assertEquals( 160, $this->account->id );
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

        $accounts = $this->account->list_all( $user, $dt->get_variables() );

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
        $this->assertTrue ( $rent_king_exists );

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

        $accounts_count = $this->account->count_all( $user, $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 1, $accounts_count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $accounts_count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account = null;
    }
}
