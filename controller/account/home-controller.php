<?php
class HomeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'home/';
        $this->title = 'Dashboard';
    }

    /**
     * Setup a new account
     * @return TemplateResponse
     */
    protected function index() {
        return new RedirectResponse( '/website/' );

        $this->resources->css('dashboard/dashboard');
        $advertising_url = $this->user->account->get_settings('advertising-url');

        return $this->get_template_response( 'index' )
            ->select('dashboard')
            ->set( compact( 'advertising_url' ) );
    }

    /**
     * List Accounts to select
     *
     * @return TemplateResponse
     */
    protected function select_account() {
        return $this->get_template_response( 'select-account' )
            ->add_title( _('Select Account') );
    }

    /**
     * Change Account
     * @return RedirectResponse
     */
    protected function change_account() {
        if ( empty( $_GET['aid'] ) )
            return new RedirectResponse('/');

        /**
         * @var Account $account
         */
        foreach ( $this->user->accounts as $account ) {
            // If it's amongst the user's accounts, redirect him
            if ( $account->id == $_GET['aid'] ) {
                set_cookie( 'wid', $account->id, 172800 ); // 2 Days
                break;
            }
        }

        // Either redirect to home or logout if he's trying to control someone else's site
        return new RedirectResponse( '/' );
    }
}


