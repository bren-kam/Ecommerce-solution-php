<?php
class CompaniesAjaxController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();
    }

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
        foreach ( $companies as $c ) {
            $date = new DateTime( $company->date_created );

            $data[] = array(
                '<a href="/accounts/companies/add-edit/?cid=' . $c->id . '" title="' . _('Edit Company') . '">' . $c->name . '</a>'
                , $company->domain
                , $date->format( 'F jS, Y' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}