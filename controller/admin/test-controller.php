<?php
class TestController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'test/';
    }

    /**
     * List Accounts
     *
     *
     * @return TemplateResponse
     */
    protected function index() {
        set_time_limit(1200);
        $product = new Product();

        $products = $product->get_results(
            "SELECT p.`product_id` AS id, p.`product_specifications` FROM `products` AS p LEFT JOIN `product_specification` AS ps ON ( ps.`product_id` = p.`product_id` ) WHERE p.`publish_visibility` <> 'deleted' AND ps.`product_id` IS NULL AND p.`product_specifications` <> '' AND p.`product_specifications` <> 'a:0:{}' LIMIT 10000"
            , PDO::FETCH_CLASS, 'Product'
        );

        /**
         * @var Product $product
         */
        foreach ( $products as $product ) {
            if ( empty( $product->specifications ) && !empty( $product->product_specifications ) ) {
                $specifications = @unserialize( $product->product_specifications );

                if ( !$specifications )
                    $specifications = @unserialize( html_entity_decode( $product->product_specifications, ENT_QUOTES, 'UTF-8' ) );

                $specs = array();

                if ( is_array( $specifications ) && count( $specifications ) > 0 )
                foreach ( $specifications as $ps ) {
                    $specification_name = html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' );
                    $specification_value = html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' );

                    $specs[] = array ( $specification_name, $specification_value );
                }

                $product->add_specifications( $specs );
            }
        }

        return new HtmlResponse( 'heh' );
    }
}