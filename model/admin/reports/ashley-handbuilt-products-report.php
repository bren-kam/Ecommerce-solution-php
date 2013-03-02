<?php
class AshleyHandbuiltProductsReport extends CustomReport {
    /**
     * @var User
     */
    protected $user;

    /**
     * Setup
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the Report
     *
     * @return array
     */
    public function report() {
        $report = $this->get_results( "SELECT `name`, `sku` FROM `products` WHERE `user_id_created` NOT IN (353, 1477) AND `brand_id` IN (8,170,171,588) AND `publish_visibility` <> 'deleted' ORDER BY `sku`", PDO::FETCH_ASSOC );

        array_unshift( $report, array( 'Product Name', 'SKU' ) );

        return $report;
    }
}