<?php

require_once 'test/base-database-test.php';

class TokenTest extends BaseDatabaseTest {
    const TYPE = 'user-creation';

    /**
     * @var Token
     */
    private $token;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->token = new Token();

        // Define
        $this->phactory->define( 'tokens', array( 'token_type' => self::TYPE ) );
        $this->phactory->recall();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->token->type = self::TYPE;
        $this->token->create();

        // Assert
        $this->assertNotNull( $this->token->id );

        // Get
        $ph_token = $this->phactory->get( 'tokens', array( 'token_id' => $this->token->id ) );

        // Assert
        $this->assertEquals( self::TYPE, $ph_token->token_type );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->token = null;
    }
}
