<?php
class UsersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'shopping-cart/users/';
        $this->section = 'shopping-cart';
        $this->title = _('Users | Shopping Cart');
    }

    /**
     * List
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->kb( 122 )
            ->menu_item( 'shopping-cart/users' );
    }

    /**
     * Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit() {
        if ( !isset( $_GET['wuid'] ) )
            return new RedirectResponse('/shopping-cart/users/');

        $user = new WebsiteUser();
        $user->get( $_GET['wuid'], $this->user->account->id );

        if ( !$user->id )
            return new RedirectResponse('/shopping-cart/users/');

        /***** CREATE FORM *****/
        $form = new BootstrapForm( 'fEditUser' );

        // Personal Information
        $form->add_field( 'title', _('Personal Information') );

        $form->add_field( 'text', _('Email'), 'tEmail', $user->email )
            ->attribute( 'maxlength', 100 )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email') );

        $form->add_field( 'password', _('Password'), 'pPassword' )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'blank', '');

        // Billing Information
        $form->add_field( 'title', _('Billing Information') );

        $form->add_field( 'text', _('First Name'), 'tBillingFirstName', $user->billing_first_name )
            ->attribute( 'maxlength', 50 );

        $form->add_field( 'text', _('Last Name'), 'tBillingLastName', $user->billing_last_name )
            ->attribute( 'maxlength', 50 );

        $form->add_field( 'text', _('Address 1'), 'tBillingAddress1', $user->billing_address1 )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Address 2'), 'tBillingAddress2', $user->billing_address2 )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('City'), 'tBillingCity', $user->billing_city )
            ->attribute( 'maxlength', 100 );

        $states[''] = _('-- Select a State --');
        $states = array_merge( $states, data::states( false ) );

        $form->add_field( 'select', _('State'), 'sBillingState', $user->billing_state )
            ->options( $states );

        $form->add_field( 'text', _('Zip Code'), 'tBillingZip', $user->billing_zip )
            ->attribute( 'maxlength', 10 )
            ->add_validation( 'zip', _('The "Billing Information - Zip Code" field must contain a valid zip code' ) );

        $form->add_field( 'text', _('Phone Number'), 'tBillingPhone', $user->billing_phone )
            ->attribute( 'maxlength', 10 )
            ->add_validation( 'phone', _(' The "Billing Information - Phone Number" field must contain a valid phone number' ) );

        $form->add_field( 'text', _('Alt. Phone Number'), 'tBillingAltPhone', $user->billing_alt_phone )
            ->attribute( 'maxlength', 10 )
            ->add_validation( 'phone', _(' The "Billing Information - Alt. Phone Number" field must contain a valid phone number' ) );

        $form->add_field( 'blank', '');

        // Shipping Information
        $form->add_field( 'title', _('Shipping Information') );

        $form->add_field( 'text', _('First Name'), 'tShippingFirstName', $user->shipping_first_name )
            ->attribute( 'maxlength', 50 );

        $form->add_field( 'text', _('Last Name'), 'tShippingLastName', $user->shipping_last_name )
            ->attribute( 'maxlength', 50 );

        $form->add_field( 'text', _('Address 1'), 'tShippingAddress1', $user->shipping_address1 )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Address 2'), 'tShippingAddress2', $user->shipping_address2 )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('City'), 'tShippingCity', $user->shipping_city )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'select', _('State'), 'sShippingState', $user->shipping_state )
            ->options( $states );

        $form->add_field( 'text', _('Zip Code'), 'tShippingZip', $user->shipping_zip )
            ->attribute( 'maxlength', 10 )
            ->add_validation( 'zip', _('The "Billing Information - Zip Code" field must contain a valid zip code' ) );

        $form->add_field( 'blank', '' );

        /***** END FORM *****/

        $response = $this->get_template_response( 'edit' )
            ->kb( 123 )
            ->menu_item( 'shopping-cart/users' )
            ->add_title( _('Edit User') )
            ->set( array( 'form' => $form->generate_form() ) );

        if ( $form->posted() ) {
            if ( $user->email != $_POST['tEmail'] ) {
                $potential_user = new WebsiteUser();
                $potential_user->get_by_email( $_POST['tEmail'], $this->user->account->id );

                if ( $potential_user->id ) {
                    if ( 1 == $potential_user->status ) {
                        $this->notify( _('That email is already taken by another active user. Please choose a different email.'), false );

                        // Generate the form
                        $response->set( 'form', $form->generate_form() );

                        return $response;
                    } else {
                        // Override that user
                        $user = $potential_user;
                    }
                }
            }

            // Update all the fields
            $user->email = $_POST['tEmail'];
            $user->billing_first_name = $_POST['tBillingFirstName'];
            $user->billing_last_name = $_POST['tBillingLastName'];
            $user->billing_address1 = $_POST['tBillingAddress1'];
            $user->billing_address2 = $_POST['tBillingAddress2'];
            $user->billing_city = $_POST['tBillingCity'];
            $user->billing_state = $_POST['sBillingState'];
            $user->billing_zip = $_POST['tBillingZip'];
            $user->billing_phone = $_POST['tBillingPhone'];
            $user->billing_alt_phone = $_POST['tBillingAltPhone'];
            $user->shipping_first_name = $_POST['tShippingFirstName'];
            $user->shipping_last_name = $_POST['tShippingLastName'];
            $user->shipping_address1 = $_POST['tShippingAddress1'];
            $user->shipping_address2 = $_POST['tShippingAddress2'];
            $user->shipping_city = $_POST['tShippingCity'];
            $user->shipping_state = $_POST['sShippingState'];
            $user->shipping_zip = $_POST['tShippingZip'];

            $user->save();

            // Set the password
            if ( !empty( $_POST['tPassword'] ) )
                $user->set_password( $_POST['tPassword'] );

            $this->notify( _('Your user has been successfully updated!') );
            $this->log( 'update-website-user', $this->user->contact_name . ' updated a website user on ' . $this->user->account->title, $user->id );
            return new RedirectResponse('/shopping-cart/users/');
        }

        return $response;
    }

    /***** AJAX *****/

    /**
     * List Shopping Cart Users
     *
     * @return DataTableResponse
     */
    protected function list_users() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $website_user = new WebsiteUser();

        // Set Order by
        $dt->order_by( '`email`', '`billing_first_name`', '`date_registered`' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`email`' => false, '`billing_first_name`' => false ) );

        // Get items
        $website_users = $website_user->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_user->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete this user? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );

        /**
         * @var WebsiteUser $user
         */
        if ( is_array( $website_users ) )
        foreach ( $website_users as $user ) {
            $date = new DateTime( $user->date_registered );

            $data[] = array(
                $user->email . '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'wuid', $user->id, '/shopping-cart/users/edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'wuid' => $user->id, '_nonce' => $delete_nonce ), '/shopping-cart/users/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>' .
                    '</div>'
                , $user->billing_first_name
                , $date->format('F jS, Y')
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
        $response->check( isset( $_GET['wuid'] ), _('You cannot delete this user') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $website_user = new WebsiteUser();
        $website_user->get( $_GET['wuid'], $this->user->account->id );
        $website_user->remove();

        // Redraw the table
        $response->add_response( 'reload_datatable', 'reload_datatable' );

        $this->log( 'delete-website-user', $this->user->contact_name . ' deleted a website user on ' . $this->user->account->title, $website_user->id );

        return $response;
    }

    /**
     * Download
     * @return CsvResponse
     */
    protected function download() {
        $user = new WebsiteUser();
        $users = $user->get_by_account($this->user->account->id);

        $csv = [];
        $csv[] = [
            'email'
            , 'billing_first_name'
            , 'billing_last_name'
            , 'billing_address1'
            , 'billing_address2'
            , 'billing_city'
            , 'billing_state'
            , 'billing_zip'
            , 'billing_phone'
            , 'billing_alt_phone'
            , 'shipping_first_name'
            , 'shipping_last_name'
            , 'shipping_address1'
            , 'shipping_address2'
            , 'shipping_city'
            , 'shipping_state'
            , 'shipping_zip'
            , 'status'
        ];
        foreach ( $users as $user ) {
            $csv[] = [
                $user->email
                , $user->billing_first_name
                , $user->billing_last_name
                , $user->billing_address1
                , $user->billing_address2
                , $user->billing_city
                , $user->billing_state
                , $user->billing_zip
                , $user->billing_phone
                , $user->billing_alt_phone
                , $user->shipping_first_name
                , $user->shipping_last_name
                , $user->shipping_address1
                , $user->shipping_address2
                , $user->shipping_city
                , $user->shipping_state
                , $user->shipping_zip
                , $user->status
            ];
        }

        return new CsvResponse( $csv, 'shopping-cart-users-' . date('YmdHis') . '.csv' );
    }
}


