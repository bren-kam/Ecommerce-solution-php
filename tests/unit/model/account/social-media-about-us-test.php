<?php

require_once 'base-database-test.php';

class SocialMediaAboutUsTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaAboutUs
     */
    private $sm_about_us;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_about_us = new SocialMediaAboutUs();
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
        $this->sm_about_us = null;
    }
}
