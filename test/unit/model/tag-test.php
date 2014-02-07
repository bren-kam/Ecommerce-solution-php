<?php

require_once 'test/base-database-test.php';

class TagTest extends BaseDatabaseTest {
    const OBJECT_ID = 7;
    const TYPE = 'product';
    const VALUE = 'Nylon';

    /**
     * @var Tag
     */
    private $tag;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->tag = new Tag();

        // Define
        $this->phactory->define( 'tags', array( 'object_id' => self::OBJECT_ID, 'type' => self::TYPE, 'value' => self::VALUE ) );
        $this->phactory->recall();
    }

    /**
     * Test Adding several tags
     */
    public function testAddBulk() {
        // Add tags
        $this->tag->add_bulk( self::TYPE, self::OBJECT_ID, array( self::VALUE ) );

        // Get
        $ph_tag = $this->phactory->get( 'tags', array( 'object_id' => self::OBJECT_ID, 'type' => self::TYPE ) );

        // Assert
        $this->assertEquals( self::VALUE, $ph_tag->value );
    }

    /**
     * Get values by a type
     */
    public function testGetValueByType() {
        // Create
        $this->phactory->create('tags');

        // Get
        $tags = $this->tag->get_value_by_type( self::TYPE, self::OBJECT_ID );
        $expected_tags = array( self::VALUE );

        // Assert
        $this->assertEquals( $expected_tags, $tags );
    }

    /**
     * Delete by type
     */
    public function testDeleteByType() {
        // Create
        $this->phactory->create('tags');

        // Delete them
        $this->tag->delete_by_type( self::TYPE, self::OBJECT_ID );

        // Get
        $ph_tag = $this->phactory->get( 'tags', array( 'object_id' => self::OBJECT_ID, 'type' => self::TYPE ) );

        // Assert
        $this->assertNull( $ph_tag );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->tag = null;
    }
}
