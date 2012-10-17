<?php

require_once 'base-database-test.php';

class AccountPageTest extends BaseDatabaseTest {
    /**
     * @var AccountPage
     */
    private $account_page;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page = new AccountPage();
    }

    /**
     * Test Getting all attributes
     */
    public function testGetAll() {
        // Declare variables
        $account_id = 96; // Test account

        $account_pages = $this->account_page->get_all( $account_id );

        $this->assertTrue( is_array( $account_pages ) );
        $this->assertTrue( isset( $account_pages[0]['slug'] ) );
    }

    /**
     * Test creating an attribute
     */
    public function testCreate() {
        $this->account_page->website_id = -3;
        $this->account_page->slug = 'road-runner';
        $this->account_page->title = 'Road Runner';
        $this->account_page->content = 'Watch out for the super fast road runner!';
        $this->account_page->create();

        $this->assertTrue( !is_null( $this->account_page->id ) );

        // Make sure it's in the database
        $slug = $this->db->get_var( 'SELECT `slug` FROM `website_pages` WHERE `website_id` = ' . (int) $this->account_page->website_id );

        $this->assertEquals( $this->account_page->slug, $slug );

        // Delete the attribute
        $this->db->delete( 'website_pages', array( 'website_page_id' => $this->account_page->id ), 'i' );
    }

    /**
     * Test Copy by Account
     */
    public function testCopyByAccount() {
        // Declare variables
        $template_account_id = 96;
        $account_id = -5;

        // Delete before
        $this->db->delete( 'website_pages', array( 'website_id' => $account_id ) , 'i' );

        // Do the copying
        $this->account_page->copy_by_account( $template_account_id, $account_id );

        // Get account page ids
        $account_page_ids = $this->db->get_col( "SELECT `website_page_id` FROM `website_pages` WHERE `website_id` = $account_id" );

        $this->assertGreaterThan( 5, count( $account_page_ids ) );

        // Delete
        $this->db->delete( 'website_pages', array( 'website_id' => $account_id ) , 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_page = null;
    }
}
