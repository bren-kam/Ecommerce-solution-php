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

    /**
     * Copy products
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_by_account( $template_account_id, $account_id ) {
        $this->copy( array(
                'website_id' => $account_id
                , 'product_id' => NULL
                , 'status' => NULL
                , 'on_sale' => NULL
                , 'sequence' => NULL
                , 'active' => 1
            ), array( 'website_id' => $template_account_id )
        );
    }
}
