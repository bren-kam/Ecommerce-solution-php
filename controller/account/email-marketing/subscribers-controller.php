<?php
class SubscribersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/subscribers/';
        $this->section = 'email-marketing';
        $this->title = _('Subscribers') . ' | ' . _('Email Marketing');
    }

    /**
     * List Email Messages
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->email_marketing )
            $this->notify( _('You are only able to manage your subscribers. To have full use of the Email Marketing section you can sign up for it by calling our Online Specialists at (800) 549-9206.'), false );

        return $this->get_template_response( 'index' )
            ->add_title( _('Subscribers') )
            ->select( 'subscribers', 'subscribed' );
    }

    /**
     * List Email Messages
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function unsubscribed() {
        return $this->get_template_response( 'unsubscribed' )
            ->add_title( _('Unsubscribers') )
            ->select( 'subscribers', 'unsubscribed' );
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

        $email = new Email();

        $status = (int) $_GET['s'];

        // Set Order by
        $dt->order_by( '`email`', '`name`', '`phone`', '`date_created`' );
        $dt->add_where( " AND `status` = $status" );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`email`' => false, '`name`' => false ) );

        // Get items
        $subscribers = $email->list_all( $dt->get_variables() );
        $dt->set_row_count( $email->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        if ( $status ) {
        	$confirm = _('Are you sure you want to unsubscribe this email? This cannot be undone.');
        	$unsubscribe_nonce = nonce::create( 'unsubscribe' );
        } else {
            $confirm = $unsubscribe_nonce = '';
        }

        /**
         * @var Email $subscriber
         */
        if ( is_array( $subscribers ) )
        foreach ( $subscribers as $subscriber ) {
            $actions = ( $status ) ? ' | <a href="' . url::add_query_arg( array( 'eid' => $subscriber->id, 'e' => $subscriber->email, '_nonce' => $unsubscribe_nonce ), '/email-marketing/subscribers/unsubscribe/' ) . '"  title="' . _('Unsubscribe') . '" ajax="1" confirm="' . $confirm . '">' . _('Unsubscribe') . '</a>' : '';
            $date = new DateTime( $subscriber->date );

            $data[] = array(
                $subscriber->email . '<br /><div class="actions"><a href="' . url::add_query_arg( 'eid', $subscriber->id, '/email-marketing/subscribers/add-edit/' ) . '" title="' . _('Edit Subscriber') . '">' . _('Edit Subscriber') . '</a>' . $actions . '</div>'
                , $subscriber->name
                , $subscriber->phone
                , $date->format( 'F jS, Y g:i a' )
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
}


