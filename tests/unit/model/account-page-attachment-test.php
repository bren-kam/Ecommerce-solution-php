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
     * Test Get
     */
    public function testGet() {
        // Declare Variables
        $website_id = -5;
        $key = 'Hungry';

        // Insert id
        $website_page_id = $this->db->insert( 'website_pages', compact( 'website_id' ), 'i' );
        $website_attachment_id = $this->db->insert( 'website_attachments', compact( 'website_page_id', 'key' ), 'is' );

        // Get
        $this->account_page_attachment->get( $website_attachment_id, $website_id );

        $this->assertEquals( $key, $this->account_page_attachment->key );

        // Cleanup
        $this->db->delete( 'website_pages', compact( 'website_id' ), 'i' );
        $this->db->delete( 'website_attachments', compact( 'website_page_id' ), 'i' );
    }

    /**
     * Test Get By Key
     */
    public function testGetByKey() {
        // Declare Variables
        $website_page_id = -5;
        $key = 'Hungry';
        $value = 'Hippos';

        // Insert id
        $this->db->insert( 'website_attachments', compact( 'website_page_id', 'key', 'value' ), 'iss' );

        // Get
        $attachment = $this->account_page_attachment->get_by_key( $website_page_id, $key );

        $this->assertEquals( $value, $attachment->value );

        // Cleanup
        $this->db->delete( 'website_attachments', compact( 'website_page_id' ), 'i' );
    }

    /**
     * Test Get by account page ids
     */
    public function testGetByAccountPageIds() {
        // Declare variables
        $account_page_ids = array( -5 );

        // Insert attachment
        $this->db->insert( 'website_attachments', array( 'website_page_id' => -5 ), 'i' );

        // Get by account page ids
        $attachments = $this->account_page_attachment->get_by_account_page_ids( $account_page_ids );

        $this->assertTrue( current( $attachments ) instanceof AccountPageAttachment );

        // Delete
        $this->db->delete( 'website_attachments', array( 'website_page_id' => -5 ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->account_page_attachment->website_page_id = -3;
        $this->account_page_attachment->key = 'banner';
        $this->account_page_attachment->value = 'hedgehog.jpg';
        $this->account_page_attachment->extra = '';
        $this->account_page_attachment->meta = '';
        $this->account_page_attachment->sequence = 0;
        $this->account_page_attachment->create();

        // Make sure it's in the database
        $value = $this->db->get_var( 'SELECT `value` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $this->account_page_attachment->id );

        $this->assertEquals( $this->account_page_attachment->value, $value );

        // Delete the attribute
        $this->db->delete( 'website_attachments', array( 'website_attachment_id' => $this->account_page_attachment->id ), 'i' );
    }

    /**
     * Test Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare Variables
        $this->account_page_attachment->website_page_id = $website_page_id = -3;
        $this->account_page_attachment->key = 'banner';

        // Create
        $this->account_page_attachment->create();

        // Save
        $this->account_page_attachment->value = 'advertising';
        $this->account_page_attachment->save();

        // Make sure it's in the database
        $value = $this->db->get_var( 'SELECT `value` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $this->account_page_attachment->id );

        $this->assertEquals( $this->account_page_attachment->value, $value );

        // Delete the attribute
        $this->db->delete( 'website_attachments', compact( 'website_page_id' ), 'i' );
    }

   /**
     * Test Update Sequence
     */
    public function testUpdateSequence() {
        // Declare Variables
        $website_id = -5;
        $sequence = 5;

        // Insert id
        $website_page_id = $this->db->insert( 'website_pages', compact( 'website_id' ), 'i' );
        $website_attachment_id = $this->db->insert( 'website_attachments', compact( 'website_page_id' ), 'is' );

        $sequence_array = array( $sequence => $website_attachment_id );

        // Remove
        $this->account_page_attachment->update_sequence( $website_id, $sequence_array );

        // Make sure it's in the database
        $fetched_sequence = $this->db->get_var( 'SELECT `sequence` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $website_attachment_id );

        $this->assertEquals( $sequence, $fetched_sequence );

        // Cleanup
        $this->db->delete( 'website_pages', compact( 'website_id' ), 'i' );
        $this->db->delete( 'website_attachments', compact( 'website_page_id' ), 'i' );
    }

   /**
     * Test Remove
    *
    * @depends testCreate
     */
    public function testRemove() {
        // Declare Variables
        $this->account_page_attachment->value = 'Hippos';
        $this->account_page_attachment->website_page_id = $website_page_id = -3;

        // Create
        $this->account_page_attachment->create();

        // Remove
        $this->account_page_attachment->remove();

        // Make sure it's in the database
        $value = $this->db->get_var( 'SELECT `value` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $this->account_page_attachment->id );

        $this->assertFalse( $value );
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
        $this->db->query( "INSERT INTO `website_attachments` ( `website_page_id`, `key`, `value` ) VALUES (-1, 'video', 'google.mp4'), (-1, 'search', ''), (-1, 'email', '')" );

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
