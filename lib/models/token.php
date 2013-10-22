<?php
class Token extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $token_id, $user_id, $key, $type, $date_valid;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'tokens' );

        // We want to make sure they match
        if ( isset( $this->token_id ) )
            $this->id = $this->token_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->key = md5( time() . mt_rand( 0, 10000 ) );

        $this->insert( array(
            'user_id' => $this->user_id
            , 'key' => strip_tags($this->key)
            , 'token_type' => strip_tags($this->type)
            , 'date_valid' => strip_tags($this->date_valid)
        ), 'isss' );

        $this->id = $this->token_id = $this->get_insert_id();
    }

    /**
     * Get Token
     *
     * @param $token
     */
    public function get( $token ) {
         $this->prepare(
            'SELECT * FROM `tokens` WHERE `key` = :key'
            , 'ii'
            , array( ':key' => $token )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->token_id;
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'token_id' => $this->id
        ), 'ii' );
    }

    /**
     * Check it the user has token registed
     * 
     * @param int $user_id 
     * @param string $token_type 
     * 
     * @return boolean return true if the tokend found false otherwise
     * 
     */
    public function get_by_user( $user_id, $token_type ) {
        $this->prepare(
            'SELECT * FROM `tokens` WHERE `user_id` = :user_id AND `token_type` = :token_type'
            , 'ii'
            , array( ':user_id' => $user_id, ':token_type' => $token_type )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->token_id;
    }
}
