<?php
class Server extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $name, $ip, $nodebalancer_ip, $whm_hash;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'server' );
    }

    /**
     * Get
     *
     * @param int $server_id
     */
    public function get( $server_id ) {
        $this->prepare(
            'SELECT * FROM `server` WHERE `id` = :server_id'
            , 'i'
            , array( ':server_id' => $server_id)
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Get all the companies
     *
     * @return Server[]
     */
    public function get_all() {
        return $this->get_results( 'SELECT * FROM `server`', PDO::FETCH_CLASS, 'Server' );
    }
}
