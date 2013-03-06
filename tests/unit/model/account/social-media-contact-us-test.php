<?php

require_once 'base-database-test.php';

class SocialMediaContactUsTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaContactUs
     */
    private $sm_contact_us;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_contact_us = new SocialMediaContactUs();
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
        $this->sm_contact_us = null;
    }
}
