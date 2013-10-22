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
        library('Excel_Reader/Excel_Reader');
        $er = new Excel_Reader();
        // Set the basics and then read in the rows
        $er->setOutputEncoding('ASCII');
        $er->read( ABS_PATH . 'temp/map-price-list.xls' );

        $rows = array_slice( $er->sheets[0]['cells'], 3 );

        foreach ( $rows as $row ) {
            $product = new Product();
            $product->get_by_sku( $row[3] );

            if ( $product->id ) {
                $product->price_min = $row[17];
                fn::info( $product );exit;
                $product->save();
            }
        }

        return new HtmlResponse( 'heh' );
    }
}