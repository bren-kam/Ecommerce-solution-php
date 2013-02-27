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
     * List Subscribers
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
     * List Unsubscribed
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function unsubscribed() {
        return $this->get_template_response( 'unsubscribed' )
            ->add_title( _('Unsubscribers') )
            ->select( 'subscribers', 'unsubscribed' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get Coupon
        $email = new Email();
        $email_list = new EmailList();

        $email_id = ( isset( $_GET['eid'] ) ) ? $_GET['eid'] : false;

        if ( $email_id )
            $email->get( $email_id, $this->user->account->id );

        $form = new FormTable( 'fAddEditSubscriber' );

        if ( !$email->id )
            $form->submit( _('Create') );

        $form->add_field( 'title', _('Basic Information') );

        $form->add_field( 'text', _('Name'), 'tName', $email->name )
            ->attribute( 'maxlength', 80 );

        $form->add_field( 'text', _('Email'), 'tEmail', $email->email )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email') );

        $form->add_field( 'text', _('Phone'), 'tPhone', $email->phone )
            ->attribute( 'maxlength', 20 )
            ->add_validation( 'phone', _('The "Phone" field must contain a valid phone number') );

        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Email List Subscriptions') );

        $email_lists = $email_list->get_by_account( $this->user->account->id );
        $lists = $email->get_associations();

        foreach ( $email_lists as $el ) {
            $value = ( in_array( $el->id, $lists ) ) ? '1' : false;
            $form->add_field( 'checkbox', $el->name, 'email-lists[' . $el->id . ']', $value );
        }

        if ( $form->posted() ) {
            $success = true;

            $email->name = $_POST['tName'];
            $email->email = $_POST['tEmail'];
            $email->phone = $_POST['tPhone'];

            if ( $email->id ) {
                $email->save();
            } else {
                $test_email = new Email();
                $test_email->get_by_email( $this->user->account->id, $email->email );

                if ( $test_email->id && 2 == $test_email->status ) {
                    $success = false;
                    $this->notify( _('This email has been unsubscribed by the user.') );
                } else {
                    $email->website_id = $this->user->account->id;
                    $email->status = 1;
                    $email->create();
                }
            }

            if ( $success ) {
                if ( isset( $_POST['email-lists'] ) )
                    $email->add_associations( array_keys( $_POST['email-lists'] ) );

                $this->notify( _('Your email has been added/updated successfully!') );
                return new RedirectResponse('/email-marketing/subscribers/');
            }
        }

        $form = $form->generate_form();
        $title = ( $email->id ) ? _('Edit') : _('Add');

        return $this->get_template_response( 'add-edit' )
            ->select( 'subscribers', 'add-edit' )
            ->add_title( $title . ' ' . _('Subscriber') )
            ->set( compact( 'email', 'form' ) );
    }

    /**
     * Export
     *
     * @return CsvResponse
     */
    public function export() {
        // Get the email list ID
        $email_list_id = ( isset( $_GET['elid'] ) ) ? $_GET['elid'] : 0;

        $where = ' AND e.`status` = 1 AND e.`website_id` = ' . (int) $this->user->account->id;

        if ( $email_list_id )
            $where .= ' AND ea.`email_list_id` = ' . (int) $email_list_id;

        // Get subscribers
        $email = new Email();
        $subscribers = $email->list_all( array(
            $where
            , array()
            , 'ORDER BY e.`date_created` ASC'
            , 100000
        ) );

        $output[]  = array( 'Email', 'Name', 'Phone' );

        foreach ( $subscribers as $subscriber ) {
            $output[] = array( $subscriber->email, $subscriber->name, $subscriber->phone );
        }

        return new CsvResponse( $output, format::slug( $this->user->account->title ) . '-email-subscribers.csv' );
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
        $dt->order_by( 'e.`email`', 'e.`name`', 'e.`phone`', 'e.`date_created`' );
        $dt->add_where( " AND e.`status` = $status" );
        $dt->add_where( ' AND e.`website_id` = ' . (int) $this->user->account->id );

        if ( isset( $_GET['elid'] ) )
        $dt->add_where( " AND ea.`email_list_id` = " . (int) $_GET['elid'] );

        $dt->search( array( 'e.`email`' => false, 'e.`name`' => false ) );

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
            $actions = ( $status ) ? ' | <a href="' . url::add_query_arg( array( 'eid' => $subscriber->id, '_nonce' => $unsubscribe_nonce ), '/email-marketing/subscribers/unsubscribe/' ) . '"  title="' . _('Unsubscribe') . '" ajax="1" confirm="' . $confirm . '">' . _('Unsubscribe') . '</a>' : '';
            $date = new DateTime( $subscriber->date );

            $data[] = array(
                $subscriber->email . '<br /><div class="actions"><a href="' . url::add_query_arg( 'eid', $subscriber->id, '/email-marketing/subscribers/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions . '</div>'
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
    public function unsubscribe() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['eid'] ), _('You cannot unsubscribe this subscriber') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email = new Email();
        $email->get( $_GET['eid'], $this->user->account->id );
        $email->remove_all( $this->user->account->mc_list_id );

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


