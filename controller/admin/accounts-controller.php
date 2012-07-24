<?php
class AccountsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct( 'account/' );
    }

    /**
     * Login Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
    }
}