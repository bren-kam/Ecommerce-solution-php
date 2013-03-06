<?php

require_once 'base-database-test.php';

class SocialMediaFanOfferTest extends BaseDatabaseTest {
    /**
     * @var SocialMediaFanOffer
     */
    private $sm_fan_offer;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->sm_fan_offer = new SocialMediaFanOffer();
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
        $this->sm_fan_offer = null;
    }
}
