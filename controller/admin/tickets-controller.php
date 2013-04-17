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
        // Get variables
        $assigned_to_users = $this->user->get_admin_users();

        // Reset any defaults
        unset( $_SESSION['tickets'] );

        // Set first one to be me
        if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) )
            $_SESSION['tickets']['assigned-to'] = $this->user->id;

        $this->resources->css( 'tickets/list' )
            ->javascript( 'tickets/list' );

        return $this->get_template_response( 'index' )
            ->set( compact( 'assigned_to_users' ) );
    }

    /**
     * Ticket
     *
     * @return RedirectResponse|TemplateResponse
     */
    protected function ticket() {
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
            ->javascript( 'fileuploader', 'jquery.autoresize', 'tickets/ticket' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * Create ticket
     *
     * @return AjaxResponse
     */
    protected function create() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $ticket = new Ticket();

        if ( isset( $_POST['hSupportTicketId'] ) && !empty( $_POST['hSupportTicketId'] ) ) {
            $ticket->get( $_POST['hSupportTicketId'] );
        } else {
            $ticket->status = 0;
            $ticket->create();
        }

        // Special words
        $words = array(
            247 => '@sales'  // Chad
            , 73 => '@products' // Chris
            , 54 => '@accounting' // Craig
        );

        // Special hash priorities
        $priorities = array(
            1 => '#high' // high priority
            , 2 => '#urgent' // urgent priority
        );

        $variables = array(
            $_POST['tTicketSummary']
            , $_POST['taTicketMessage']
        );

        // Figure out who we're assigned to
        $assigned_to_user_id = 493; // Technical user (default)
        $priority = 0; // Normal

        // Find out if they are trying to direct it to a particular person
        foreach ( $variables as $string ) {
            $string = strtolower( $string );

            if ( 493 == $assigned_to_user_id )
            foreach ( $words as $user_id => $word ) {
                if ( stristr( $string, $word ) ) {
                    // Found it -- we're done here
                    $assigned_to_user_id = $user_id;
                    break;
                }
            }

            if ( 0 == $priority )
            foreach ( $priorities as $priority_id => $hash ) {
                if ( stristr( $string, $hash ) ) {
                    // Found it -- we're done here
                    $priority = $priority_id;
                    break;
                }
            }
        }

        // Get browser information
        $browser = fn::browser();

        // Set ticket information
        $ticket->user_id = $this->user->id;
        $ticket->assigned_to_user_id = $assigned_to_user_id; // Technical user
        $ticket->website_id = 0; // Admin side -- no website
        $ticket->summary = $_POST['tTicketSummary'];
        $ticket->message = nl2br( format::links_to_anchors( format::htmlentities( format::convert_characters( $_POST['taTicketMessage'] ), array('&') ), true , true ) );
        $ticket->browser_name = $browser['name'];
        $ticket->browser_version = $browser['version'];
        $ticket->browser_platform = $browser['platform'];
        $ticket->browser_user_agent = $browser['user_agent'];
        $ticket->status = 0;
        $ticket->priority = $priority;

        // Update the ticket
        $ticket->save();

        // Add links if there are any
        if ( isset( $_POST['uploads'] ) && is_array( $_POST['uploads'] ) )
            $ticket->add_links( $_POST['uploads'] );

        // Add statistics
        library('statistics-api');
        $stat = new Stat_API( Config::key('rs-key') );

        // Get the dates
        $date = new DateTime();

        // Add the value of a new ticket
        $stat->add_graph_value( 23451, 1, $date->format('Y-m-d') );

        // Send email
        $assigned_user = new User();
        $assigned_user->get( $assigned_to_user_id ); // Technical user

        fn::mail( $assigned_user->email, 'New ' . $assigned_user->company . ' Ticket - ' . $ticket->summary, "Name: " . $this->user->contact_name . "\nEmail: " . $this->user->email . "\nSummary: " . $ticket->summary . "\n\n" . $ticket->message . "\n\nhttp://admin." . $assigned_user->domain . "/tickets/ticket/?tid=" . $ticket->id );

        // Close the window
        jQuery('a.close:visible:first')->click();

        // Don't want the attachments coming up next time
        jQuery('#ticket-attachments-list')->empty();

        // Reset the two fields
        jQuery('#tTicketSummary, #taTicketMessage, #hSupportTicketId')->val('')->blur();

        // Add the jQuery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Add a comment
     *
     * @return AjaxResponse
     */
    protected function add_comment() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['comment'] ) && isset( $_POST['hTicketId'] ), _('Failed to add comment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $ticket = new Ticket();
        $ticket_comment = new TicketComment();
        $ticket_creator = new User();
        $assigned_user = new User();
        $ticket_upload = new TicketUpload();

        // Get ticket
        $ticket->get( $_POST['hTicketId'] );

        // Get users
        $ticket_creator->get( $ticket->user_id );
        $assigned_user->get( $ticket->assigned_to_user_id );

        // Set variables
        $status = ( 0 == $ticket->status ) ? ' (Open)' : ' (Closed)';

        // Create ticket comment
        $ticket_comment->ticket_id = $ticket->id;
        $ticket_comment->user_id = $this->user->user_id;
        $ticket_comment->comment = nl2br( format::links_to_anchors( format::htmlentities( format::convert_characters( $_POST['comment'] ), array('&') ), true, true ) );
        $ticket_comment->private = (int) isset( $_POST['private'] );

        $ticket_comment->create();

        // Handle attachments
        if ( isset( $_POST['uploads'] ) && is_array( $_POST['uploads'] ) )
            $ticket_comment->add_upload_links( $_POST['uploads'] );

        // Send emails
        $comment = strip_tags( $ticket_comment->comment );

        // If it's not private, send an email to the client
        if ( 0 == $ticket_comment->private && ( 1 == $ticket->status || !$ticket_creator->has_permission( User::ROLE_ADMIN ) ) )
            fn::mail( $ticket->email, 'Ticket #' . $ticket->id . $status . ' - ' . $ticket->summary, "******************* Reply Above This Line *******************\n\n{$comment}\n\n**Support Issue**\n" . $ticket->message, $ticket_creator->company . ' <support@' . url::domain( $ticket_creator->domain, false ) . '>' );

        // Send the assigned user an email if they are not submitting the comment
        if ( $ticket->assigned_to_user_id != $this->user->id && $ticket->assigned_to_user_id != $ticket->user_id && 1 == $ticket->status )
            fn::mail( $assigned_user->email, 'New Comment on Ticket #' . $ticket->id . ' - ' . $ticket->summary, "******************* Reply Above This Line *******************\n\n" . $this->user->contact_name . ' has posted a new comment on Ticket #' . $ticket->id . ".\n\nhttp://admin." . url::domain( $assigned_user->domain, false ) . "/tickets/ticket/?tid=" . $ticket->id . "**Comment**\n{$comment}\n\n**Support Issue**\n" . $ticket->message, $assigned_user->company . ' <support@' . url::domain( $assigned_user->domain, false ) . '>' );

        /***** Add comment *****/

        // Declare variables
        $date = new DateTime( $ticket_comment->date_created );
        $confirmation = _('Are you sure you want to delete this comment? This cannot be undone.');
        $uploads = $ticket_upload->get_by_comment( $ticket_comment->id );

        // Create Comment HTML
        $comment = '<div class="comment" id="comment-' . $ticket_comment->id . '">';
        $comment .= '<p class="name">';

        if ( '1' == $ticket_comment->private )
            $comment .= '<img src="/images/icons/lock.gif" width="11" height="15" alt="' . _('Private') . '" class="private" />';

        $comment .= '<a href="#" class="assign-to" rel="' . $ticket->user_id . '">' . $this->user->contact_name . '</a>';
        $comment .= '<span class="date">' . $date->format( 'F j, Y g:ia' ) . '</span>';
        $comment .= '<a href="#" class="delete-comment" title="' . _('Delete') . '" confirm="' .  $confirmation . '">';
        $comment .= '<img src="/images/icons/x.png" alt="' . _('X') . '" width="15" height="17" />';
        $comment .= '</a>';
        $comment .= '</p>';
        $comment .= '<p class="message">' . $ticket_comment->comment . '</p>';
        $comment .= '<div class="attachments">';

        /**
         * @var TicketUpload $upload
         */
        if ( isset( $uploads ) )
        foreach ( $uploads as $upload ) {
            $comment .= '<p><a href="http://s3.amazonaws.com/retailcatalog.us/attachments/' . $upload->key . '" target="_blank" title="' . _('Download') . '">' . f::name( $upload->key ) . '</a></p>';
        }

        $comment .= '</div>';
        $comment .= '<br clear="left" />';
        $comment .= '</div>';

        // Add comment
        jQuery('#comments-list')->prepend( $comment );

        // Also need to reset the form
        jQuery('#comment')
            ->val('')
            ->trigger('blur');

        jQuery('#uploads')->empty();
        jQuery('#private')->prop( 'checked', false );


        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload an attachment to comment
     *
     * @return AjaxResponse
     */
    protected function upload_to_ticket() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $ticket_upload = new TicketUpload();
        $ticket = new Ticket();
        $file = new File( 'retailcatalog.us' );
        $uploader = new qqFileUploader( array('pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt'), 10485760 );

        if ( !isset( $_GET['tid'] ) || empty( $_GET['tid'] ) ) {
            $ticket->status = -1;
            $ticket->create();

            // Set the variable
            jQuery('#hSupportTicketId')->val( $ticket->id );
        } else {
            $ticket->get( $_GET['tid'] );
        }

        // Get variables
		$directory = $this->user->id . '/' . $ticket->ticket_id . '/';
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

        $file_url = $file->upload_file( $result['file_path'], $ticket_upload->key, 'attachments/' );
        $confirmation = _('Are you sure you want to remove this attachment?');
        $delete_upload = nonce::create('delete_upload');

        $upload = '<div class="upload" id="upload-' . $ticket_upload->id . '">';
        $upload .= '<a href="' . $file_url . '" class="download" target="_blank">' . $file_name . '</a>';
        $upload .= '<a href="' . url::add_query_arg( array( '_nonce' => $delete_upload, 'tuid' => $ticket_upload->id ), '/tickets/delete-upload/' ) . '" class="delete" title="' . _('Delete') . '" ajax="1" confirm="' . $confirmation . '">';
        $upload .= '<img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete') . '" />';
        $upload .= '</a>';
        $upload .= '<input type="hidden" name="uploads[]" value="' . $ticket_upload->id . '" />';
        $upload .= '</div>';

        // Clone image template
        jQuery('#ticket-attachments-list')
            ->append( $upload )
            ->sparrow();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload an attachment to comment
     *
     * @return AjaxResponse
     */
    protected function upload_to_comment() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['tid'] ), _('Failed to upload attachment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $ticket_upload = new TicketUpload();
        $file = new File( 'retailcatalog.us' );
        $uploader = new qqFileUploader( array('pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt'), 10485760 );

        // Get variables
		$directory = $this->user->id . '/' . $_GET['tid'] . '/';
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

        $file_url = $file->upload_file( $result['file_path'], $ticket_upload->key, 'attachments/' );
        $confirmation = _('Are you sure you want to remove this attachment?');
        $delete_upload = nonce::create('delete_upload');

        $upload = '<div class="upload" id="upload-' . $ticket_upload->id . '">';
        $upload .= '<a href="' . $file_url . '" class="download" target="_blank">' . $file_name . '</a>';
        $upload .= '<a href="' . url::add_query_arg( array( '_nonce' => $delete_upload, 'tuid' => $ticket_upload->id ), '/tickets/delete-upload/' ) . '" class="delete" title="' . _('Delete') . '" ajax="1" confirm="' . $confirmation . '">';
        $upload .= '<img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete') . '" />';
        $upload .= '</a>';
        $upload .= '<input type="hidden" name="uploads[]" value="' . $ticket_upload->id . '" />';
        $upload .= '</div>';

        // Clone image template
        jQuery('#uploads')
            ->append( $upload )
            ->sparrow();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Delete a comment
     *
     * @return AjaxResponse
     */
    protected function delete_comment() {
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
        $ticket_upload = new TicketUpload();
        $uploads = $ticket_upload->get_by_comment( $_POST['tcid'] );

        if ( is_array( $uploads ) ) {
            $file = new File( 'retailcatalog.us' );

            /**
             * @var $upload TicketUpload
             */
            foreach ( $uploads as $upload ) {
                // Delete the file
                $file->delete_file( $upload->key, 'attachments/' );

                // Delete the upload entry
                $upload->delete();
            }

            // Delete links
            $ticket_comment->delete_upload_links();
        }

        // Remove from page
        jQuery('#comment-' . $ticket_comment->id)->remove();

        // Then delete ticket
        $ticket_comment->delete();

        // Add jquery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Delete Upload
     *
     * @return AjaxResponse
     */
    protected function delete_upload() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['tuid'] ), _('Failed to delete attachment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket upload
        $ticket_upload = new TicketUpload();
        $ticket_upload->get( $_GET['tuid'] );

        // Delete upload
        $file = new File( 'retailcatalog.us' );

        $file->delete_file( $ticket_upload->key, 'attachments/' );

        // Remove it from the page
        jQuery('#upload-' . $ticket_upload->id)->remove();

        // Delete upload
        $ticket_upload->delete_upload();

        // Add response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Update who the ticket is assigned to
     *
     * @return AjaxResponse
     */
    protected function update_assigned_to() {
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
        $ticket->save();

        // Send out email
        $priorities = array(
            0 => 'Normal',
            1 => 'High',
            2 => 'Urgent'
        );

        $assigned_user = new User();
        $assigned_user->get( $_POST['auid'] );

        // Send out an email if their role is less than 8
        $message = 'Hello ' . $assigned_user->contact_name . ",\n\n";
        $message .= 'You have been assigned Ticket #' . $ticket->id . ". To view it, follow the link below:\n\n";
        $message .= 'http://admin.' . url::domain( $assigned_user->domain, false ) . '/tickets/ticket/?tid=' . $ticket->id . "\n\n";
        $message .= 'Priority: ' . $priorities[$ticket->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        fn::mail( $assigned_user->email, 'You have been assigned Ticket #' . $ticket->id . ' (' . $priorities[$ticket->priority] . ') - ' . $ticket->summary, $message, $assigned_user->company . ' <noreply@' . url::domain( $assigned_user->domain, false ) . '>' );

        // Change who it's assigned to
        jQuery('#sAssignedTo')->val( $assigned_user->id );

        // Add jQuery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Update the priority of a ticket
     *
     * @return AjaxResponse
     */
    protected function update_priority() {
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
        $ticket->save();

        return $response;
    }

    /**
     * Update ticket status
     *
     * @return AjaxResponse
     */
    protected function update_status() {
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
        $ticket->save();

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
		if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
			$dt->add_where( ' AND ( c.`company_id` = ' . (int) $this->user->company_id . ' OR a.`user_id` = ' . (int) $this->user->id . ' )' );

        $status = ( isset( $_SESSION['tickets']['status'] ) ) ? (int) $_SESSION['tickets']['status'] : 0;

        // Grab only the right status
        $dt->add_where( " AND a.`status` = $status" );

        // Grab only the right status
        if ( isset( $_SESSION['tickets']['assigned-to'] ) && !empty( $_SESSION['tickets']['assigned-to'] ) && '0' != $_SESSION['tickets']['assigned-to'] ) {
            if ( '-1' == $_SESSION['tickets']['assigned-to'] ) {
                $dt->add_where( ' AND c.`role` <= ' . (int) $this->user->role );
            } else {
                $assigned_to = ( $this->user->has_permission( User::ROLE_SUPER_ADMIN ) ) ? ' AND c.`user_id` = ' . (int) $_SESSION['tickets']['assigned-to'] : ' AND ( b.`user_id` = ' . (int) $_SESSION['tickets']['assigned-to'] . ' OR c.`user_id` = ' . (int) $_SESSION['tickets']['assigned-to'] .' )';
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