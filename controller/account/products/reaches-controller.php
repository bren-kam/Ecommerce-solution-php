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
            ->kb( 54 )
            ->select( 'reaches' );
    }

    /**
     * Reach
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function reach() {
        // Make sure they can be here
        if ( !isset( $_GET['wrid'] ) )
            return new RedirectResponse('/products/reaches/');

        // Get reach
        $reach = new WebsiteReach();
        $reach->get( $_GET['wrid'], $this->user->account->id );
        $reach->get_meta();
        $reach->get_info();

        // Get comments
        $reach_comment = new WebsiteReachComment();
        $comments = $reach_comment->get_by_reach( $reach->id, $this->user->account->id );

        // Get assignable users
        $auth_user_website = new AuthUserWebsite();
        $assignable_users_array = $auth_user_website->get_by_account( $this->user->account->id );

        // Get account owner
        $user = new User();
        $user->get( $this->user->account->user_id );

        $assignable_users_array[] = $user;
        $assignable_users = array();

        foreach ( $assignable_users_array as $u ) {
            $assignable_users[$u->id] = $u;
        }

        $this->resources
            ->css( 'products/reaches/reach' )
            ->javascript( 'jquery.autoresize', 'products/reaches/reach' );

        return $this->get_template_response( 'reach' )
            ->kb( 55 )
            ->add_title( _('Reach') )
            ->select( 'reaches' )
            ->set( compact( 'reach', 'comments', 'assignable_users' ) );
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
        $dt->order_by( 'name', 'wu.`email`', 'assigned_to', 'waiting', 'wr.`priority`', 'wr.`date_created`' );
        $dt->add_where( " AND wr.`website_id` = " . $this->user->account->id );

        if ( !$this->user->has_permission( User::ROLE_STORE_OWNER ) )
            $dt->add_where( ' AND wr.`status` = 0' );

        $dt->search( array( 'wu.`billing_first_name`' => false, 'wu.`billing_last_name`' => false, 'wu.`email`' => false, 'u.`contact_name`' => false ) );

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

        $data = array();

        // Create output
        if ( is_array( $reaches ) )
        foreach ( $reaches as $reach ) {
            $date = new DateTime( $reach->date_created );

            $name = ( empty( $reach->name ) ) ? 'Anonymous' : $reach->name;

            $data[] = array(
                '<a href="' . url::add_query_arg( 'wrid', $reach->id, '/products/reaches/reach/' ) . '">' . $name . '</a>'
                , $reach->email
                , $reach->assigned_to
                , ( $reach->waiting ) ? 'Waiting' : 'Ready'
                , $priorities[ (int) $reach->priority ]
                , $date->format( 'F jS, Y g:ia' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
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
        $response->check( isset( $_POST['comment'] ) && isset( $_POST['hReachId'] ), _('Failed to add comment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $reach = new WebsiteReach();
        $reach_comment = new WebsiteReachComment();
        $assigned_user = new User();

        // Get ticket
        $reach->get( $_POST['hReachId'], $this->user->account->id );

        // Set variables
        $status = ( 0 == $reach->status ) ? ' (Open)' : ' (Closed)';

        // Create ticket comment
        $reach_comment->website_reach_id = $reach->id;
        $reach_comment->user_id = $this->user->user_id;
        $reach_comment->comment = nl2br( format::links_to_anchors( format::htmlentities( format::convert_characters( $_POST['comment'] ), array('&') ), true, true ) );
        $reach_comment->private = (int) isset( $_POST['private'] );

        $reach_comment->create();

        // Send emails
        $comment = strip_tags( $reach_comment->comment );

        // If it's not private, send an email to the website user
        if ( 0 == $reach_comment->private ) {
            // No longer waiting for us
            $reach->waiting = 0;
            $reach->save();

            fn::mail( $reach->email, $reach->get_friendly_type() . ' #' . $reach->id . $status, "******************* Reply Above This Line *******************\n\n{$comment}\n\n" . $reach->get_friendly_type() . "\n" . $reach->message, $this->user->account->title . ' <reaches@' . url::domain( $this->user->domain, false ) . '>' );
        }

        // Send the assigned user an email if they are not submitting the comment
        if ( $reach->assigned_to_user_id != $this->user->id && 1 == $reach->status ) {
            $assigned_user->get( $reach->assigned_to_user_id );
            fn::mail( $assigned_user->email, 'New Comment on ' . $reach->get_friendly_type() . ' #' . $reach->id, $this->user->contact_name . ' has posted a new comment on ' . $reach->get_friendly_type() . ' #' . $reach->id . ".\n\nhttp://admin." . url::domain( $assigned_user->domain, false ) . "/products/reaches/reach/?wrid=" . $reach->id, $this->user->account->title . ' <reaches@' . url::domain( $this->user->account->domain, false ) . '>' );
        }

        /***** Add comment *****/

        // Declare variables
        $date = new DateTime( $reach_comment->date_created );
        $confirmation = _('Are you sure you want to delete this comment? This cannot be undone.');

        // Create Comment HTML
        $comment = '<div class="comment" id="comment-' . $reach_comment->id . '">';
        $comment .= '<p class="name">';

        if ( '1' == $reach_comment->private )
            $comment .= '<img src="/images/icons/lock.gif" width="11" height="15" alt="' . _('Private') . '" class="private" />';

        $comment .= '<a href="#" class="assign-to" rel="' . $this->user->id . '">' . $this->user->contact_name . '</a> ';
        $comment .= '<span class="date">' . $date->format( 'F jS, Y g:ia' ) . '</span>';
        $comment .= '<a href="#" class="delete-comment" title="' . _('Delete') . '" confirm="' .  $confirmation . '">';
        $comment .= '<img src="/images/icons/x.png" alt="' . _('X') . '" width="15" height="17" />';
        $comment .= '</a>';
        $comment .= '</p>';
        $comment .= '<p class="message">' . $reach_comment->comment . '</p>';
        $comment .= '<br clear="left" />';
        $comment .= '</div>';

        // Add comment
        jQuery('#comments-list')->prepend( $comment );

        // Also need to reset the form
        jQuery('#comment')
            ->val('')
            ->trigger('blur');

        jQuery('#private')->prop( 'checked', false );


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
        $response->check( isset( $_GET['wrcid'] ), _('Failed to delete comment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get reach comment
        $reach_comment = new WebsiteReachComment();
        $reach_comment->get( $_GET['wrcid'], $this->user->account->id );

        // Remove from page
        jQuery('#comment-' . $reach_comment->id)->remove();

        // Then delete ticket
        $reach_comment->remove();

        // Add jquery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Update who the reach is assigned to
     *
     * @return AjaxResponse
     */
    protected function update_assigned_to() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['wrid'] ) && isset( $_POST['auid'] ), _('Failed to update assigned user') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get reach
        $reach = new WebsiteReach();
        $reach->get( $_POST['wrid'], $this->user->account->id );

        // Change priority
        $reach->assigned_to_user_id = $_POST['auid'];

        // Update ticket
        $reach->save();

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
        $message .= 'You have been assigned ' . $reach->get_friendly_type() . ' #' . $reach->id . ". To view it, follow the link below:\n\n";
        $message .= url::add_query_arg( 'wrid', $reach->id, 'http://account.' . url::domain( $assigned_user->domain, false ) . '/productes/reaches/reach/' ) . "\n\n";
        $message .= 'Priority: ' . $priorities[$reach->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        fn::mail( $assigned_user->email, 'You have been assigned ' . $reach->get_friendly_type() . ' #' . $reach->id . ' (' . $priorities[$reach->priority] . ')', $message, $assigned_user->company . ' <noreply@' . url::domain( $assigned_user->domain, false ) . '>' );

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
        $response->check( isset( $_POST['wrid'] ) && isset( $_POST['priority'] ), _('Failed to update priority') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get reach
        $reach = new WebsiteReach();
        $reach->get( $_POST['wrid'], $this->user->account->id );

        // Change priority
        $reach->priority = $_POST['priority'];

        // Update
        $reach->save();

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
        $response->check( isset( $_POST['wrid'] ) && isset( $_POST['status'] ), _('Failed to update status') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

         // Get reach
        $reach = new WebsiteReach();
        $reach->get( $_POST['wrid'], $this->user->account->id );

        // Change status
        $reach->status = $_POST['status'];

        // Update
        $reach->save();

        return $response;
    }
}

