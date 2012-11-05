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
