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
        set_time_limit(300);
        $website_location = new WebsiteLocation();
        $addresses = $website_location->get_results("SELECT w.`website_id`, wpm.`value` FROM `website_pagemeta` AS wpm LEFT JOIN `website_pages` AS wp ON ( wp.`website_page_id` = wpm.`website_page_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = wp.`website_id` ) WHERE w.`status` = 1 AND wpm.`key` = 'addresses'", PDO::FETCH_ASSOC);

        foreach ( $addresses as $address ) {
            $adds = unserialize( htmlspecialchars_decode( $address['value'] ) );
            foreach ( $adds as $add ) {
                $website_location = new WebsiteLocation();
                $website_location->website_id = $address['website_id'];
                $website_location->name = $add['location'];
                $website_location->address = $add['address'];
                $website_location->city = $add['city'];
                $website_location->state = $add['state'];
                $website_location->zip = $add['zip'];
                $website_location->phone = $add['phone'];
                $website_location->fax = $add['fax'];
                $website_location->email = $add['email'];
                $website_location->website = $add['website'];
                $website_location->store_hours = $add['store-hours'];
                $website_location->create();
            }
        }

        return new HtmlResponse( 'heh' );
    }
}