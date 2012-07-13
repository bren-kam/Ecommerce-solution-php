<?php
class User extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'users' );
    }

    /**
     * Get By Email
     *
     * @param string $email
     */
    public function get_by_email( $email ) {
        // Do stuff
    }

    /**
     * Check if the user has permissions
     *
     * @param int $permission
     * @return bool
     */
    public function has_permission( $permission ) {
        return false;
    }
}
