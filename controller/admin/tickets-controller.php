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
        $tu = new TicketUpload();
        $tc = new TicketComment();

        $ticket->get( $ticket_id );

        // Don't want them to see this if they don't have the right role
        if ( $this->user->role < $ticket->role && $this->user->user_id != $ticket->user_id )
            return new RedirectResponse('/tickets/');

        // Get the uploads
        $ticket_uploads = $tu->get_by_ticket( $ticket_id );
        $comment_array = $tc->get_by_ticket( $ticket_id );
        $comments = $comment_user_ids = array();

        if ( is_array( $comment_array ) ) {
            $comment_uploads = $tu->get_by_comments( $ticket_id );

            foreach ( $comment_array as $comment ) {
                $comments[$comment->ticket_comment_id] = $comment;
                $comment_user_ids[] = $comment->user_id;
            }

            if ( is_array( $comment_uploads ) )
            foreach ( $comment_uploads as $comment_upload ) {
                $comments[$comment_upload->ticket_comment_id]->uploads[] = array(
                    'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $comment_upload->key
                    , 'name' => ucwords( str_replace( '-', ' ', f::name( $comment_upload->key ) ) )
                );
            }
        }

        $admin_users = $this->user->get_admin_users( $comment_user_ids );

        $template_response = $this->get_template_response( 'ticket', _('Ticket') )
            ->add_title( _('View') )
            ->set( compact( 'ticket', 'ticket_uploads', 'comments', 'admin_users' ) );

        $this->resources
            ->css( 'tickets/ticket' )
            ->javascript( 'tickets/ticket', 'jquery.autoresize' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * Upload an attachment
     *
     * @return AjaxResponse
     */
    public function upload_attachment() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['wid'] ), _('Failed to upload attachment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $ticket_upload = new TicketUpload();
        $file = new File();
        $uploader = new qqFileUploader( array('pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt'), 10485760 );

        // Get variables
		$directory = $this->user->id . '/' . $_POST['wid']. '/';
        $file_name =  format::slug( f::strip_extension( $_GET['qqfile'] ) ) . '.' . f::extension( $_GET['qqfile'] );

        // Create upload
        $ticket_upload->key = $directory . $file_name;
        $ticket_upload->create();

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload attachment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $file_url = $file->upload_file( $result['file_path'], $directory, $file_name );

        // Clone image template
        jQuery('#image-template')->clone()
            ->removeAttr('id')
            ->find('a:first')
                ->attr( 'href', str_replace( '/small/', '/large/', $image_url ) )
                ->find('img:first')
                    ->attr( 'src', $image_url )
                    ->parents('.image:first')
            ->find('input:first')
                ->val($image_name)
                ->parent()
            ->appendTo('#images-list');

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Delete a comment
     *
     * @return AjaxResponse
     */
    public function delete_comment() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['tcid'] ), _('Failed to delete comment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket comment
        $ticket_comment = new TicketComment();
        $ticket_comment->get( $_POST['tcid'] );

        // Need to get uploads and delete them

        // Then delete ticket
        $ticket_comment->delete();

        return $response;
    }

    /**
     * Update who the ticket is assigned to
     *
     * @return AjaxResponse
     */
    public function update_assigned_to() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['tid'] ) && isset( $_POST['auid'] ), _('Failed to update assigned user') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket
        $ticket = new Ticket();
        $ticket->get( $_POST['tid'] );

        // Change priority
        $ticket->assigned_to_user_id = $_POST['auid'];

        // Update ticket
        $ticket->update();

        // Send out email
        $priorities = array(
            0 => 'Normal',
            1 => 'High',
            2 => 'Urgent'
        );

        $assigned_user = new User();
        $assigned_user->get( $_POST['atui'] );

        // Send out an email if their role is less than 8
        $message = 'Hello ' . $assigned_user->contact_name . ",\n\n";
        $message .= 'You have been assigned Ticket #' . $ticket->id . ". To view it, follow the link below:\n\n";
        $message .= 'http://admin.' . url::domain( $assigned_user->domain, false ) . '/tickets/ticket/?tid=' . $ticket->id . "\n\n";
        $message .= 'Priority: ' . $priorities[$ticket->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        fn::mail( $assigned_user->email, 'You have been assigned Ticket #' . $ticket->id . ' (' . $priorities[$ticket->priority] . ') - ' . $ticket->summary, $message, $assigned_user->company . ' <noreply@' . url::domain( $assigned_user->domain, false ) . '>' );

        return $response;
    }

    /**
     * Update the priority of a ticket
     *
     * @return AjaxResponse
     */
    public function update_priority() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['tid'] ) && isset( $_POST['priority'] ), _('Failed to update priority') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket
        $ticket = new Ticket();
        $ticket->get( $_POST['tid'] );

        // Change priority
        $ticket->priority = $_POST['priority'];

        // Update ticket
        $ticket->update();

        return $response;
    }

    /**
     * Update ticket status
     *
     * @return AjaxResponse
     */
    public function update_status() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['tid'] ) && isset( $_POST['status'] ), _('Failed to update status') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket
        $ticket = new Ticket();
        $ticket->get( $_POST['tid'] );

        // Change status
        $ticket->status = $_POST['status'];

        // Update ticket
        $ticket->update();

        // Mark statistic for updated tickets if it's a GSR user
        if ( 1 == $ticket->status && in_array( $this->user->id, array( 493, 1, 814, 305, 85, 19 ) ) ) {
            // Load library
            library('statistics-api');
            $stat = new Stat_API( Config::key('rs-key') );

            // Get the dates
            $date = new DateTime();
            $ticket_date = new DateTime( $ticket->date_created );

            // Add the value of a completed ticket
            $stat->add_graph_value( 23452, 1, $date->format('Y-m-d') );

            // Add the average ticket time
            $hours = ( $date->getTimestamp() - $ticket_date->getTimestamp() ) / 3600;
            $stat->add_graph_value( 23453, round( $hours, 1 ), $date->format('Y-m-d')  );
        }

        return $response;
    }

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
			$dt->add_where( ' AND ( c.`company_id` = ' . (int) $this->user->company_id . ' OR a.`user_id` = ' . (int) $this->user->id . ' )' );

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