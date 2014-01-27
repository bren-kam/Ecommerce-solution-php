<?php

require_once 'test/base-database-test.php';

class SocialMediaSweepstakesTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaSweepstakes
     */
    private $sm_sweepstakes;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_sweepstakes = new SocialMediaSweepstakes();
    }

    /**
     * Get
     */
    public function testGet() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $fb_page_id = -7;

        // Insert
        $this->phactory->insert( 'sm_sweepstakes', compact( 'sm_facebook_page_id', 'fb_page_id' ), 'ii' );

        // Get
        $this->sm_sweepstakes->get( $sm_facebook_page_id );

        $this->assertEquals( $fb_page_id, $this->sm_sweepstakes->fb_page_id );

        // Clean up
        $this->phactory->delete( 'sm_sweepstakes', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $key = 'Poke';

        // Create
        $this->sm_sweepstakes->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_sweepstakes->key = $key;
        $this->sm_sweepstakes->create();

        // Get
        $retrieved_key = $this->phactory->get_var( "SELECT `key` FROM `sm_sweepstakes` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_key, $this->sm_sweepstakes->key );

        // Clean up
        $this->phactory->delete( 'sm_sweepstakes', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Save
     *
     * @depends testCreate
     */
    public function testSave() {
        // Declare variables
        $sm_facebook_page_id = -5;
        $before = 'Poke';

        // Create
        $this->sm_sweepstakes->sm_facebook_page_id = $sm_facebook_page_id;
        $this->sm_sweepstakes->create();

        // Update test
        $this->sm_sweepstakes->before = $before;
        $this->sm_sweepstakes->save();

        // Now check it!
        $retrieved_before = $this->phactory->get_var( "SELECT `before` FROM `sm_sweepstakes` WHERE `sm_facebook_page_id` = $sm_facebook_page_id" );

        $this->assertEquals( $retrieved_before, $before );

        // Clean up
        $this->phactory->delete( 'sm_sweepstakes', compact( 'sm_facebook_page_id' ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_sweepstakes = null;
    }
}
