<?php
class TicketsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'tickets/';
        $this->section = 'tickets';
    }

    /**
     * List Tickets
     *
     * @return TemplateResponse
     */
    protected function index() {
        $users = $this->user->get_all();
        $assigned_to_users = array();

        /**
         * @var User $user
         */
        foreach ( $users as $user ) {
            if ( $user->has_permission(6) && !empty( $user->contact_name ) )
                $assigned_to_users[$user->id] = $user->contact_name;
        }

        $template_response = $this->get_template_response( 'index' )
            ->set( compact( 'assigned_to_users' ) );

        $this->resources->css( 'tickets/list' );
        $this->resources->javascript( 'tickets/list' );

        // Reset any defaults
        unset( $_SESSION['tickets'] );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * List Accounts
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`' );
        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );
        $dt->add_where( $where = ' AND ( ' . $this->user->role . ' >= COALESCE( c.`role`, 7 ) OR a.`user_id` = ' . $this->user->id . ' )' );

        // If they are below 8, that means they are a partner
		if ( !$this->user->has_permission(8) )
			$dt->add_where( ' AND c.`company_id` = ' . (int) $this->user->company_id );

        $status = ( isset( $_SESSION['tickets']['status'] ) ) ? (int) $_SESSION['tickets']['status'] : 0;

        // Grab only the right status
        $dt->add_where( " AND a.`status` = $status" );

        // Grab only the right status
        if ( isset( $_SESSION['tickets']['assigned-to'] ) && !empty( $_SESSION['tickets']['assigned-to'] ) && '0' != $_SESSION['tickets']['assigned-to'] ) {
            if ( '-1' == $_SESSION['tickets']['assigned-to'] ) {
                $dt->add_where( ' AND c.`role` <= ' . (int) $this->user->role );
            } else {
                $assigned_to = ( $this->user->has_permission(8) ) ? ' AND c.`user_id` = ' . (int) $_SESSION['tickets']['assigned-to'] : ' AND ( b.`user_id` = ' . (int) $_SESSION['tickets']['assigned-to'] . ' OR c.`user_id` = ' . (int) $_SESSION['tickets']['assigned-to'] .' )';
                $dt->add_where( $assigned_to );
            }
        }

        /**
         * Create ticket class
         */
        $ticket = new Ticket();


        // Get accounts
        $tickets = $ticket->list_all( $dt->get_variables() );
        $dt->set_row_count( $ticket->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        if ( is_array( $tickets ) )
        foreach ( $tickets as $ticket ) {
            switch ( $ticket->priority ) {
                default:
                case 0:
                    $priority = '<span class="normal">NORMAL</span>';
                break;

                case 1:
                    $priority = '<span class="high">HIGH</span>';
                break;

                case 2:
                    $priority = '<span class="urgent">URGENT</span>';
                break;
            }

            $date = new DateTime( $ticket->date_created );

            $data[] = array(
                '<a href="/tickets/ticket/?tid=' . $ticket->id . '" title="' . _('View Ticket') . '">' . format::limit_chars( $ticket->summary, 55 ) . '</a>'
                ,  $ticket->name
                , $ticket->website
                , $priority
                , $ticket->assigned_to
                , $date->format('F j, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}