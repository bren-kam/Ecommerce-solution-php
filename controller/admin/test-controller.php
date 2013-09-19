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
        $account = new Account();
        $websites = $account->get_results("SELECT ws.`value` AS zone_id, w.`domain` FROM `website_settings` AS ws LEFT JOIN `websites` AS w ON ( w.`website_id` = ws.`website_id` ) WHERE w.`status` = 1 AND ws.`key` = 'r53-zone-id'");

        library('r53');
        $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );

        foreach ( $websites as $website ) {
            $full_domain_name = url::domain( $website['domain'], false ) .'.';
            $r53->changeResourceRecordSets( $website['zone_id'], array(
                $r53->prepareChange( 'CREATE', $full_domain_name, 'TXT', '14400', '"v=spf1 a mx ip4:199.79.48.137 ~all"' )
            ));
        }

        return new HtmlResponse( 'heh' );
    }
}