<?php

require_once 'base-database-test.php';

class AccountPageAttachmentTest extends BaseDatabaseTest {
    const KEY = 'Hungry';
    const VALUE = 'Hippos';
    const WEBSITE_PAGE_ID = 3;
    const SEQUENCE = 5;

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
        $this->phactory->define( 'website_attachments', array( 'website_page_id' => self::WEBSITE_PAGE_ID, 'key' => self::KEY, 'value' => self::VALUE, 'sequence' => self::SEQUENCE ) );
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
     */
    public function testSave() {
        // Create
        $ph_website_attachment = $this->phactory->create( 'website_attachments' );

        // Save
        $this->account_page_attachment->id = $ph_website_attachment->website_attachment_id;
        $this->account_page_attachment->value = 'advertising';
        $this->account_page_attachment->save();

        // Make sure it's in the database
        $ph_website_attachment_after = $this->phactory->get( 'website_attachments', array( 'website_attachment_id' => $ph_website_attachment->website_attachment_id ) );

        $this->assertEquals( $this->account_page_attachment->value, $ph_website_attachment_after->value );
    }

   /**
    * Test Update Sequence
    */
    public function testUpdateSequence() {
        // Declare
        $sequence = 2;

        // Create
        $ph_website_page = $this->phactory->create( 'website_pages' );
        $ph_website_attachment = $this->phactory->create( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id ) );

        $sequence_array = array( $sequence => $ph_website_attachment->website_attachment_id );

        // Remove
        $this->account_page_attachment->update_sequence( self::WEBSITE_ID, $sequence_array );

        // Make sure it's in the database
        $ph_website_attachment_after = $this->phactory->get( 'website_attachments', array( 'website_attachment_id' => $ph_website_attachment->website_attachment_id ) );

        $this->assertEquals( $sequence, $ph_website_attachment_after->sequence );
    }

   /**
    * Test Remove
    *
    * @depends testCreate
    */
    public function testRemove() {
        // Create
        $ph_website_attachment = $this->phactory->create( 'website_attachments' );

        // Remove
        $this->account_page_attachment->id = $ph_website_attachment->website_attachment_id;
        $this->account_page_attachment->remove();

        // Make sure it's in the database
        $ph_website_attachment_after = $this->phactory->get( 'website_attachments', array( 'website_attachment_id' => $ph_website_attachment->website_attachment_id ) );

        $this->assertNull( $ph_website_attachment_after );
    }

    /**
     * Test delete unique attachments -- attachments that you can't have more than once
     */
    public function testDeleteUniqueAttachments() {
        // Create
        $ph_website_page = $this->phactory->create( 'website_pages' );
        $this->phactory->create( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id ) ); // Not Unique
        $this->phactory->create( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id, 'key' => 'email' ) ); // Unique
        $this->phactory->create( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id, 'key' => 'video' ) ); // Unique
        $this->phactory->create( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id, 'key' => 'search' ) ); // Unique

        // Yarr! Delete them!
        $this->account_page_attachment->delete_unique_attachments( array( $ph_website_page->website_page_id ) );

        // Shouldn't have anything left
        $ph_website_attachments = $this->phactory->getAll( 'website_attachments', array( 'website_page_id' => $ph_website_page->website_page_id ) );

        $this->assertCount( 1, $ph_website_attachments );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_page_attachment = null;
    }
}
