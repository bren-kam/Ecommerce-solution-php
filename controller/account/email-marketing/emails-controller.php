<?php
class EmailsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/emails/';
        $this->section = 'email-marketing';
        $this->title = _('Emails') . ' | ' . _('Email Marketing');
    }

    /**
     * List Email Messages
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        return $this->get_template_response( 'index' )
            ->add_title( _('Emails') )
            ->select( 'emails', 'view' );
    }

    /**
     * Send Email Message
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function send() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        // Initialize variable
        $email_message_id = ( isset( $_GET['emid'] ) ) ? $_GET['emid'] : '';

        // Get email lists
        $email_list = new EmailList();
        $email_lists = $email_list->get_count_by_account( $this->user->account->id );

        // Get email message
        $message = new EmailMessage();

        if ( $email_message_id ) {
            // Get Message
            $message->get( $email_message_id, $this->user->account->id );

            // Get email lists
            $email_lists_array = $email_list->get_by_message( $message->id, $this->user->account->id );

            foreach ( $email_lists_array as $el ) {
                $message->email_lists[$el->id] = $el;
            }

            // Get meta
            $message->get_smart_meta();
        }

        // Get settings
        $settings = $this->user->account->get_settings( 'from_name', 'from_email', 'timezone' );
        $timezone = $settings['timezone'];
        $server_timezone = Config::setting('server-timezone');

        // Make sure they don't have any blank settings
        if ( array_search( '', $settings ) ) {
            $this->notify( _('One or more of your email settings has not been set. Please update them and then try again.'), false );
            //return new RedirectResponse('/email-marketing/settings/');
        }

        $this->resources
            ->css( 'email-marketing/emails/send', 'jquery.timepicker' )
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'jquery.blockUI', 'jquery.timepicker', 'jquery.datatables', 'email-marketing/emails/send' );

        $email_template = new EmailTemplate();
        $templates = $email_template->get_by_account( $this->user->account->id );

        return $this->get_template_response( 'send' )
            ->add_title( _('Send') . ' | ' . _('Emails') )
            ->select( 'emails' )
            ->set( compact( 'email_lists', 'message', 'settings', 'timezone', 'server_timezone', 'templates' ) );
    }

    /***** AJAX *****/

    /**
     * List All
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $email_message = new EmailMessage();

        // Set Order by
        $dt->order_by( '`subject`', '`status`', 'date_sent' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`subject`' => false ) );

        // Get items
        $messages = $email_message->list_all( $dt->get_variables() );
        $dt->set_row_count( $email_message->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete this email? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $statuses = array( 'Draft', 'Scheduled', 'Sent' );
        $timezone = $this->user->account->get_email_settings( 'timezone' );
        $server_timezone = Config::setting('server-timezone');

        /**
         * @var EmailMessage $message
         */
        if ( is_array( $messages ) )
        foreach ( $messages as $message ) {
            $date = new DateTime( $message->date_sent );
            $message->date_sent = dt::adjust_timezone( $message->date_sent, $server_timezone, $timezone );

            if ( $message->status < 2 ) {
                $actions = '<a href="' . url::add_query_arg( 'emid', $message->id, '/email-marketing/emails/send/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ';
                $actions .= '<a href="' . url::add_query_arg( array( 'emid' => $message->id, '_nonce' => $delete_nonce ), '/email-marketing/emails/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
            } else {
                $actions = '<a href="' . url::add_query_arg( 'emid', $message->id, '/analytics/email-marketing/email/' ) . '" title="' . _('Analytics') . '">' . _('Analytics') . '</a>';
            }

            $data[] = array(
                format::limit_chars( $message->subject, 50, '...' ) . '<br /><div class="actions">' . $actions . '</div>',
                $statuses[$message->status],
                $date->format( 'F jS, Y g:i a' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     *
     * @return AjaxResponse
     */
    public function delete() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['emid'] ), _('You cannot delete this email message') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email_message = new EmailMessage();
        $email_message->get( $_GET['emid'], $this->user->account->id );
        $email_message->remove_all();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Save
     *
     * @return AjaxResponse
     */
    public function save() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_POST['hEmailMessageID'] ), _('An error occurred while trying to save this email. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        // Create/Update message
        $email_message = new EmailMessage();

        if ( 0 == $_POST['hEmailMessageID'] ) {
            $email_message->website_id = $this->user->account->id;
        } else {
            $email_message->get( $_POST['hEmailMessageID'], $this->user->account->id );
        }

        $email_message->email_template_id = ( empty( $_POST['hEmailTemplateID'] ) ) ? 0 : $_POST['hEmailTemplateID'];
        $email_message->subject = $_POST['tSubject'];
        $email_message->message = $_POST['taMessage'];
        $email_message->type = ( empty( $_POST['hEmailType'] ) ) ? 'none' : $_POST['hEmailType'];

        $date_sent = $_POST['tDate'];

        // Turn it into machine-readable time
        if ( !empty( $_POST['tTime'] ) ) {
        	list( $time, $am_pm ) = explode( ' ', $_POST['tTime'] );

        	if ( 'pm' == strtolower( $am_pm ) ) {
        		list( $hour, $minute ) = explode( ':', $time );

        		$date_sent .= ( 12 == $hour ) ? ' ' . $time . ':00' : ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
        	} else {
        		$date_sent .= ' ' . $time . ':00';
        	}
        }

        // Adjust for time zone
        $email_message->date_sent = dt::adjust_timezone( $date_sent, $this->user->account->get_email_settings( 'timezone' ), Config::setting('server-timezone') );

        if ( $email_message->id ) {
            $email_message->save();
            $email_message->remove_associations();
            $email_message->remove_meta();
        } else {
            $email_message->create();
        }

        // Get email lists
        if ( is_array( $_POST['email_lists'] ) )
            $email_message->add_associations( $_POST['email_lists'] );

        // Extra data
        if ( 'product' == $email_message->type ) {
            $message_meta = array();
            $i = 0;

            if ( isset( $_POST['products'] ) )
            foreach ( $_POST['products'] as $product_data ) {
                list( $product_id, $product_price ) = explode( '|', $product_data );
                $message_meta[] = array( 'product', serialize( array( 'product_id' => $product_id, 'price' => $product_price, 'order' => $i ) ) );
                $i++;
            }

            $email_message->add_meta( $message_meta );
        }

        $response->add_response( 'email_message_id', $email_message->id );

        if ( 0 == $email_message->mc_campaign_id )
            return $response;

        $email_message->update_mailchimp( $this->user->account, $_POST['email_lists'] );

        return $response;
    }

    /**
     * Test
     *
     * @return AjaxResponse
     */
    public function test() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_POST['emid'], $_POST['message'] ), _('An error occurred while trying to test this message. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email_message = new EmailMessage();
        $email_message->get( $_GET['emid'], $this->user->account->id );
        $email_message->test();

        return $response;
    }
}


