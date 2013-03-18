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
     * Get
     */
    public function testGet() {
        // Set variables
        $website_id = -7;
        $file_path = 'http://[domain]/testing.gif';
        $domain = 'www.google.com';

        // Create
        $website_file_id = $this->db->insert( 'website_files', compact( 'website_id', 'file_path' ), 'is' );

        // Get
        $this->account_file->get( $website_file_id, $domain, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( str_replace( '[domain]', $domain, $file_path ), $this->account_file->file_path );

        // Clean up
        $this->db->delete( 'website_files', compact( 'website_id' ), 'i' );
    }

    /**
     * Get By File Path
     */
    public function testGetByFilePath() {
        // Set variables
        $website_id = -7;
        $file_path = 'http://[domain]/testing.gif';
        $domain = 'www.google.com';

        // Create
        $this->db->insert( 'website_files', compact( 'website_id', 'file_path' ), 'is' );

        // Get
        $this->account_file->get_by_file_path( $file_path, $domain, $website_id );

        // Make sure we grabbed the right one
        $this->assertEquals( str_replace( '[domain]', $domain, $file_path ), $this->account_file->file_path );

        // Clean up
        $this->db->delete( 'website_files', compact( 'website_id' ), 'i' );
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
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Set variables
        $website_id = -7;
        $file_path = 'http://[domain]/testing.gif';
        $domain = 'www.google.com';

        // Create
        $website_file_id = $this->db->insert( 'website_files', compact( 'website_id', 'file_path' ), 'is' );

        // Get
        $this->account_file->get( $website_file_id, $domain, $website_id );

        // Remove/Delete
        $this->account_file->remove();

        $retrieved_file_path = $this->db->get_var( "SELECT `file_path` FROM `website_files` WHERE `website_file_id` = $website_file_id" );

        $this->assertFalse( $retrieved_file_path );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_file = null;
    }
}
