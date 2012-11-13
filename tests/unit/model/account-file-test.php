<?php

require_once 'base-database-test.php';

class AccountFileTest extends BaseDatabaseTest {
    /**
     * @var AccountFile
     */
    private $account_file;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_file = new AccountFile();
    }

    /**
     * Test creating the note
     */
    public function testCreate() {
        $this->account_file->website_id = -5;
        $this->account_file->file_path = 'gobbledy-gook.jpg';
        $this->account_file->create();

        $this->assertTrue( !is_null( $this->account_file->id ) );

        // Get the message
        $file_path = $this->db->get_var( 'SELECT `file_path` FROM `website_files` WHERE `website_file_id` = ' . (int) $this->account_file->id );

        $this->assertEquals( $file_path, $this->account_file->file_path );

        // Delete
        $this->db->delete( 'website_files', array( 'website_file_id' => $this->account_file->id ), 'i' );
    }


    /**
     * Test Get By Account
     *
     * @depends testCreate
     */
    public function testGetByAccount() {
        // Declare variables
        $account_id = -5;

        // Create test file
        $this->account_file->website_id = $account_id;
        $this->account_file->file_path = 'gobbledy-gook.jpg';
        $this->account_file->create();

        // Get the files
        $account_files = $this->account_file->get_by_account( $account_id );

        $this->assertTrue( current( $account_files ) instanceof AccountFile );

        // Delete
        $this->db->delete( 'website_files', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_file = null;
    }
}
