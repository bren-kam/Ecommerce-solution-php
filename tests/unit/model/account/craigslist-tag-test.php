<?php

require_once 'base-database-test.php';

class CraigslistTagTest extends BaseDatabaseTest {
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
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $craigslist_tag_id = -9;
        $object_id = -5;
        $type = 'category';

        // Create
        $this->craigslist_tag->craigslist_tag_id = $craigslist_tag_id;
        $this->craigslist_tag->object_id = $object_id;
        $this->craigslist_tag->type = $type;
        $this->craigslist_tag->create();

        // Make sure it's in the database
        $craigslist_tag = $this->phactory->get_row( 'SELECT * FROM `craigslist_tags` WHERE `craigslist_tag_id` = ' . (int) $this->craigslist_tag->id );

        $this->assertEquals( $craigslist_tag->object_id, $this->craigslist_tag->object_id );

        // Clean Up
        $this->phactory->delete( 'craigslist_tags', compact( 'object_id' ), 'i' );
    }

    /**
     * Test Get by All
     *
     * @depends testCreate
     */
    public function testGetByAll() {
        // Declare variables
        $craigslist_tag_id = -11;
        $craigslist_tag_id2 = -19;
        $object_id = -7;
        $type = 'category';
        $type2 = 'product';

        // Create
        $this->craigslist_tag->craigslist_tag_id = $craigslist_tag_id;
        $this->craigslist_tag->object_id = $object_id;
        $this->craigslist_tag->type = $type;
        $this->craigslist_tag->create();

        // Create one more
        $this->craigslist_tag->craigslist_tag_id = $craigslist_tag_id2;
        $this->craigslist_tag->type = $type2;
        $this->craigslist_tag->create();

        // Get by all
        $craigslist_tags = $this->craigslist_tag->get_by_all( $object_id, $object_id, $object_id );

        // We should have grabbed two
        $craigslist_tag_count = count( $craigslist_tags );
        $this->assertEquals( 2, $craigslist_tag_count );

        // Make sure it grabbed the right type
        $this->assertTrue( current( $craigslist_tags ) instanceof CraigslistTag );

        // Clean Up
        $this->phactory->delete( 'craigslist_tags', compact( 'object_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->craigslist_tag = null;
    }
}
