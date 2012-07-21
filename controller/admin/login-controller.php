<?php
class LoginController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'login/';
        $this->section = 'Login';
    }

    /**
     * Login Page
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response('index');
        return $template_response;
    }

    /**
     * Override login function
     * @return bool
     */
    protected function get_logged_in_user() {
        return true;
    }
}


