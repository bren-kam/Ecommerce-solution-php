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

        /**
        library('ac/ActiveCampaign.class');

        $ac = new ActiveCampaign( Config::key('ac-api-url'), Config::key('ac-api-key') );
        */

        $feed = new AshleySpecificFeedGateway();
        $account = new Account();
        $account->get( 354 );
        $feed->run( $account );

        return new HtmlResponse( 'heh' );
    }
}