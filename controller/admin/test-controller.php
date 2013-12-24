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
        $product = json_encode( array(
            'auth_key' => 'abc123'
            , 'method' => 'set-product'
            , 'product' => array(
                'sku' => 'A10200'
                , 'category' => 'Dining Room > Chairs'
                , 'industry' => 'furniture'
                , 'name' => 'Regal Chair'
                , 'description' => 'The Regal series offers furniture suitable for royalty. Rich gold and brown colours with the finest wood will give your room another feeling. This chair is part of the set and paired greated with the Regal Table.'
                , 'price_wholesale' => '50'
                , 'price_map' => '89.99'
                , 'status' => 'in-stock'
                , 'specifications' => array(
                    'Height' => '36 in.'
                    , 'Width' => '15 1/2 in.'
                    , '52 pounds'
                )
                , 'images' => array(
                    'http://mysite.com/images/regal-chair.png'
                    , 'http://mysite.com/images/regal-chair-back.png'
                )
            )
        ));

        echo $product;

        return new HtmlResponse( 'heh' );
    }
}