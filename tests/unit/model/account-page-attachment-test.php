<?php

require_once 'base-database-test.php';

class AccountPageAttachmentTest extends BaseDatabaseTest {
    const KEY = 'Hungry';
    const VALUE = 'Hippos';
    const WEBSITE_PAGE_ID = 3;

    /**
     * @var AccountPageAttachment
     */
    private $account_page_attachment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page_attachment = new AccountPageAttachment();

        // Define
        $this->phactory->define( 'website_pages', array( 'website_id' => self::WEBSITE_ID ) );
        $this->phactory->define( 'website_attachments', array( 'website_page_id' => self::WEBSITE_PAGE_ID, 'key' => self::KEY, 'value' => self::VALUE ) );
        $this->phactory->recall();
    }

    /**
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_website_page = $this->phactory->create( 'website_pages' );
        $ph_website_attachment = $this->phactory->create( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id ) );

        // Get
        $this->account_page_attachment->get( $ph_website_attachment->website_attachment_id, self::WEBSITE_ID );

        $this->assertEquals( self::KEY, $this->account_page_attachment->key );
    }

    /**
     * Test Get By Key
     */
    public function testGetByKey() {
        // Create
        $this->phactory->create( 'website_attachments' );

        // Get
        $attachment = $this->account_page_attachment->get_by_key( self::WEBSITE_PAGE_ID, self::KEY );

        $this->assertEquals( self::VALUE, $attachment->value );
    }

    /**
     * Test Get by account page ids
     */
    public function testGetByAccountPageIds() {
        // Create
        $this->phactory->create( 'website_attachments' );

        // Get by account page ids
        $attachments = $this->account_page_attachment->get_by_account_page_ids( array( self::WEBSITE_PAGE_ID ) );
        $attachment = current( $attachments );

        $this->assertContainsOnlyInstancesOf( 'AccountPageAttachment', $attachments );
        $this->assertEquals( self::KEY, $attachment->key );
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->account_page_attachment->website_page_id = self::WEBSITE_PAGE_ID;
        $this->account_page_attachment->key = self::KEY;
        $this->account_page_attachment->value = self::VALUE;
        $this->account_page_attachment->create();

        // Make sure it's in the database
        $ph_account_page_attachment = $this->phactory->get( 'website_attachments', array( 'website_attachment_id' => $this->account_page_attachment->id ) );

        $this->assertEquals( self::VALUE, $ph_account_page_attachment->value );
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
        $value = $this->phactory->get_var( 'SELECT `value` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $this->account_page_attachment->id );

        $this->assertEquals( $this->account_page_attachment->value, $value );

        // Delete the attribute
        $this->phactory->delete( 'website_attachments', compact( 'website_page_id' ), 'i' );
    }

   /**
     * Test Update Sequence
     */
    public function testUpdateSequence() {
        // Declare Variables
        $website_id = -5;
        $sequence = 5;

        // Insert id
        $website_page_id = $this->phactory->insert( 'website_pages', compact( 'website_id' ), 'i' );
        $website_attachment_id = $this->phactory->insert( 'website_attachments', compact( 'website_page_id' ), 'is' );

        $sequence_array = array( $sequence => $website_attachment_id );

        // Remove
        $this->account_page_attachment->update_sequence( $website_id, $sequence_array );

        // Make sure it's in the database
        $fetched_sequence = $this->phactory->get_var( 'SELECT `sequence` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $website_attachment_id );

        $this->assertEquals( $sequence, $fetched_sequence );

        // Cleanup
        $this->phactory->delete( 'website_pages', compact( 'website_id' ), 'i' );
        $this->phactory->delete( 'website_attachments', compact( 'website_page_id' ), 'i' );
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
        $value = $this->phactory->get_var( 'SELECT `value` FROM `website_attachments` WHERE `website_attachment_id` = ' . (int) $this->account_page_attachment->id );

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
        $this->phactory->query( "INSERT INTO `website_attachments` ( `website_page_id`, `key`, `value` ) VALUES (-1, 'video', 'google.mp4'), (-1, 'search', ''), (-1, 'email', '')" );

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
