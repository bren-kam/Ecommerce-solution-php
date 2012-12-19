<?php
class ProductsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'products/';
        $this->section = 'products';
        $this->title = _('Products');
    }

    /**
     * List Shopping Cart Users
     *
     * @return TemplateResponse
     */
    protected function index() {
        $response = $this->get_template_response( 'index', _('Products') )
            ->select( 'sub-products', 'view' );

        return $response;
    }
}


