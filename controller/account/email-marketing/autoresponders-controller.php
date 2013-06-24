<?php
class AutorespondersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/autoresponders/';
        $this->section = 'email-marketing';
        $this->title = _('Autoresponders') . ' | ' . _('Email Marketing');
    }

    /**
     * List Subscribers
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->kb( 82 )
            ->add_title( _('Autoresponders') )
            ->select( 'autoresponders' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get Autoresponder
        $email_autoresponder = new EmailAutoresponder();

        $email_list = new EmailList();
        $email_lists = $email_list->get_by_account( $this->user->account->id );

        $email_autoresponder_id = ( isset( $_GET['eaid'] ) ) ? $_GET['eaid'] : false;

        if ( $email_autoresponder_id )
            $email_autoresponder->get( $email_autoresponder_id, $this->user->account->id );

        $form = new FormTable( 'fAddEditAutoresponder' );

        if ( !$email_autoresponder->id )
            $form->submit( _('Add') );

        $form->add_field( 'title', _('Base Information') );

        $form->add_field( 'text', _('Name'), 'tName', $email_autoresponder->name )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $form->add_field( 'text', _('Subject'), 'tSubject', $email_autoresponder->subject )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Subject" field is required') );

        $form->add_field( 'textarea', _('Autoresponse'), 'taAutoresponse', $email_autoresponder->message )
            ->attribute( 'rte', 1 );

        $form->add_field( 'checkbox', _('Include Current Offer'), 'cbCurrentOffer', $email_autoresponder->current_offer );

        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Email List') );

        foreach ( $email_lists as $el ) {
            $radio = $form->add_field( 'radio', $el->name, 'sEmailList', $el->id );

            if ( !isset( $_POST['sEmailList'] ) && $email_autoresponder->email_list_id == $el->id )
                $radio->attribute( 'checked', 'checked' );
        }

        if ( $form->posted() ) {
            $email_autoresponder->name = $_POST['tName'];
            $email_autoresponder->subject = $_POST['tSubject'];
            $email_autoresponder->message = $_POST['taAutoresponse'];
            $email_autoresponder->current_offer = (int) isset( $_POST['cbCurrentOffer'] );
            $email_autoresponder->email_list_id = $_POST['sEmailList'];

            if ( $email_autoresponder->id ) {
                $email_autoresponder->save();
            } else {
                $email_autoresponder->website_id = $this->user->account->id;
                $email_autoresponder->create();
            }

            $this->notify( _('Your autoresponder has been added/updated successfully!') );
            return new RedirectResponse('/email-marketing/autoresponders/');
        }

        $form = $form->generate_form();
        $title = ( $email_autoresponder->id ) ? _('Edit') : _('Add');

        return $this->get_template_response( 'add-edit' )
            ->kb( 83 )
            ->select( 'autoresponders', 'add-edit' )
            ->add_title( $title . ' ' . _('Email Autoresponder') )
            ->set( compact( 'email_autoresponder', 'form' ) );
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

        $email_autoresponder = new EmailAutoresponder();

        // Set Order by
        $dt->order_by( '`name`', '`subject`' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`name`' => false, '`subject`' => true ) );

        // Get items
        $autoresponders = $email_autoresponder->list_all( $dt->get_variables() );
        $dt->set_row_count( $email_autoresponder->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete this autoresponder? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );

        /**
         * @var EmailAutoresponder $autoresponder
         */
        if ( is_array( $autoresponders ) )
        foreach ( $autoresponders as $autoresponder ) {
            // Make the delete text
            $actions = ( $autoresponder->default ) ? '' : ' | <a href="' . url ::add_query_arg( array( 'eaid' => $autoresponder->id, '_nonce' => $delete_nonce ), '/email-marketing/autoresponders/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a></div>';

            $data[] = array(
                $autoresponder->name . '<br /><div class="actions"><a href="' . url::add_query_arg( 'eaid', $autoresponder->id, '/email-marketing/autoresponders/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions . '</div>'
                , format::limit_chars( $autoresponder->subject, 100 )
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
    protected function delete() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['eaid'] ), _('You cannot delete this autoresponder') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email_autoresponder = new EmailAutoresponder();
        $email_autoresponder->get( $_GET['eaid'], $this->user->account->id );
        $email_autoresponder->remove();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}