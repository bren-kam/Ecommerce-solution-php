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
     * @return TemplateResponse
     */
    protected function index() {
        if ( isset( $_SESSION['sm_facebook_page_id'] ) ) {
            $response = 'It is already set';
        } else {
            $_SESSION['sm_facebook_page_id'] = 5;
            $response = 'It is now set';
        }


        return new HtmlResponse( $response . fn::info( $_SESSION, false ) );
    }
}