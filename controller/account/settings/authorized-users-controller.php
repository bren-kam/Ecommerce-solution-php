<?php
class AuthorizedUsersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'settings/authorized-users/';
        $this->title = _('Authorized Users') . ' | ' . _('Settings');
    }

    /**
     * Modify settings
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( $this->user->role == User::ROLE_AUTHORIZED_USER )
            return new RedirectResponse('/settings/');

        return $this->get_template_response( 'index' )
            ->kb( 118 )
            ->select( 'authorized-users' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get Autoresponder
        $auth_user_website = new AuthUserWebsite();

        $user_id = ( isset( $_GET['uid'] ) ) ? $_GET['uid'] : false;

        if ( $user_id )
            $auth_user_website->get( $user_id, $this->user->account->id );

        $form = new BootstrapForm( 'fAddEditAuthUser' );

        if ( !$auth_user_website->user_id )
            $form->submit( _('Add') );

        $form->add_field( 'title', _('User Information') );

        if ( $auth_user_website->user_id ) {
            $form->add_field( 'row', _('Name') . ':', $auth_user_website->contact_name );
            $form->add_field( 'row', _('Email') . ':', $auth_user_website->email );
        } else {
            $form->add_field( 'text', _('Name'), 'tName', $auth_user_website->contact_name )
                ->attribute( 'maxlength', 100 )
                ->add_validation( 'req', _('The "Name" field is required') );

            $form->add_field( 'text', _('Email'), 'tEmail', $auth_user_website->email )
                ->attribute( 'maxlength', 200 )
                ->add_validation( 'req', _('The "Email" field is required') )
                ->add_validation( 'email', _('The "Email" field must contain a valid email') );

            if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) {
                $form->add_field( 'select', _('Role'), 'sRole' )
                    ->options( array(
                    'authorized-user' => _('Authorized User')
                    , 'representative' => _('Representative')
                ));
            }
        }

        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Section Permissions') );

        $form->add_field( 'checkbox', _('Pages'), 'cbPages', $auth_user_website->pages );
        $form->add_field( 'checkbox', _('Products'), 'cbProducts', $auth_user_website->products );
        $form->add_field( 'checkbox', _('Analytics'), 'cbAnalytics', $auth_user_website->analytics );
        $form->add_field( 'checkbox', _('Blog'), 'cbBlog', $auth_user_website->blog );
        $form->add_field( 'checkbox', _('Email Marketing'), 'cbEmailMarketing', $auth_user_website->email_marketing );
        $form->add_field( 'checkbox', _('Shopping Cart'), 'cbShoppingCart', $auth_user_website->shopping_cart );

        if ( $form->posted() ) {
            $success = true;

            if ( $auth_user_website->user_id ) {
                $auth_user_website->pages = ( isset( $_POST['cbPages'] ) ) ? 1 : 0;
                $auth_user_website->products = ( isset( $_POST['cbProducts'] ) ) ? 1 : 0;
                $auth_user_website->analytics = ( isset( $_POST['cbAnalytics'] ) ) ? 1 : 0;
                $auth_user_website->blog = ( isset( $_POST['cbBlog'] ) ) ? 1 : 0;
                $auth_user_website->email_marketing = ( isset( $_POST['cbEmailMarketing'] ) ) ? 1 : 0;
                $auth_user_website->shopping_cart = ( isset( $_POST['cbShoppingCart'] ) ) ? 1 : 0;
                $auth_user_website->save();
            } else {
                if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST )) {
                    $role = ( 'representative' == $_POST['sRole'] ) ? User::ROLE_MARKETING_SPECIALIST : User::ROLE_AUTHORIZED_USER;
                } else {
                    $role = User::ROLE_AUTHORIZED_USER;
                }

                try {
                    $auth_user_website->add(
                        $_POST['tName']
                        , $_POST['tEmail']
                        , $this->user->account->id
                        , isset( $_POST['cbPages'] )
                        , isset( $_POST['cbProducts'] )
                        , isset( $_POST['cbAnalytics'] )
                        , isset( $_POST['cbBlog'] )
                        , isset( $_POST['cbEmailMarketing'] )
                        , isset( $_POST['cbShoppingCart'] )
                        , $role
                    );
                } catch ( ModelException $e ) {
                    $this->notify( _('This user already has an account which is ineligible to be added as an authorized user, please use another email address.' ), false );
                    $success = false;
                }
            }

            if ( $success ) {
                $this->notify( _('Your authorized user has been added/updated successfully!') );
                return new RedirectResponse('/settings/authorized-users/');
            }
        }

        $form = $form->generate_form();
        $title = ( $auth_user_website->user_id ) ? _('Edit') : _('Add');

        return $this->get_template_response( 'add-edit' )
            ->kb( 119 )
            ->add_title( $title . ' ' . _('Authorized User') )
            ->select( 'authorized-users', 'add-edit' )
            ->set( compact( 'form', 'auth_user_website' ) );
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
        $auth_user_website = new AuthUserWebsite();

        // Set Order by
        $dt->order_by( 'u.`email`', 'auw.`pages`', 'auw.`products`', 'auw.`analytics`', 'auw.`blog`', 'auw.`email_marketing`', 'auw.`shopping_cart`' );
        $dt->add_where( " AND auw.`website_id` = " . (int) $this->user->account->id );
        $dt->search( array( 'u.`email`' => false ) );

        // Get all
        $auth_users = $auth_user_website->list_all( $dt->get_variables() );
        $dt->set_row_count( $auth_user_website->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        $confirm = _('Are you sure you want to delete this Authorized user? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $resend_nonce = nonce::create( 'send_activation_link' );
        
        /**
         * @var AuthUserWebsite $au
         */
        if ( is_array( $auth_users ) ){
             foreach ( $auth_users as $au ) {
                 $options_html = $au->email . '<div class="actions">' .
                        '<a href="' . url::add_query_arg( 'uid', $au->user_id, '/settings/authorized-users/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                        '<a href="' . url::add_query_arg( array( 'uid' => $au->user_id, '_nonce' => $delete_nonce ), '/settings/authorized-users/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>' ;
                 
                 if ( empty( $au->password ) )
                     $options_html .= ' | <a href="' . url::add_query_arg( array( 'uid' => $au->user_id, '_nonce' => $resend_nonce ), '/settings/authorized-users/send-activation-link/' ) . '" title="' . _('Delete') . '" ajax="1">' . _('Resend Activation Link') .'</a>';
                 
                 $options_html .= '</div>';
                
                 $data[] = array(
                     $options_html
                     , ( $au->pages ) ? _('Yes') : _('No')
                     , ( $au->products ) ? _('Yes') : _('No')
                     , ( $au->analytics ) ? _('Yes') : _('No')
                     , ( $au->blog ) ? _('Yes') : _('No')
                     , ( $au->email_marketing ) ? _('Yes') : _('No')
                     , ( $au->shopping_cart ) ? _('Yes') : _('No')
                );
            }
        }
       
        // Send response
        $dt->set_data( $data );

        return $dt;
    }
    
    /**
     *  Resend activation link
     * 
     * 
     */
    public function send_activation_link() {
         // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );
        
        // If there is an error, return
        if ( $response->has_error() )
            return $response;
            
        // Send activation link
        $auth_user_website = new AuthUserWebsite();
        $auth_user_website->send_activation_link( $_GET["uid"] , $this->user->account );
        
        return $response;
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
        $response->check( isset( $_GET['uid'] ), _('You cannot delete this authorized user') );
        $response->check( $this->user->has_permission( User::ROLE_AUTHORIZED_USER ), _('You do not have permission to delete this authorized user') );

        if ( $response->has_error() )
            return $response;

        // Remove the page
        $auth_user_website = new AuthUserWebsite();
        $auth_user_website->get( $_GET['uid'], $this->user->account->id );
        $auth_user_website->remove();

        // Redraw the table
        $response->add_response( 'reload_datatable', 'reload_datatable' );

        return $response;
    }
}


