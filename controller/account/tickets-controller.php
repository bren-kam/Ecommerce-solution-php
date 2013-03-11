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
        $assigned_to_user_id = ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) ? 493 : $this->user->account->os_user_id; // Technical user (default)
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
        $ticket->website_id = $this->user->account->id;
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
}