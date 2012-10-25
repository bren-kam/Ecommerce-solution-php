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
            , 'key' => $this->key
            , 'token_type' => $this->type
            , 'date_valid' => $this->date_valid
        ), 'isss' );

        $this->id = $this->token_id = $this->get_insert_id();
    }
}
