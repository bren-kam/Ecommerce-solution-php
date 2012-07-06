<?php
/**
 * Method to handle an account
 */
class Account extends DB {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'websites' );
    }
}
