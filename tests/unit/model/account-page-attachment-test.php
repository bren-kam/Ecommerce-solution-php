<?php

require_once 'base-database-test.php';

class AccountPageAttachmentTest extends BaseDatabaseTest {
    /**
     * @var AccountPageAttachment
     */
    private $account_page_attachment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page_attachment = new AccountPageAttachment();
    }

    /**
     * Test Get by account page ids
     */
    public function testGetByAccountPageIds() {
        // Declare variables
        $account_page_ids = array( 5, 10 );

        // Get by account page ids
        $attachments = $this->account_page_attachment->get_by_account_page_ids( $account_page_ids );

        $this->assertTrue( current( $attachments ) instanceof AccountPageAttachment );
        $this->assertEquals( 8, count( $attachments ) );
    }

    /**
     * Test creating an attribute
     */
    public function testCreate() {
        $this->account_page_attachment->website_page_id = -3;
        $this->account_page_attachment->key = 'banner';
        $this->account_page_attachment->value = 'hedgehog.jpg';
        $this->account_page_attachment->extra = '';
        $this->account_page_attachment->meta = '';
        $this->account_page_attachment->sequence = 0;
        $this->account_page_attachment->create();

        $this->assertTrue( !is_null( $this->account_page_attachment->id ) );

        // Make sure it's in the database
        $value = $this->db->get_var( 'SELECT `value` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $this->account_page_attachment->id );

        $this->assertEquals( $this->account_page_attachment->value, $value );

        // Delete the attribute
        $this->db->delete( 'website_attachments', array( 'website_attachment_id' => $this->account_page_attachment->id ), 'i' );
    }

    /**
     * Test delete unique attachments -- attachments that you can't have more than once
     *
     * @depends testGetByAccountPageIds
     */
    public function testDeleteUniqueAttachments() {
        // Declare variables
        $account_page_ids = array( -1 );

        // Insert a few
        $this->db->query( "INSERT INTO `website_attachments` ( `website_page_id`, `key`, `value` ) VALUES (-1, 'video', 'google.flv'), (-1, 'search', ''), (-1, 'email', '')" );

        // Yarr! Delete them!
        $this->account_page_attachment->delete_unique_attachments( $account_page_ids );

        // Shouldn't have anything left
        $attachments = $this->account_page_attachment->get_by_account_page_ids( $account_page_ids );

        $this->assertEquals( 0, count( $attachments ) );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_page_attachment = null;
    }
}
