<?php
class AccountProduct extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_products' );
    }

    /**
     * Deactivate products by account
     *
     * @param int $account_id
     */
    public function deactivate_by_account( $account_id ) {
        parent::update( array( 'active' => 0 ), array( 'website_id' => $account_id ), 'i', 'i' );
    }
}
