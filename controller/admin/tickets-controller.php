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

        $this->resources->css( 'tickets/index' )
            ->javascript( 'tickets/index' );

        return $this->get_template_response( 'index' )
            ->kb( 24 )
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
        if ( $this->user->role < $ticket->role && $this->user->user_id != $ticket->user_id && $this->user->user_id != User::JEFF )
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

        $this->resources
            ->css( 'tickets/ticket' )
            ->javascript( 'fileuploader', 'jquery.autoresize', 'tickets/ticket' );

        return $this->get_template_response( 'ticket', _('Ticket') )
            ->kb( 25 )
            ->add_title( format::limit_chars( $ticket->summary, 30 ) )
            ->set( compact( 'ticket', 'ticket_uploads', 'comments', 'admin_users' ) );
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
            User::DAVID => '@products'
            , User::DAVID => '@accounting'
        );

        // Special hash priorities
        $priorities = array(
            Ticket::PRIORITY_HIGH => '#high' // high priority
            , Ticket::PRIORITY_URGENT => '#urgent' // urgent priority
        );

        $variables = array(
            $_POST['tTicketSummary']
            , $_POST['taTicketMessage']
        );

        // Figure out who we're assigned to
        $assigned_to_user_id = User::TECHNICAL;
        $priority = Ticket::PRIORITY_NORMAL;

        // Find out if they are trying to direct it to a particular person
        foreach ( $variables as $string ) {
            $string = strtolower( $string );

            if ( User::TECHNICAL == $assigned_to_user_id )
            foreach ( $words as $user_id => $word ) {
                if ( stristr( $string, $word ) ) {
                    // Found it -- we're done here
                    $assigned_to_user_id = $user_id;
                    break;
                }
            }

            if ( Ticket::PRIORITY_NORMAL == $priority )
            foreach ( $priorities as $priority_id => $hash ) {
                if ( stristr( $string, $hash ) ) {
                    // Found it -- we're done here
                    $priority = $priority_id;
                    break;
                }
            }
        }

        $topic_assignation_map = [
            'accounting' => User::DAVID
            , 'design' => User::DESIGN_TEAM
            , 'development' => User::TECHNICAL
            , 'bug' => User::TECHNICAL
            , 'feature-request' => User::TECHNICAL
        ];

        if ( isset($_POST['tTicketTopic']) && isset($topic_assignation_map[$_POST['tTicketTopic']]) ) {
            $assigned_to_user_id = $topic_assignation_map[$_POST['tTicketTopic']];
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
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->priority = $priority;
        $ticket->user_id_created = $this->user->id;

        // Update the ticket
        $ticket->save();

        // Add links if there are any
        if ( isset( $_POST['uploads'] ) && is_array( $_POST['uploads'] ) ) {
            $ticket_upload = new TicketUpload();
            $ticket_upload->add_relations( $ticket->id, $_POST['uploads'] );
        }

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

        library('sendgrid-api'); SendgridApi::send(
            $assigned_user->email
            , 'New ' . $assigned_user->company . ' Ticket - ' . $ticket->summary
            , "Name: " . $this->user->contact_name
                . "\nEmail: " . $this->user->email
                . "\nSummary: " . $ticket->summary
                . "\n\n" . str_replace( array("<br>", "<br />", "<br/>"), "\n", $ticket->message )
                . "\n\nhttp://admin." . $assigned_user->domain . "/tickets/ticket/?tid=" . $ticket->id
        );

        $response->notify( 'Your message has been sent, we will contact you soon.' );
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
        $status = ( Ticket::STATUS_OPEN == $ticket->status ) ? ' (Open)' : ' (Closed)';

        // Create ticket comment
        $ticket_comment->ticket_id = $ticket->id;
        $ticket_comment->user_id = $this->user->user_id;
        $ticket_comment->comment = nl2br( format::links_to_anchors( format::htmlentities( format::convert_characters( $_POST['comment'] ), array('&') ), true, true ) );
        $ticket_comment->private = (int) isset( $_POST['private'] );

        $ticket_comment->create();

        // Handle attachments
        if ( isset( $_POST['uploads'] ) && is_array( $_POST['uploads'] ) )
            $ticket_upload->add_comment_relations( $ticket_comment->id, $_POST['uploads'] );

        // Send emails
        $comment = strip_tags( $ticket_comment->comment );

        // If it's not private, send an email to the client
        if ( TicketComment::VISIBILITY_PUBLIC == $ticket_comment->private && ( Ticket::STATUS_OPEN == $ticket->status || !$ticket_creator->has_permission( User::ROLE_ADMIN ) ) )
            library('sendgrid-api'); SendgridApi::send(
                $ticket->email
                , 'Ticket #' . $ticket->id . $status . ' - ' . $ticket->summary
                , "******************* Reply Above This Line *******************"
                  . "\n\n" . $this->user->contact_name . ' has posted a new comment on Ticket #' . $ticket->id . "."
                    . "\n\n{$comment}"
                    . "\n\n**Support Issue**"
                    . "\n" . str_replace( array("<br>", "<br />", "<br/>"), "\n", $ticket->message )
                , $ticket_creator->company . ' <support@' . url::domain( $ticket_creator->domain, false ) . '>'
            );

        // Send the assigned user an email if they are not submitting the comment
        if ( $ticket->assigned_to_user_id != $this->user->id && $ticket->assigned_to_user_id != User::KERRY && $ticket->assigned_to_user_id != $ticket->user_id )
            library('sendgrid-api'); SendgridApi::send(
                $assigned_user->email
                , 'New Comment on Ticket #' . $ticket->id . $status .' - ' . $ticket->summary
                , "******************* Reply Above This Line *******************"
                    . "\n\n" . $this->user->contact_name . ' has posted a new comment on Ticket #' . $ticket->id . "."
                    . "\n\nhttp://admin." . url::domain( $assigned_user->domain, false ) . "/tickets/ticket/?tid=" . $ticket->id
                    . "\n\n**Comment**"
                    . "\n{$comment}"
                    . "\n\n**Support Issue**"
                    . "\n" . str_replace( array("<br>", "<br />", "<br/>"), "\n", $ticket->message )
                , $assigned_user->company . ' <support@' . url::domain( $assigned_user->domain, false ) . '>'
            );

        /***** Add comment *****/
        $uploads = $ticket_upload->get_by_comment( $ticket_comment->id );

        $response->add_response( 'id', $ticket_comment->id );
        $response->add_response( 'contact_name', $this->user->contact_name );
        $response->add_response( 'user_id', $this->user->id );
        $response->add_response( 'comment', $ticket_comment->comment );
        $response->add_response( 'private', $ticket_comment->private );

        if ( !empty( $uploads ) ) {
            $response_uploads = array();
            foreach ( $uploads as $upload ) {
                $response_uploads[] = array(
                    'link' => "http://s3.amazonaws.com/retailcatalog.us/attachments/{$upload->key}"
                    , 'name' => f::name( $upload->key )
                );
            }
            $response->add_response( 'uploads', $response_uploads );
        }

        if ( $ticket->jira_id )
            $ticket_comment->create_jira_comment();

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
        $uploader = new qqFileUploader( array('pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt', 'eml'), 10485760 );

        if ( !isset( $_GET['tid'] ) || empty( $_GET['tid'] ) ) {
            $ticket->status = -1;
            $ticket->create();

            // Set the variable
            $response->add_response('ticket_id', $ticket->id);
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

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        $response->add_response( 'url', $file_url );
        $response->add_response( 'id', $ticket_upload->id );

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

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        $response->add_response( 'id', $ticket_upload->id );
        $response->add_response( 'url', $file_url );

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
                $upload->remove();
            }
        }

        // Then delete ticket
        $ticket_comment->remove();

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
        $ticket_upload->remove();

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
            Ticket::PRIORITY_NORMAL => 'Normal',
            Ticket::PRIORITY_HIGH => 'High',
            Ticket::PRIORITY_URGENT => 'Urgent'
        );

        $assigned_user = new User();
        $assigned_user->get( $_POST['auid'] );

        // Send out an email if their role is less than 8
        $message = 'Hello ' . $assigned_user->contact_name . ",\n\n";
        $message .= 'You have been assigned Ticket #' . $ticket->id . ". To view it, follow the link below:\n\n";
        $message .= 'http://admin.greysuitretail.com/tickets/ticket/?tid=' . $ticket->id . "\n\n";
        $message .= 'Priority: ' . $priorities[$ticket->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        library('sendgrid-api'); SendgridApi::send( $assigned_user->email, 'You have been assigned Ticket #' . $ticket->id . ' (' . $priorities[$ticket->priority] . ') - ' . $ticket->summary, $message, $assigned_user->company . ' <noreply@greysuitretail.com>' );

        // If assigned to Development, make sure it's on Jira
        if ( $assigned_user->id == User::DEVELOPMENT ) {
            if ( $ticket->jira_id ) {
                $ticket->update_jira_issue();
            } else {
                $ticket->create_jira_issue();

                $ticket_comment = new TicketComment();
                $comments = $ticket_comment->get_by_ticket( $ticket->id );
                if ( $comments ) {
                    foreach ( $comments as $comment ) {
                        $comment->create_jira_comment();
                    }
                }
            }
        }

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
        if ( Ticket::STATUS_CLOSED == $ticket->status && in_array( $this->user->id, array( User::TECHNICAL, User::KERRY, User::RODRIGO, User::MANINDER, User::RAFFERTY ) ) ) {
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
        $dt->order_by( 'a.`summary`', 'name', 'd.`title`', 'a.`priority`', 'assigned_to', 'a.`date_created`', 'last_updated_at' );
        $dt->search( array( 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true ) );
        $dt->add_where( $where = ' AND ( ' . $this->user->role . ' >= COALESCE( c.`role`, 7 ) OR a.`user_id` = ' . $this->user->id . ' )' );

        // If they are below 8, that means they are a partner
		if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
			$dt->add_where( ' AND ( c.`company_id` = ' . (int) $this->user->company_id . ' OR a.`user_id` = ' . (int) $this->user->id . ' )' );

        $status = ( isset( $_SESSION['tickets']['status'] ) ) ? (int) $_SESSION['tickets']['status'] : 0;

        // Grab only the right status
        if ( $status == Ticket::STATUS_OPEN ) {
            $dt->add_where( " AND a.`status` IN (". Ticket::STATUS_OPEN .", ". Ticket::STATUS_IN_PROGRESS .")" );
        } else {
            $dt->add_where( " AND a.`status` = $status" );
        }

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
                case Ticket::PRIORITY_NORMAL:
                    $priority = '<span class="label label-default">NORMAL</span>';
                break;

                case Ticket::PRIORITY_HIGH:
                    $priority = '<span class="label label-warning">HIGH</span>';
                break;

                case Ticket::PRIORITY_URGENT:
                    $priority = '<span class="label label-danger">URGENT</span>';
                break;
            }

            $date = new DateTime( $ticket->date_created );

            $last_updated = 'Never';
            if ( $ticket->last_updated_at ) {
                $last_updated = DateHelper::time_elapsed( $ticket->last_updated_at ) . ' by ' . $ticket->last_updated_by;
            }

            $data[] = array(
                '<a href="/tickets/ticket/?tid=' . $ticket->id . '" title="' . _('View Ticket') . '">' . format::limit_chars( $ticket->summary, 55 ) . '</a>'
                , $ticket->name
                , $ticket->website
                , $priority
                , $ticket->assigned_to
                , $date->format('F j, Y')
                , $last_updated
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Archive Old
     */
    protected  function archive_old() {
        $ticket = new Ticket();
        $old_tickets = $ticket->get_old();

        foreach ( $old_tickets as $ticket ) {
            // Add Closed message
            $ticket_comment = new TicketComment();
            $ticket_comment->ticket_id = $ticket->id;
            $ticket_comment->user_id = User::TECHNICAL;
            $ticket_comment->comment = 'This ticket is being closed due to inactivity past 30 days. If you need furher help with this issue, please do not hesitate to contact us.<br /><br />Technical Team';
            $ticket_comment->private = 0;

            $ticket_comment->create();

            // Close Ticket
            $ticket->status = Ticket::STATUS_CLOSED;
            $ticket->save();

            // Send email
            $assigned_user = new User();
            $assigned_user->get( $ticket->assigned_to_user_id );
            if ( !$assigned_user->id )
                continue;

            // Send the assigned user an email if they are not submitting the comment
            library('sendgrid-api'); SendgridApi::send(
                $ticket->email
                , 'New Comment on Ticket #' . $ticket->id . ' (Closed) - ' . $ticket->summary
                , "******************* Reply Above This Line *******************"
                . "\n\n" . $this->user->contact_name . ' has posted a new comment on Ticket #' . $ticket->id . "."
                . "\n\nhttp://admin.greysuitretail.com/tickets/ticket/?tid=" . $ticket->id
                . "\n\n**Comment**"
                . "\nThis ticket is being closed due to inactivity past 30 days. If you need furher help with this issue, please do not hesitate to contact us.\n\nTechnical Team"
                . "\n\n**Support Issue**"
                . "\n" . str_replace( array("<br>", "<br />", "<br/>"), "\n", $ticket->message )
                , $assigned_user->company . ' <support@' . url::domain( $assigned_user->domain, false ) . '>'
            );

        }
    }
}