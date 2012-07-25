<?php

class UserTest extends BaseDatabaseTest {

    private $user;

    //Will be executed before every test
    public function setUp() {
        $this->user = new User();
    }

    public function testLogin() {
        $valid_email = '';
        $valid_pass = '';
        $result = $this->user->login( $valid_email, $valid_pass );

        $this->assertNotNull( $this->user->id );
        $this->assertTrue( $result );
    }

    //Will be executed after every test
    public function tearDown() {
        $this->user = null;
    }
}
