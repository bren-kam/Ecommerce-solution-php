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
        $account = new Account();
        $email_marketing = new EmailMarketing();
        $website_ids = $email_marketing->get_col("SELECT DISTINCT `website_id` FROM `email_lists` WHERE `ac_list_id` > 0");

        foreach ( $website_ids as $website_id ) {
            continue;
            $account->get( $website_id );
            $ac = EmailMarketing::setup_ac( $account );
            $ac->setup_list();

            $email_lists = $email_marketing->get_results( "SELECT * FROM `email_lists` WHERE `website_id` = $account->id", PDO::FETCH_CLASS, 'EmailList' );
            extract( $account->get_settings( 'address', 'city', 'state', 'zip' ) );
            /**
             * @var EmailList $list
             */
            foreach ( $email_lists as $list ) {
                $ac->list->edit( $list->ac_list_id, $list->name, $this->user->account->ga_profile_id, url::domain( $account->domain, false ), $account->title, $address, $city, $state, $zip );
            }
        }

        return new HtmlResponse( 'heh' );
    }
}