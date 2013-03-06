<?php

require_once 'base-database-test.php';

class SocialMediaFacebookSiteTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaFacebookSite
     */
    private $sm_facebook_site;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_facebook_site = new SocialMediaFacebookSite();
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
        $this->sm_facebook_site = null;
    }
}
