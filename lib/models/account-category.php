<?php
class AccountCategory extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_categories' );
    }

    /**
     * Deactivate products by account
     *
     * @param int $account_id
     */
    public function delete_by_account( $account_id ) {
        parent::delete( array( 'website_id' => $account_id ), 'i' );
    }
}
