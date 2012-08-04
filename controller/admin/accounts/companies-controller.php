<?php
class CompaniesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/companies/';
        $this->section = 'Accounts';
    }

    /**
     * List Companies
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->has_permission(8) )
            return new RedirectResponse('/accounts/');

        $template_response = $this->get_template_response( 'index' );
        $template_response->select( 'companies', 'view' );

        return $template_response;
    }

    /**
     * Add/Edit A Company
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        if ( !$this->user->has_permission(8) )
            return new RedirectResponse('/accounts/');

        // Get the company_id if there is one
        $company_id = ( isset( $_GET['cid'] ) ) ? (int) $_GET['cid'] : false;

        $template_response = $this->get_template_response( 'add-edit' );
        $template_response->select( 'companies', 'view' );

        $company = new Company();

        if ( $company_id )
            $company->get( $company_id );

        // Create new form table
        $ft = new FormTable( $this->resources, 'fAddEditCompany' );

        $ft->add_field( 'text', _('Name'), 'tName', $company->name )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $ft->add_field( 'text', _('Domain'), 'tDomain', $company->domain )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'URL', _('The "Domain" field must contain a valid domain name') );

        // Update the company if posted
        if ( $ft->posted() ) {
            $company->name = $_POST['tName'];
            $company->domain = $_POST['tDomain'];

            if ( $company_id ) {
                $company->update();
            } else {
                $company->create();
                $company_id = $company->id;
            }
        }

        $ft->submit( ( $company_id ) ? _('Save') : _('Add') );

        $template_response->set( 'form', $ft->generate_form() );

        return $template_response;
    }
}