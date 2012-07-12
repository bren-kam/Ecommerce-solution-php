<?php
class HomeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct( 'home/' );
    }

    /**
     * Setup a new account
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response('index');
        return $template_response;
    }
}


