<?php
class AccountsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/';
        $this->section = 'accounts';
    }

    /**
     * List Accounts
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response( 'index' );
        $template_response->select( 'Accounts', 'view' );

        return $template_response;
    }

    /**
     * Edit Account
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        $account = new Account;
        $account->get( $this->user, $_GET['aid'] );

        $this->resources->javascript('accounts/edit');
        $this->resources->css('accounts/edit');

        $template_response = $this->get_template_response('edit');
        $template_response->select( 'accounts', 'edit' );
        $template_response->set( 'account', $account );

        return $template_response;
    }
}