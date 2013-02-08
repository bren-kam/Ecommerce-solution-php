<?php
class ReachesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'products/reaches/';
        $this->section = 'Reaches';
    }

    /**
     * List Reaches page
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
                    ->select( 'reaches' );
    }

    /***** AJAX *****/

    /**
     * List Reaches
     *
     * @return DataTableResponse
     */
    protected function list_reaches() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set variables
        $dt->order_by( 'wr.`name`', 'wu.`email`', 'wr.`assigned_to`', 'wr.`status`', 'wr.`priority`', 'wr.`date_created`' );
        $dt->add_where( " AND wr.`website_id` = " . $this->user->account->id );

        if ( !$this->user->has_permission(5) )
            $dt->add_where( ' AND wr.`status` = 0 AND wr.`waiting` = 1' );

        $dt->search( array( 'wr.`name`' => false, 'wu.`email`' => false, 'wr.`assigned_to`' => false ) );

        // Get Reaches
        $website_reach = new WebsiteReach();
        $reaches = $website_reach->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_reach->count_all( $dt->get_count_variables() ) );

        // Setup data
        $priorities = array(
            0 => _('Normal'),
            1 => _('High'),
            2 => _('Urgent')
        );

        $statuses = array(
            0 => _('Open'),
            1 => _('Closed')
        );

        $data = array();

        // Create output
        if ( is_array( $reaches ) )
        foreach ( $reaches as $reach ) {
            $date = new DateTime( $reach->date_created );

            $data[] = array(
                '<a href="' . url::add_query_arg( 'rid', $reach->id, '/reaches/reach/' ) . '">' . $reach->name . '</a>'
                , $reach->email
                , $reach->assigned_to
                , $statuses[ (int) $reach->status ]
                , $priorities[ (int) $reach->priority ]
                , $date->format( 'F jS, Y g:ia' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}

