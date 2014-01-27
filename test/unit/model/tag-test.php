<?php

require_once 'test/base-database-test.php';

class TagTest extends BaseDatabaseTest {
    /**
     * @var Tag
     */
    private $tag;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->tag = new Tag();
    }

    /**
     * Test Adding several tags
     */
    public function testAddBulk() {
        // Declare Variables
        $object_id = 1;
        $type = 'product';
        $tags = array( 'abc', 'def', 'ghi' );

        // Delete any preexisting tags
        $this->phactory->delete( 'tags', array( 'type' => $type, 'object_id' => $object_id ), 'si' );

        // Add tags
        $this->tag->add_bulk( $type, $object_id, $tags );

        // Check to see if they exist
        $fetched_tags = $this->phactory->get_col( "SELECT `value` FROM `tags` WHERE `type` = '$type' AND `object_id` = $object_id ORDER BY `value` ASC" );

        $this->assertEquals( $tags, $fetched_tags );

        // Delete tags
        $this->phactory->delete( 'tags', array( 'type' => $type, 'object_id' => $object_id ), 'si' );
    }

    /**
     * Get values by a type
     *
     * @depends testAddBulk
     */
    public function testGetValueByType() {
        // Declare Variables
        $type = 'product';
        $object_id = 1;
        $tags = array( 'knuckles', 'sonic' );

        // Add them
        $this->tag->add_bulk( $type, $object_id, $tags );

        // Get them
        $fetched_tags = $this->tag->get_value_by_type( $type, $object_id );

        $this->assertEquals( $tags, $fetched_tags );

        // Delete tags
        $this->phactory->delete( 'tags', array( 'type' => $type, 'object_id' => $object_id ), 'si' );
    }

    /**
     * Delete by type
     *
     * @depends testAddBulk
     * @depends testGetValueByType
     */
    public function testDeleteByType() {
        // Declare Variables
        $type = 'product';
        $object_id = 1;
        $tags = array( 'mario', 'luigi' );

        // Add tags
        $this->tag->add_bulk( $type, $object_id, $tags );

        // Delete them
        $this->tag->delete_by_type( $type, $object_id );

        // Fetch them
        $fetched_tags = $this->tag->get_value_by_type( $type, $object_id );

        // SHouldn't be any
        $this->assertTrue( 0 == count( $fetched_tags ) );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->tag = null;
    }
}
