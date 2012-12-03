<?php
class HomeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'home/';
        $this->title = 'Dashboard';
    }

    /**
     * Setup a new account
     * @return TemplateResponse
     */
    protected function index() {
        $response = $this->get_template_response( 'index' );
        $response->select('dashboard');

        return $response;
    }
}


