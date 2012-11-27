<?php

require_once 'base-database-test.php';

class EmailListTest extends BaseDatabaseTest {
    /**
     * @var EmailList
     */
    private $email_list;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_list = new EmailList();
    }

    /**
     * Test Getting the Default Email List
     */
    public function testGetDefaultEmailList() {
        // Declare variables
        $account_id = -5;

        // Insert an email list
        $this->db->insert( 'email_lists', array( 'website_id' => $account_id, 'category_id' => 0 ), 'ii' );

        $email_list_id = $this->db->get_insert_id();

        $default_email_list = $this->email_list->get_default_email_list( $account_id );

        $this->assertEquals( $email_list_id, $default_email_list->id );

        // Delete
        $this->db->delete( 'email_lists', array( 'account_id' => $account_id ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->email_list->website_id = -3;
        $this->email_list->name = 'Test Default';
        $this->email_list->create();

        $this->assertTrue( !is_null( $this->email_list->id ) );

        // Make sure it's in the database
        $name = $this->db->get_var( 'SELECT `name` FROM `email_lists` WHERE `email_list_id` = ' . (int) $this->email_list->id );

        $this->assertEquals( 'Test Default', $name );

        // Delete
        $this->db->delete( 'email_lists', array( 'email_list_id' => $this->email_list->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_list = null;
    }
}
