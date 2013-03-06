<?php

require_once 'base-database-test.php';

class SocialMediaEmailSignUpTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaEmailSignUp
     */
    private $sm_email_sign_up;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_email_sign_up = new SocialMediaEmailSignUp();
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
        $this->sm_email_sign_up = null;
    }
}
