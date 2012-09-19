<?php
class HomeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();
    }

    /**
     * Setup a new account
     * @return RedirectResponse
     */
    protected function index() {
        return new RedirectResponse( '/accounts/' );
    }
}


