<?php

require_once 'base-database-test.php';

class AccountFileTest extends BaseDatabaseTest {
    const FILE_PATH = 'http://[domain]/testing.gif';
    const DOMAIN = 'www.google.com';

    /**
     * @var AccountFile
     */
    private $account_file;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_file = new AccountFile();

        $this->phactory->define( 'website_files', array( 'website_id' => self::WEBSITE_ID, 'file_path' => self::FILE_PATH ) );
        $this->phactory->recall();
    }
    
    /**
     * Get
     */
    public function testGet() {
        // Create
        $ph_website_file = $this->phactory->create( 'website_files' );

        // Get
        $this->account_file->get( $ph_website_file->website_file_id, self::DOMAIN, self::WEBSITE_ID );

        // Make sure we grabbed the right one
        $expected = str_replace( '[domain]', self::DOMAIN, self::FILE_PATH );
        $this->assertEquals( $expected, $this->account_file->file_path );
    }

    /**
     * Get By File Path
     */
    public function testGetByFilePath() {
        // Create
        $this->phactory->create( 'website_files' );

        // Get
        $this->account_file->get_by_file_path( self::FILE_PATH, self::DOMAIN, self::WEBSITE_ID );

        // Make sure we grabbed the right one
        $expected = str_replace( '[domain]', self::DOMAIN, self::FILE_PATH );
        $this->assertEquals( $expected, $this->account_file->file_path );
    }

    /**
     * Test creating the note
     */
    public function testCreate() {
        // Create
        $this->account_file->website_id = self::WEBSITE_ID;
        $this->account_file->file_path = self::FILE_PATH;
        $this->account_file->create();

        $this->assertTrue( !is_null( $this->account_file->id ) );

        // Get the message
        $ph_account_file = $this->phactory->get( 'website_files', array( 'website_file_id' => $this->account_file->id ) );

        $this->assertEquals( self::FILE_PATH, $ph_account_file->file_path );
    }


    /**
     * Test Get By Account
     */
    public function testGetByAccount() {
        // Create
        $this->phactory->create( 'website_files' );

        // Get the files
        $account_files = $this->account_file->get_by_account( self::WEBSITE_ID );
        $account_file = current( $account_files );

        $this->assertContainsOnlyInstancesOf( 'AccountFile', $account_files );
        $this->assertEquals( self::FILE_PATH, $account_file->file_path );
    }

    /**
     * Remove
     *
     * @depends testGet
     */
    public function testRemove() {
        // Create
        $ph_website_file = $this->phactory->create( 'website_files' );

        // Remove/Delete
        $this->account_file->id = $ph_website_file->website_file_id;
        $this->account_file->remove();

        // Get
        $ph_account_file = $this->phactory->get( 'website_files', array( 'website_file_id' => $ph_website_file->website_file_id ) );

        $this->assertNull( $ph_account_file );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_file = null;
    }
}
