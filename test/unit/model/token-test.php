<?php

require_once 'test/base-database-test.php';

class TokenTest extends BaseDatabaseTest {
    const TYPE = 'user-creation';
    
    const KEY = 'some-token-key';

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
        $this->phactory->define( 'tokens', array( 'token_type' => self::TYPE, 'key' => self::KEY, 'user_id' => self::WEBSITE_ID ) );
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
     * Test Get
     */
    public function testGet() {
        // Create
        $ph_token = $this->phactory->create( 'tokens' );

        // Get
        $this->token->get( $ph_token->key );

        // Assert
        $this->assertEquals( $this->token->user_id, self::WEBSITE_ID );
    }
    
    /** 
     * Test Remove
     */
    public function testRemove() {
        // Create
        $ph_token = $this->phactory->create( 'tokens' );
        
        // Get
        $this->token->get( $ph_token->key );
        
        // Remove
        $this->token->remove();
        
        // Count
        $tokens = $this->phactory->getAll( 'tokens', array( 'key' => $ph_token->key ) );
        $count = count( $tokens );
        
        // Assert
        $this->assertEquals( $count, 0 );
    }
    
    /**
     * Test Get By User
     */
    public function testGetByUser() {
        // Create
        $ph_token = $this->phactory->create( 'tokens' );
        
        // Get
        $this->token->get_by_user( self::WEBSITE_ID, self::TYPE );
        
        // Assert
        $this->assertEquals( $this->token->id, $ph_token->token_id );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->token = null;
    }
}
