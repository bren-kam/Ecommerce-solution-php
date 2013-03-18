<?php

require_once 'base-database-test.php';

class TokenTest extends BaseDatabaseTest {
    /**
     * @var Token
     */
    private $token;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->token = new Token();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Declare variables
        $type = 'user-creation';
        
        // Create
        $this->token->type = $type;
        $this->token->create();

        // Make sure it's in the database
        $retrieved_type = $this->db->get_var( 'SELECT `token_type` FROM `tokens` WHERE `token_id` = ' . (int) $this->token->id );

        $this->assertEquals( $type, $retrieved_type );

        // Delete
        $this->db->delete( 'tokens', array( 'token_id' => $this->token->id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->token = null;
    }
}
