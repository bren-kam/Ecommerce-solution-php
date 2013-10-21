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
        //$butler_feed = new ButlerFeedGateway();
        //$butler_feed->run();
        //$ashley = new AshleyMasterProductFeedGateway();
        //$ashley->run();

        $wl = new WebsiteLocation();
        $website_locations = $wl->get_results( 'SELECT wl.* FROM `website_location` AS wl LEFT JOIN `websites` AS w ON ( w.`website_id` = wl.`website_id` ) WHERE wl.`lng` IS NULL AND w.`status` = 1 AND w.`website_id` = 633', PDO::FETCH_CLASS, 'WebsiteLocation' );

        library('google-maps-api');
        $gmaps = new GoogleMapsAPI( new Account() );

        /**
         * @var WebsiteLocation $location
         */
        foreach ( $website_locations as $location ) {
            $geo_location = $gmaps->geocode( $location->address . ', ' . $location->city . ', ' . $location->state . ' ' . $location->zip );

            if ( $gmaps->success() ) {
                $location->lat = $geo_location->lat;
                $location->lng = $geo_location->lng;
                $location->save();
            }
        }

        return new HtmlResponse( 'heh' );
    }
}