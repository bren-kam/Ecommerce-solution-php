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

    /**
     * Ticket
     *
     * @return RedirectResponse|TemplateResponse
     */
    public function ticket() {
        if ( !isset( $_GET['tid'] ) )
            return new RedirectResponse('/tickets/');

        // Yay! We have the ticket ID
        $ticket_id = $_GET['tid'];

        // Instantiate all the objects
        $ticket = new Ticket();
        $tc = new TicketComment();
        $tu = new TicketUpload();

        $ticket->get( $ticket_id );

        // Don't want them to see this if they don't have the right role
        if ( $this->user->role < $ticket->role && $this->user->user_id != $ticket->user_id )
            return new RedirectResponse('/tickets/');

        // Get the uploads
        $ticket_uploads = $tu->get_for_ticket( $ticket_id );
        $comment_array = $tc->get_all( $ticket_id );
        $comments = $comment_user_ids = array();

        if ( is_array( $comment_array ) ) {
            $comment_uploads = $tu->get_for_comments( $ticket_id );

            foreach ( $comment_array as $comment ) {
                $comments[$comment->ticket_comment_id] = $comment;
                $comment_user_ids[] = $comment->user_id;
            }

            if ( is_array( $comment_uploads ) )
            foreach ( $comment_uploads as $comment_upload ) {
                $comments[$comment_upload->ticket_comment_id][] = array(
                    'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $comment_upload->key
                    , 'name' => ucwords( str_replace( '-', ' ', f::name( $a->key ) ) )
                );
            }
        }

        $admin_users = $this->user->get_admin_users( $comment_user_ids );

        $template_response = $this->get_template_response( 'ticket', _('Ticket') )
            ->set( compact( 'ticket', 'ticket_uploads', 'comments', 'admin_users' ) );

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