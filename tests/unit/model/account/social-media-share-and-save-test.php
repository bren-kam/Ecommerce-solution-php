<?php

require_once 'base-database-test.php';

class SocialMediaShareAndSaveTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaShareAndSave
     */
    private $sm_share_and_save;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_share_and_save = new SocialMediaShareAndSave();
    }

    /**
     * Test
     */
    public function testReplace() {
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->sm_share_and_save = null;
    }
}
