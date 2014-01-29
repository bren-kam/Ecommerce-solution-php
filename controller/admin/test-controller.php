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
        //echo security::encrypt( 'myfurniturediscounters@blinkyblinky.me', ENCRYPTION_KEY, true );
        $usernames = $this->user->get_results( "SELECT `website_id`, `value` FROM `website_settings` WHERE `key` = 'ga-username'", PDO::FETCH_ASSOC );

        foreach ( $usernames as $username ) {
            echo $username['website_id'] . ' - ' . security::decrypt( base64_decode( $username['value'] ), ENCRYPTION_KEY ) . "\n<br>";
        }


        return new HtmlResponse( 'heh' );
    }
}