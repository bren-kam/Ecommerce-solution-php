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
        $this->section = 'accounts';
        $this->title = 'Companies';
    }

    /**
     * List Companies
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->has_permission(8) )
            return new RedirectResponse('/accounts/');

        $template_response = $this->get_template_response( 'index' )
            ->select( 'companies', 'view' );

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

        $template_response = $this->get_template_response( 'add-edit' )
            ->select( 'companies', 'add' )
            ->add_title( ( $company_id ) ? _('Edit') : _('Add') );

        $company = new Company();

        if ( $company_id )
            $company->get( $company_id );

        // Create new form table
        $ft = new FormTable( 'fAddEditCompany' );

        $ft->submit( ( $company_id ) ? _('Save') : _('Add') );

        $ft->add_field( 'text', _('Name'), 'tName', $company->name )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $ft->add_field( 'text', _('Domain'), 'tDomain', $company->domain )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'URL', _('The "Domain" field must contain a valid domain name') );

        $ft->add_field( 'text', _('Notification Email'), 'tEmail', $company->domain )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'email', _('The "Email" field must contain a valid email address') );


        // Update the company if posted
        if ( $ft->posted() ) {
            $company->name = $_POST['tName'];
            $company->domain = $_POST['tDomain'];
            $company->email = $_POST['tEmail'];

            if ( $company_id ) {
                $company->save();
                $this->notify( _('The company was successfully updated!') );
            } else {
                $company->create();
                $this->notify( _('Your company was successfully created!') );
            }

            return new RedirectResponse('/accounts/companies/');
        }

        $template_response->set( 'form', $ft->generate_form() );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * List Companies
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get Models
        $company = new Company();

        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`name`', '`domain`', '`date_created`' );
        $dt->search( array( '`name`' => true, '`domain`' => true ) );

        if ( !$this->user->has_permission(8) )
            $dt->add_where( ' AND `company_id` = ' . (int) $this->user->id );

        // Get rows
        $companies = $company->list_all( $dt->get_variables() );
        $dt->set_row_count( $company->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        if ( is_array( $companies ) )
        foreach ( $companies as $company ) {
            $date = new DateTime( $company->date_created );

            $data[] = array(
                '<a href="/accounts/companies/add-edit/?cid=' . $company->id . '" title="' . _('Edit Company') . '">' . $company->name . '</a>'
                , $company->domain
                , $date->format( 'F jS, Y' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}