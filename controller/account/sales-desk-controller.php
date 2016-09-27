<?php
class SalesDeskController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'sales-desk/';
        $this->section = 'Sales Desk';
    }

    /**
     * List Reaches page
     *
     * @return TemplateResponse
     */
    protected function index() {
        $this->resources->css( 'sales-desk/index' )
            ->javascript( 'sales-desk/index' );

        return $this->get_template_response( 'index' )
            ->kb( 54 )
            ->menu_item( 'sales-desk/index' );
    }

    /**
     * Reach
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function reach() {
        // Make sure they can be here
        if ( !isset( $_GET['wrid'] ) )
            return new RedirectResponse('/sales-desk/');

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
            if ( $u->role != User::ROLE_MARKETING_SPECIALIST &&
                strpos($u->email, '@ashleyfurniture') === FALSE &&
                strpos($u->email, '@sales.ashleyfurniture') === FALSE )
                $assignable_users[$u->id] = $u;
        }

        $this->resources
            ->css( 'sales-desk/reach' )
            ->javascript( 'jquery.autoresize', 'sales-desk/reach' );

        return $this->get_template_response( 'reach' )
            ->kb( 55 )
            ->add_title( _('Reach') )
            ->menu_item( 'sales-desk/index' )
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
        $status = ( isset( $_SESSION['reaches']['status'] ) ) ? (int) $_SESSION['reaches']['status'] : WebsiteReach::STATUS_OPEN;
        $dt->order_by( 'name', 'wu.`email`', 'assigned_to', 'waiting', 'wr.`priority`', 'wr.`date_created`' );
        $dt->add_where( " AND wr.`website_id` = " . $this->user->account->id );
        $dt->add_where( ' AND wr.`status` = ' . $status );

        $dt->search( array( 'wu.`billing_first_name`' => false, 'wu.`billing_last_name`' => false, 'wu.`email`' => false, 'u.`contact_name`' => false ) );

        // Get Reaches
        $website_reach = new WebsiteReach();
        $reaches = $website_reach->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_reach->count_all( $dt->get_count_variables() ) );

        // Setup data
        $priorities = array(
            WebsiteReach::PRIORITY_NORMAL => 'Normal',
            WebsiteReach::PRIORITY_HIGH => 'High',
            WebsiteReach::PRIORITY_URGENT => 'Urgent'
        );

        $data = array();

        // Create output
        if ( is_array( $reaches ) )
        foreach ( $reaches as $reach ) {
            $date = new DateTime( $reach->date_created );

            $name = ( empty( $reach->name ) ) ? 'Anonymous' : $reach->name;

            $data[] = array(
                '<a href="' . url::add_query_arg( 'wrid', $reach->id, '/sales-desk/reach/' ) . '">' . $name . '</a>'
                , $reach->email
                , $reach->assigned_to
                , ( $reach->waiting ) ? 'Requires Response' : 'Waiting'
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
        $reach->get_meta();

        // Set variables
        $status = ( 0 == $reach->status ) ? ' (Open)' : ' (Closed)';

        // Create ticket comment
        $reach_comment->website_reach_id = $reach->id;
        $reach_comment->user_id = $this->user->user_id;
        $reach_comment->comment = nl2br( format::links_to_anchors( format::htmlentities( format::convert_characters( $_POST['comment'] ), array('&') ), true, true ) );
        $reach_comment->private = (int) isset( $_POST['private'] );

        $reach_comment->create();

        $this->log( 'comment-on-reach', $this->user->contact_name . ' commented on a reach on ' . $this->user->account->title, $_POST );

        // Send emails
        $comment = strip_tags( $reach_comment->comment );

        // If it's not private, send an email to the website user
        if ( 0 == $reach_comment->private ) {
            // No longer waiting for us
            $reach->waiting = 0;
            $reach->save();

            library('sendgrid-api'); SendgridApi::send(
                $reach->email
                , $reach->get_friendly_type() . ' #' . $reach->id . $status
                , "******************* Reply Above This Line *******************\n\n{$comment}\n\n" . $reach->get_friendly_type() . "\n" . $reach->message
                , '"' . $this->user->account->title . '" <reaches@blinkyblinky.me>'
            );
        }

        // Send the assigned user an email if they are not submitting the comment
        if ( $reach->assigned_to_user_id != $this->user->id && 1 == $reach->status ) {
            $assigned_user->get( $reach->assigned_to_user_id );
            library('sendgrid-api'); SendgridApi::send(
                $assigned_user->email
                , 'New Comment on ' . $reach->get_friendly_type() . ' #' . $reach->id
                , $this->user->contact_name . ' has posted a new comment on ' . $reach->get_friendly_type() . ' #' . $reach->id . ".\n\nhttp://admin." . url::domain( $assigned_user->domain, false ) . "/sales-desk/reach/?wrid=" . $reach->id
                , '"' . $this->user->account->title . '" <reaches@blinkyblinky.me>'
            );
        }

        /***** Add comment *****/

        $response->add_response( 'contact_name', $this->user->contact_name );
        $response->add_response( 'id', $reach_comment->id );
        $response->add_response( 'user_id', $this->user->id );
        $response->add_response( 'comment', $reach_comment->comment );
        $response->add_response( 'private', $reach_comment->private );

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
        $response->check( isset( $_POST['wrcid'] ), _('Failed to delete comment') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get reach comment
        $reach_comment = new WebsiteReachComment();
        $reach_comment->get( $_POST['wrcid'], $this->user->account->id );

        // Remove from page
        jQuery('#comment-' . $reach_comment->id)->remove();

        // Then delete ticket
        $reach_comment->remove();

        $this->log( 'delete-reach-comment', $this->user->contact_name . ' deleted a reach comment on ' . $this->user->account->title, $reach_comment->id );

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

        $this->log( 'update-reach-assigned-to', $this->user->contact_name . ' changed who a reach was assigned to on  ' . $this->user->account->title, $reach->id );

        // Send out email
        $priorities = array(
            WebsiteReach::PRIORITY_NORMAL => 'Normal',
            WebsiteReach::PRIORITY_HIGH => 'High',
            WebsiteReach::PRIORITY_URGENT => 'Urgent'
        );

        $assigned_user = new User();
        $assigned_user->get( $_POST['auid'] );

        // Send out an email if their role is less than 8
        $message = 'Hello ' . $assigned_user->contact_name . ",\n\n";
        $message .= 'You have been assigned ' . $reach->get_friendly_type() . ' #' . $reach->id . ". To view it, follow the link below:\n\n";
        $message .= url::add_query_arg( 'wrid', $reach->id, 'http://account.' . url::domain( $assigned_user->domain, false ) . '/sales-desk/reach/' ) . "\n\n";
        $message .= 'Priority: ' . $priorities[$reach->priority] . "\n\n";
        $message .= "Sincerely,\n" . $assigned_user->company . " Team";

        library('sendgrid-api'); SendgridApi::send( $assigned_user->email, 'You have been assigned ' . $reach->get_friendly_type() . ' #' . $reach->id . ' (' . $priorities[$reach->priority] . ')', $message, $assigned_user->company . ' <noreply@' . url::domain( $assigned_user->domain, false ) . '>' );

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

        $this->log( 'update-reach-priority', $this->user->contact_name . ' updated reach priority on ' . $this->user->account->title, $reach->id )
        ;
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

        $this->log( 'update-reach-status', $this->user->contact_name . ' updated reach status on ' . $this->user->account->title, $reach->id );

        return $response;
    }

    /**
     * Settings
     *
     * @return TemplateResponse
     */
    protected function settings() {
        // Instantiate classes
        $form = new BootstrapForm( 'fSettings' );

        // Get settings
        $settings_array = array(
            'request-a-quote-email'
            , 'request-a-quote-button'
        );

        $settings = $this->user->account->get_settings( $settings_array );

        // Create form
        $form->add_field( 'text', _('Request-a-Quote Email'), 'request-a-quote-email', $settings['request-a-quote-email'] )
            ->attribute( 'maxlength', '150' )
            ->add_validation( 'req', 'email', _('The "Request-a-Quote Email" field must contain a valid email') );

        if ( $this->user->account->is_new_template() ) {
            $form->add_field( 'text', _('Request-a-Quote Button Text'), 'request-a-quote-button', empty($settings['request-a-quote-button']) ? 'Request a Quote' : $settings['request-a-quote-button'] )
                ->attribute( 'maxlength', '150' );
        } else {
            $settings['request-a-quote-button'] = 'Request a Quote';
        }

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }

            $this->user->account->set_settings( $new_settings );

            // Clear Cloudflare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Your settings have been successfully saved!') );
            $this->log( 'product-settings', $this->user->contact_name . ' has changed product settings on ' . $this->user->account->title, $_POST );

            // Refresh to get all the changes
            return new RedirectResponse('/sales-desk/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->kb( 61 )
            ->add_title( _('Settings') )
            ->menu_item( 'sales-desk/settings' )
            ->set( array( 'form' => $form->generate_form() ) );
    }

}

