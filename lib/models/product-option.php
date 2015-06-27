<?php
class ProductOption extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $name, $type;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_option' );
    }
}
