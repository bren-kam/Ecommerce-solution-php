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
        return $this->get_template_response( 'index' )
            ->add_title( _('Emails') )
            ->select( 'emails', 'view' );
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
        $response->check( isset( $_GET['emid'] ), _('You cannot delete this page') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email_message = new EmailMessage();
        $email_message->get( $_GET['emid'], $this->user->account->id );
        $email_message->remove();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


