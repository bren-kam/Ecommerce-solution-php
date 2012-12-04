<?php
class ErrorController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = '../error/';
    }

    /**
     * Handle 404 Error
     *
     * @return TemplateResponse
     */
    protected function http_404() {
        return $this->get_template_response('404');
    }

    /**
     * Handle 403 Error
     *
     * @return TemplateResponse
     */
    protected function http_403() {
        return $this->get_template_response('403');
    }
}