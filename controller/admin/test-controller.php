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
        $account = new Account();
        $email_marketing = new EmailMarketing();
        $website_ids = $email_marketing->get_col("SELECT DISTINCT `website_id` FROM `email_lists` WHERE `ac_list_id` > 0");

        foreach ( $website_ids as $website_id ) {
            $account->get( $website_id );
            $ac = EmailMarketing::setup_ac( $account );
            $ac->setup_webhook();

            $ac_list_ids = $email_marketing->get_col( "SELECT `ac_list_id` FROM `email_lists` WHERE `website_id` = $account->id" );

            // Add webhook for this account
            $ac->webhook->add(
                'Unsubscribe Hook'
                , url::add_query_arg( 'aid', $account->id, 'http://admin.greysuitretail.com/ac/' )
                , $ac_list_ids
                , 'unsubscribe'
                , array( 'public', 'system', 'admin' )
            );
        }

        /**
        library('ac/ActiveCampaign.class');

        $ac = new ActiveCampaign( Config::key('ac-api-url'), Config::key('ac-api-key') );
        */

        return new HtmlResponse( 'heh' );
    }
}