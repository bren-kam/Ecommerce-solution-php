<?php
class CustomizeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/customize/';
        $this->section = 'accounts';
        $this->title = 'Customize';
    }

    /**
     * List Companies
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
            return new RedirectResponse('/accounts/');

        return $this->get_template_response( 'index' )
            ->kb( 9 )
            ->select( 'customize', 'view' );
    }

    /**
     * Add/Edit A Company
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function css() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) || !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get Accoutn
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Create new form table
        $ft = new FormTable( 'fCustomCSS' );

        $ft->submit(  _('Save') );

        $ft->add_field( 'textarea', _('CSS'), 'taCSS', $account->get_settings('css') );

        // Update the company if posted
        if ( $ft->posted() ) {
            $account->set_settings( array( 'css' => $_POST['taCSS'] ) );
            $this->notify( 'CSS has been successfully updated!');
        }

        return $this->get_template_response( 'css' )
            ->kb( 10 )
            ->select( 'customize', 'css' )
            ->set( 'form', $ft->generate_form() )
            ->add_title( _('CSS') );
    }
}