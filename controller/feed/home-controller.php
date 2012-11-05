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
     * Reports Search Page
     *
     * @return JsonResponse
     */
    protected function index() {
        $feed_api_request = new FeedApiRequest();

        return new JsonResponse( $feed_api_request->get_response() );
    }
}