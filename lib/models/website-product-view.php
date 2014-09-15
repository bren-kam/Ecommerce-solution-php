<?php
class WebsiteProductView extends ActiveRecordBase {

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_product_view' );
    }

    /**
     * Purge Old Data
     * Keeps only WebsiteProductView items within the last week
     * @return ActiveRecordBase
     */
    public function purge_old_data() {
        return $this->prepare(
            "DELETE FROM website_product_view WHERE date_created < (CURDATE() - INTERVAL 7 DAY)"
            , ""
            , array()
        )->query();
    }
}