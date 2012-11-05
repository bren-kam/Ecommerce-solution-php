<?php
abstract class CustomReport extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        // No default table
        parent::__construct( '' );
    }

    // Report
    abstract public function report();
}
