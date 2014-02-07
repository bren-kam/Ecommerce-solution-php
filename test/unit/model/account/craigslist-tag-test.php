<?php

require_once 'test/base-database-test.php';

class CraigslistTagTest extends BaseDatabaseTest {
    const CRAIGSLIST_TAG_ID = 7;
    const OBJECT_ID = 3;
    const TYPE = 'category';

    /**
     * @var CraigslistTag
     */
    private $craigslist_tag;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->craigslist_tag = new CraigslistTag();

        // Define
        $this->phactory->define( 'craigslist_tags', array( 'craigslist_tag_id' => self::CRAIGSLIST_TAG_ID, 'object_id' => self::OBJECT_ID, 'type' => self::TYPE ) );
        $this->phactory->recall();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->craigslist_tag->craigslist_tag_id = self::CRAIGSLIST_TAG_ID;
        $this->craigslist_tag->type = self::TYPE;
        $this->craigslist_tag->create();

        // Get
        $ph_craigslist_tag = $this->phactory->get( 'craigslist_tags', array( 'craigslist_tag_id' => self::CRAIGSLIST_TAG_ID ) );

        // Assert
        $this->assertEquals( self::TYPE, $ph_craigslist_tag->type );
    }

    /**
     * Test Get by All
     */
    public function testGetByAll() {
        // Create
        $this->phactory->create('craigslist_tags');

        // Get
        $craigslist_tags = $this->craigslist_tag->get_by_all( self::OBJECT_ID, self::OBJECT_ID, self::OBJECT_ID );
        $craigslist_tag = current( $craigslist_tags );

        // Assert
        $this->assertContainsOnlyInstancesOf( 'CraigslistTag', $craigslist_tags );
        $this->assertEquals( self::TYPE, $craigslist_tag->type );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->craigslist_tag = null;
    }
}
