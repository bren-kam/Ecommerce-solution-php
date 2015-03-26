<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 17/03/15
 * Time: 15:12
 */

class CustomerSupportController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->view_base = 'customer-support/';
        $this->section = 'customer-support';
    }

    public function index() {
        $this->resources->css('customer-support/index')
            ->css_url( Config::resource('bootstrap-select-css') )
            ->javascript('customer-support/index')
            ->javascript_url( Config::resource('bootstrap-select-js'), Config::resource('typeahead-js'), Config::resource('handlebars-js') );

        $admin_users = $this->user->get_admin_users();

        return $this->get_template_response('index')
            ->menu_item('customer-support')
            ->add_title('Customer Support')
            ->set( compact('admin_users') );
    }

    /**
     * List All
     *
     * @return AjaxResponse
     */
    protected function list_all()
    {
        // Get response
        $response = new AjaxResponse($this->verified());

        // Search -- We will do this on top DataTables, but return an AjaxResponse
        $_GET['sSearch'] = $_GET['search'];  // DataTables needs this
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = '0';
        $_GET['sSortDir_0'] = 'DESC';
        $dt = new DataTableResponse($this->user);
        $dt->order_by('date_created');
        $dt->search(array('b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true, 'a.`message`', 'b.`email`' => true));

        // If they are below 8, that means they are a partner
        if (!$this->user->has_permission(User::ROLE_ADMIN))
            $dt->add_where(' AND ( c.`company_id` = ' . (int)$this->user->company_id . ' OR a.`user_id` = ' . (int)$this->user->id . ' )');

        $status = (isset($_GET['status'])) ? (int)$_GET['status'] : 0;

        // Grab only the right status
        if ( $status >= 0 ) {
            $dt->add_where(" AND a.`status` = $status");
        }

        // Grab only the right status
        if ('-1' == $_GET['assigned-to']) {
            $dt->add_where(' AND c.`role` <= ' . (int)$this->user->role);
        } else if ($_GET['assigned-to'] > 0) {
            $assigned_to = ($this->user->has_permission(User::ROLE_SUPER_ADMIN)) ? ' AND c.`user_id` = ' . (int)$_GET['assigned-to'] : ' AND ( b.`user_id` = ' . (int)$_GET['assigned-to'] . ' OR c.`user_id` = ' . (int)$_GET['assigned-to'] . ' )';
            $dt->add_where($assigned_to);
        }

        /**
         * Create ticket class
         */
        $ticket = new Ticket();
        $tickets = $ticket->list_all($dt->get_variables());

        $data = [];
        foreach ($tickets as $ticket) {
            $date = new DateTime($ticket->date_created);
            $data[] = [
                'id' => $ticket->id
                , 'user_name' => $ticket->name
                , 'user_email' => $ticket->email
                , 'summary' => substr($ticket->summary, 0, 40)
                , 'intro_text' => substr(str_replace("\n", " ", strip_tags($ticket->message)), 0, 40)
                , 'priority' => $ticket->priority
                , 'status' => $ticket->status
                , 'date_created' => strtoupper($date->format('d-M'))
            ];
        }

        $response->add_response( 'tickets', $data );

        return $response;
    }

    protected function get() {
        $response = new AjaxResponse($this->verified());

        if ( $_GET['id'] ) {
            $ticket = new Ticket();
            $comment = new TicketComment();
            $tu = new TicketUpload();

            // Ticket --
            $ticket->get($_GET['id']);

            $ticket->created_ago = DateHelper::time_elapsed( $ticket->date_created );
            $ticket->updated_ago = 'Never';
            if ( $ticket->last_updated_at ) {
                $ticket->updated_ago = DateHelper::time_elapsed( $ticket->last_updated_at ) . ' by ' . $ticket->last_updated_by;
            }

            // Ticket Attachments --
            $ticket_uploads = $tu->get_by_ticket( $ticket->id );
            $uploads = [];
            foreach( $ticket_uploads as $ticket_upload ) {
                $uploads[] = [
                    'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $ticket_upload->key
                    , 'name' => ucwords( str_replace( '-', ' ', f::name( $ticket_upload->key ) ) )
                ];
            }

            // Comments --
            $comments = [];
            $comment_array = $comment->get_by_ticket($ticket->id);
            $comment_uploads = $tu->get_by_comments($ticket->id);

            foreach ( $comment_array as $comment ) {
                $comment->created_ago = DateHelper::time_elapsed( $comment->date_created );
                $comment->uploads = [];
                $comments[$comment->ticket_comment_id] = $comment;
            }

            foreach ( $comment_uploads as $comment_upload ) {
                $comments[$comment_upload->ticket_comment_id]->uploads[] = [
                    'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $comment_upload->key
                    , 'name' => ucwords( str_replace( '-', ' ', f::name( $comment_upload->key ) ) )
                ];
            }

            $response->add_response('ticket', $ticket);
            $response->add_response('uploads', $ticket_uploads);
            $response->add_response('comments', $comments);
        }

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
     * Add a comment
     *
     * @return AjaxResponse
     */
    protected function add_comment() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['comment'] ) && isset( $_POST['ticket-id'] ), _('Failed to add comment') );

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
        $ticket->get( $_POST['ticket-id'] );

        // Get users
        $ticket_creator->get( $ticket->user_id );
        $assigned_user->get( $ticket->assigned_to_user_id );

        // Set variables
        $status = '(Open)';
        if ( Ticket::STATUS_IN_PROGRESS == $ticket->status )
            $status = '(Open)';
        if ( Ticket::STATUS_CLOSED == $ticket->status )
            $status = '(Closed)';

        // Create ticket comment
        $ticket_comment->ticket_id = $ticket->id;
        $ticket_comment->user_id = $this->user->user_id;
        $ticket_comment->to_address = $_POST['to-address'];
        $ticket_comment->cc_address = $_POST['cc-address'];
        $ticket_comment->bcc_address = $_POST['bcc-address'];
        $ticket_comment->comment = trim($_POST['comment']);
        $ticket_comment->private = (int) isset( $_POST['private'] );

        $ticket_comment->create();

        // If they changed the To Address, we need to update Ticket Primary Contact
        if ( $ticket_comment->to_address != $ticket->email ) {
            $primary_contact = new User();
            $primary_contact->get_by_email($ticket_comment->to_address);
            if ( !$primary_contact->id ) {
                $primary_contact->email = $ticket_comment->to_address;
                $primary_contact->status = User::STATUS_ACTIVE;
                $primary_contact->role = User::ROLE_AUTHORIZED_USER;
                $primary_contact->company_id = $this->user->company_id;
                $primary_contact->create();
            }
            $ticket->user_id = $primary_contact->id;
            $ticket->save();
        }

        // Make ticket as In Progress
        if ( $ticket->status == Ticket::STATUS_OPEN ) {
            $ticket->status = Ticket::STATUS_IN_PROGRESS;
            $ticket->save();
        }

        // Handle attachments
        if ( isset( $_POST['uploads'] ) && is_array( $_POST['uploads'] ) )
            $ticket_upload->add_comment_relations( $ticket_comment->id, $_POST['uploads'] );

        // If it's not private, send an email to the client
        if ( TicketComment::VISIBILITY_PUBLIC == $ticket_comment->private && Ticket::STATUS_CLOSED != $ticket->status )
            fn::mail(
                $ticket_comment->to_address
                , 'New Comment on Ticket #' . $ticket->id . $status . ' - ' . $ticket->summary
                , "******************* Reply Above This Line *******************"
                    . "\n\n<br><br>{$this->user->contact_name} has posted a new comment on Ticket #{$ticket->id}."
                    . "\n\n<br><br>{$ticket_comment->comment}"
                    . "\n\n<br><br>**Support Issue**"
                    . "\n<br>{$ticket->message}"
                , $ticket_creator->company . ' <support@' . url::domain( $ticket_creator->domain, false ) . '>'
                , $this->user->contact_name . ' <' . $this->user->email . '>'
                , false
                , false
                , $ticket_comment->cc_address
                , $ticket_comment->bcc_address
            );
        // Send the assigned user an email if they are not submitting the comment
        if ( $ticket->assigned_to_user_id != $this->user->id && $ticket->assigned_to_user_id != $ticket->user_id ) {
            fn::mail(
                $assigned_user->email
                , 'New Comment on Ticket #' . $ticket->id . $status . ' - ' . $ticket->summary
                , "******************* Reply Above This Line *******************"
                    . "\n\n<br><br>{$this->user->contact_name} has posted a new comment on Ticket #{$ticket->id}."
                    . "\n\n<br><br>{$ticket_comment->comment}"
                    . "\n\n<br><br>**Support Issue**"
                    . "\n<br>{$ticket->message}"
                , $ticket_creator->company . ' <support@' . url::domain($ticket_creator->domain, false) . '>'
                , $this->user->contact_name . ' <' . $this->user->email . '>'
                , false
                , false
            );
        }

        if ( $ticket->jira_id ) {
            $ticket_comment->create_jira_comment();
        }

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
        $message .= 'http://admin.' . url::domain( $assigned_user->domain, false ) . '/tickets/ticket/?tid=' . $ticket->id . "\n\n";
        $message .= 'Priority: ' . $priorities[$ticket->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        fn::mail( $assigned_user->email, 'You have been assigned Ticket #' . $ticket->id . ' (' . $priorities[$ticket->priority] . ') - ' . $ticket->summary, $message, $assigned_user->company . ' <noreply@' . url::domain( $assigned_user->domain, false ) . '>' );

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
     * AutoComplete
     *
     * @return AjaxResponse
     */
    protected function get_emails() {
        $ajax_response = new AjaxResponse( $this->verified() );

        $user = new User();
        $users = $user->list_all([" AND (u.email LIKE '%{$_GET['term']}%' OR u.contact_name LIKE '%{$_GET['term']}%' OR w.title LIKE '%{$_GET['term']}%') ", '', '', 9999]);

        $results = [];
        foreach ( $users as $user ) {
            $results[] = [
                'id' => $users->id
                , 'contact_name' => $user->contact_name
                , 'email' => $user->email
                , 'main_website' => $user->main_website
            ];
        }

        $ajax_response->add_response( 'objects', $results );
        return $ajax_response;
    }

    /**
     * Create ticket
     *
     * @return AjaxResponse
     */
    protected function create_ticket() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $ticket = new Ticket();

        if ( isset( $_POST['ticket-id'] ) && !empty( $_POST['ticket-id'] ) ) {
            $ticket->get( $_POST['ticket-id'] );
        } else {
            $ticket->status = 0;
            $ticket->create();
        }

        $user = new User();
        $user->get_by_email($_POST['to']);
        if ( !$user->id ) {
            $user->email = $_POST['to'];
            $user->status = User::STATUS_ACTIVE;
            $user->role = User::ROLE_AUTHORIZED_USER;
            $user->company_id = $this->user->company_id;
            $user->create();
        }

        // Get browser information
        $browser = fn::browser();

        // Set ticket information
        $ticket->user_id = $user->id;
        $ticket->assigned_to_user_id = $this->user->id;  // Send a message, but assign ticket to self
        $ticket->website_id = 0; // Admin side -- no website
        $ticket->summary = $_POST['summary'];
        $ticket->message = trim($_POST['message']);
        $ticket->browser_name = $browser['name'];
        $ticket->browser_version = $browser['version'];
        $ticket->browser_platform = $browser['platform'];
        $ticket->browser_user_agent = $browser['user_agent'];
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->priority = Ticket::PRIORITY_NORMAL;

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

        $response->notify( "Message Created" );
        $response->add_response('id', $ticket->id);
        return $response;
    }



}