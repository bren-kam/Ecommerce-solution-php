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

        $admin_users = $this->user->get_admin_users(User::$support_team);
        $account = new Account();
        $accounts = $account->list_all([' AND a.`status` = 1', '', ' ORDER BY a.`title` ', 9999]);

        return $this->get_template_response('index')
            ->menu_item('customer-support')
            ->add_title('Customer Support')
            ->set( compact('admin_users', 'accounts') );
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
        $dt->order_by('last_updated_at');
        $dt->search(array('a.`ticket_id`' => true, 'b.`contact_name`' => true, 'd.`title`' => true, 'a.`summary`' => true, 'a.`message`', 'b.`email`' => true));

        // If they are below 8, that means they are a partner
        if (!$this->user->has_permission(User::ROLE_ADMIN))
            $dt->add_where(' AND ( c.`company_id` = ' . (int)$this->user->company_id . ' OR a.`user_id` = ' . (int)$this->user->id . ' )');

        // Grab only the right user
        if ('-1' == $_GET['assigned-to']) {
            $dt->add_where(' AND c.`role` <= ' . (int)$this->user->role);
        } else if ($_GET['assigned-to'] > 0) {
            if ( $this->user->has_permission( User::ROLE_SUPER_ADMIN ) ) {
                $dt->add_where(" AND ( {$_GET['assigned-to']} IN (a.`user_id_created`, a.`assigned_to_user_id`, a.`user_id`) ) ");
            } else {
                $dt->add_where(" AND ( {$_GET['assigned-to']} IN (a.`user_id_created`, a.`assigned_to_user_id`, a.`user_id`, d.`os_user_id`) ) ");
            }
        }

        $status = (isset($_GET['status'])) ? (int)$_GET['status'] : 0;
        $status_condition_with_user = $_GET['assigned-to'] > 0;
        // Grab status
        if ( $status == -2 ) {
            $dt->add_where(" AND a.`status` IN (0, 2) " . ($status_condition_with_user ? "AND ({$_GET['assigned-to']} != a.`assigned_to_user_id`) " : ""));
        } else if ( $status == -1 ) {
            // show open and in progress
            $dt->add_where(" AND a.`status` IN (0, 2) " . ($status_condition_with_user ? "AND ({$_GET['assigned-to']} = a.`assigned_to_user_id`) " : ""));
        } else if ( $status >= 0 ) {
            $dt->add_where(" AND a.`status` = $status " . ($status_condition_with_user ? "AND ({$_GET['assigned-to']} = a.`assigned_to_user_id`) " : ""));
        }

        if ( $_GET['account'] ) {
            $dt->add_where(' AND d.`website_id` = ' . (int)$_GET['account']);
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
                , 'summary' => substr($ticket->summary . ($ticket->website ? " | {$ticket->website}" : ''), 0, 50)
                , 'intro_text' => substr(str_replace("\n", " ", strip_tags($ticket->message)), 0, 40)
                , 'priority' => $ticket->priority
                , 'status' => $ticket->status
                , 'date_created' => strtoupper($date->format('d-M'))
                , 'user_id_created' => $ticket->user_id_created
                , 'assigned_to_user_id' => $ticket->assigned_to_user_id
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

            $date_created = new DateTime($ticket->date_created);
            $ticket->created_ago = $date_created->format("D n/j/Y g:i A");
            $ticket->updated_ago = 'Never';
            if ( $ticket->last_updated_at ) {
                $ticket->updated_ago = DateHelper::time_elapsed( $ticket->last_updated_at ) . ' by ' . $ticket->last_updated_by;
            }

            // Check if user is attached to an account
            $user = new User();
            $user->get($ticket->user_id);
            if ( $user->id ) {
                $account = new Account();
                $accounts = $account->get_by_user($user->id);
                if ( empty($accounts) ) {
                    $accounts = $account->get_by_authorized_user($user->id);
                }
                $ticket->user_has_account = !empty($accounts);
            }

            // Ticket Attachments --
            $ticket_uploads = $tu->get_by_ticket( $ticket->id );
            $uploads = [];
            foreach( $ticket_uploads as $ticket_upload ) {
                $uploads[] = [
                    'link' => 'http://s3.amazonaws.com/retailcatalog.us/attachments/' . $ticket_upload
                    , 'name' => ucwords( str_replace( '-', ' ', f::name( $ticket_upload ) ) )
                ];
            }

            // Comments --
            $comments = [];
            $comment_array = $comment->get_by_ticket($ticket->id);
            $comment_uploads = $tu->get_by_comments($ticket->id);

            foreach ( $comment_array as $comment ) {
                $date_created = new DateTime($comment->date_created);
                $comment->created_ago = $date_created->format("D n/j/Y g:i A");
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
            $response->add_response('uploads', $uploads);
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
    protected function add_comment()
    {
        // Verify the nonce
        $response = new AjaxResponse($this->verified());

        // Make sure we have the proper parameters
        $response->check(isset($_POST['comment']) && isset($_POST['ticket-id']), _('Failed to add comment'));

        // If there is an error or now user id, return
        if ($response->has_error())
            return $response;

        // Initialize objects
        $ticket = new Ticket();
        $ticket_comment = new TicketComment();
        $ticket_creator = new User();
        $assigned_user = new User();
        $ticket_upload = new TicketUpload();

        // Get ticket
        $ticket->get($_POST['ticket-id']);

        // Get users
        $ticket_creator->get($ticket->user_id);
        $assigned_user->get($ticket->assigned_to_user_id);

        // Create ticket comment
        $ticket_comment->ticket_id = $ticket->id;
        $ticket_comment->user_id = $this->user->user_id;
        $ticket_comment->to_address = $_POST['to-address'];
        $ticket_comment->cc_address = $_POST['cc-address'];
        $ticket_comment->bcc_address = $_POST['bcc-address'];
        $ticket_comment->comment = trim($_POST['comment']);
        $ticket_comment->private = (int)isset($_POST['private']);

        $ticket_comment->create();

        // If they changed the To Address, we need to update Ticket Primary Contact
        if ($ticket_comment->to_address != $ticket->email) {
            $primary_contact = new User();
            $primary_contact->get_by_email($ticket_comment->to_address);
            if (!$primary_contact->id) {
                $primary_contact->email = $ticket_comment->to_address;
                $primary_contact->status = User::STATUS_ACTIVE;
                $primary_contact->role = User::ROLE_AUTHORIZED_USER;
                $primary_contact->company_id = COMPANY_ID;
                $primary_contact->create();
            }
            $ticket->user_id = $primary_contact->id;
            $ticket->save();
        }

        // Make ticket as In Progress
        if ($ticket->status == Ticket::STATUS_OPEN) {
            $ticket->status = Ticket::STATUS_IN_PROGRESS;
            $ticket->save();
        }

        // Handle attachments
        if (isset($_POST['uploads']) && is_array($_POST['uploads']))
            $ticket_upload->add_comment_relations($ticket_comment->id, $_POST['uploads']);

        $status = '(Open)';
        if (Ticket::STATUS_IN_PROGRESS == $ticket->status) {
            $status = '(In Progress)';
        } else if (Ticket::STATUS_CLOSED == $ticket->status) {
            $status = '(Closed)';
        }

        $thread = '';
        if ( $_POST['include-whole-thread'] ) {
            $comments = $ticket_comment->get_by_ticket($ticket->id);
            array_shift($comments);
            foreach ( $comments as $c ) {
                if ( $c->private == TicketComment::VISIBILITY_PUBLIC ) {
                    $date = new DateTime($c->date_created);
                    $date_str = $date->format("D n/j/Y g:i A");
                    $thread .= "\n\n<br><br>On {$date_str} {$c->name} wrote:\n<br>{$c->comment}";

                    $uploads = $ticket_upload->get_by_comment($c->ticket_comment_id);
                    foreach ( $uploads as $upload ) {
                        $link = "http://s3.amazonaws.com/retailcatalog.us/attachments/{$upload->key}";
                        $name = ucwords( str_replace( '-', ' ', f::name( $upload->key ) ) );
                        $thread .= "\n<br><a href=\"{$link}\">{$name}</a>";
                    }
                }
            }
            $date = new DateTime($ticket->date_created);
            $date_str = $date->format("D n/j/Y g:i A");
            $thread .= "\n\n<br><br>On {$date_str} {$ticket->name} wrote:\n<br>{$ticket->message}";

            $uploads = $ticket_upload->get_by_ticket($ticket->id);
            foreach ( $uploads as $upload ) {
                $link = "http://s3.amazonaws.com/retailcatalog.us/attachments/{$upload}";
                $name = ucwords( str_replace( '-', ' ', f::name( $upload ) ) );
                $thread .= "\n<br><a href=\"{$link}\">{$name}</a>";
            }
        }

        $attachments = '';
        if (isset( $_POST['upload-names'] )) {
            foreach ( $_POST['upload-names'] as $un ) {
                $attachments .= "\n<br><a href=\"{$un['url']}\">{$un['name']}</a>";
            }
        }

        // Signature
        $ticket_user = new User();
        $ticket_user->get($ticket->user_id);

        $os_domain_email = str_replace( strstr( $this->user->email, '@'), '@' . $ticket_user->domain, $this->user->email );
        $signature  = '<br><p style="font-size: 12px;">'. $this->user->contact_name .'<br>';
        $signature .= '<span style="font-size: 10px;">';
        if ( $this->user->work_phone ) {
            $signature .= $this->user->work_phone .'<br>';
        }
        $signature .= $os_domain_email .'</span>';
        $signature .= '</p>';
        $signature .= '<p style="height:35px;"><img style="height:35px;" src="http://admin.greysuitretail.com/images/logos/'.$ticket_user->domain.'.png" /></p>';

        // Send emails only for public comments
        $send_email = $ticket_comment->private == TicketComment::VISIBILITY_PUBLIC;
        // GSR System email addresses should not get any email
        $send_email = $send_email && strpos($ticket_comment->to_address, '@imagineretailer') === FALSE;
        $send_email = $send_email && strpos($ticket_comment->to_address, '@greysuitretail') === FALSE;
        $send_email = $send_email && strpos($ticket_comment->to_address, '@blinkyblinky') === FALSE;

        // If it's not private, send an email to the client
        if ( $send_email ) {
            library('sendgrid-api');

            SendgridApi::send(
                $ticket_comment->to_address
                , $ticket->summary . ' - Ticket #' . $ticket->id . ' ' . $status
                , "{$ticket_comment->comment}"
                    . "{$attachments}"
                    . "{$signature}"
                    . "{$thread}"
                , $this->user->contact_name . ' <' . $os_domain_email . '>'
                , $this->user->contact_name . ' <' . $os_domain_email . '>'
                , false
                , false
                , $ticket_comment->cc_address
                , $ticket_comment->bcc_address
            );

//            // Send the assigned user an email if they are not submitting the comment
//            if ( $ticket->assigned_to_user_id != $this->user->id && $ticket->assigned_to_user_id != $ticket->user_id ) {
//                library('sendgrid-api'); SendgridApi::send(
//                    $assigned_user->email
//                    , $ticket->summary . ' - Ticket #' . $ticket->id . ' ' . $status
//                    , "{$ticket_comment->comment}"
//                        . "{$attachments}"
//                        . "{$signature}"
//                        . "{$thread}"
//                    , $this->user->contact_name . ' <' . $os_domain_email . '>'
//                    , $this->user->contact_name . ' <' . $os_domain_email . '>'
//                    , false
//                    , false
//                );
//            }
        }

        if ( $ticket->jira_id ) {
            $ticket_comment->create_jira_comment();
        }

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

        $assigned_user = new User();
        $assigned_user->get( $_POST['auid'] );
        $response->check( $assigned_user->has_permission( User::ROLE_COMPANY_ADMIN ), 'Can not assign ticket to ' . $assigned_user->contact_name );
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

        // Send out an email if their role is less than 8
        $message = 'Hello ' . $assigned_user->contact_name . ",\n\n";
        $message .= 'You have been assigned Ticket #' . $ticket->id . ". To view it, follow the link below:\n\n";
        $message .= 'http://admin.' . url::domain( $assigned_user->domain, false ) . '/customer-support/#!tid=' . $ticket->id . "\n\n";
        $message .= 'Priority: ' . $priorities[$ticket->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        library('sendgrid-api');
        SendgridApi::send( $assigned_user->email, 'You have been assigned Ticket #' . $ticket->id . ' (' . $priorities[$ticket->priority] . ') - ' . $ticket->summary, $message, $assigned_user->company . ' <noreply@' . url::domain( $assigned_user->domain, false ) . '>' );

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
     * Update Summary
     *
     * @return AjaxResponse
     */
    protected function update_summary() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['tid'] ) && isset( $_POST['summary'] ), _('Failed to update summary') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket
        $ticket = new Ticket();
        $ticket->get( $_POST['tid'] );

        // Change priority
        $ticket->summary = $_POST['summary'];

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
        $user->get_by_email($_POST['to'], false);
        if ( $user->id && $user->status == User::STATUS_INACTIVE ) {
            $user->status = User::STATUS_ACTIVE;
            $user->save();
        } else if ( !$user->id ) {
            $user->email = $_POST['to'];
            $user->status = User::STATUS_ACTIVE;
            $user->role = User::ROLE_AUTHORIZED_USER;
            $user->company_id = COMPANY_ID;
            $user->create();
        }

        // Try to guess the Account
        $account = new Account();
        $accounts = $account->get_by_user( $user->id );
        if ( $accounts ) {
            $account = reset($accounts);
        } else {
            $accounts = $account->get_by_authorized_user( $user->id );
            $account = reset($accounts);
        }

        // Get browser information
        $browser = fn::browser();

        // Set ticket information
        $ticket->user_id = $user->id;
        $ticket->assigned_to_user_id = $this->user->id;
        $ticket->user_id_created = $this->user->id;
        $ticket->website_id = $account->website_id; // Admin side -- no website
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

    public function attach_user_to_account() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['tid'] ) && isset( $_POST['account_id'] ), _('Failed to update Account') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket
        $ticket = new Ticket();
        $ticket->get( $_POST['tid'] );

        $user = new User();
        $user->get($ticket->user_id);

        $account = new Account();
        $account->get($_POST['account_id']);

        $auth_user_website = new AuthUserWebsite();
        $auth_user_website->add(
            $user->contact_name
            , $user->email
            , $account->id
            , 0
            , 0
            , 0
            , 0
            , 0
            , 0
            , User::ROLE_AUTHORIZED_USER
            , false
        );

        $ticket->website_id = $_POST['account_id'];
        $ticket->save();

        $response->notify("User {$user->email} attached to {$account->title}.");
        return $response;
    }



}