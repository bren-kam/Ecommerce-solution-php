<?php
/**
 * Method to handle a Product
 */
class Product extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'products' );
    }
}
