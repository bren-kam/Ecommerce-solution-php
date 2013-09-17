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
        $butler_feed = new ButlerFeedGateway();
        $butler_feed->run();

        return new HtmlResponse( 'heh' );
    }
}