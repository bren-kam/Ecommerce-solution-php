<?php
/**
 * Method to handle a Ticket
 */
class Ticket extends DB {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'tickets' );
    }
}
