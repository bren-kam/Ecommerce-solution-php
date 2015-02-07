<?php
class PasswordsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/passwords/';
        $this->section = 'accounts';
    }

    /**
     * Passwords
     *
     * @return TemplateResponse
     */
    protected function index() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $this->resources
            ->css('accounts/passwords')
            ->javascript('pGenerator', 'pStrength', 'accounts/passwords');

        return $this->get_template_response('index')
            ->kb( 0 )
            ->add_title( _('Passwords') )
            ->set( compact( 'account' ) )
            ->select('accounts');
    }

    /***** AJAX *****/

    /**
     * List Account Passwords
     *
     * @return DataTableResponse
     */
    protected function list_passwords() {
        // Get Models
        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'a.`website_password_id`', 'a.`title`', 'a.`username`' );

        // Set Search Columns
        $dt->search( array( 'a.`title`' => true, 'a.`username`' => true ) );

        // Add Where's
        $dt->add_where( ' AND a.`website_id` = ' . (int)$account->id );

        // Get account passwords
        $account_password = new AccountPassword();
        $passwords = $account_password->list_all( $dt->get_variables() );
        $dt->set_row_count( $account_password->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        if ( is_array( $passwords ) ){

            $cryptoKey = Config::key( 'crypto-key' );

            foreach ( $passwords as $p ) {

                $add_edit_url = '/accounts/passwords/add-edit/';
                $add_edit_url = url::add_query_arg( array( 'pid' => $p->id, 'aid' => $account->id ), $add_edit_url );

                $data[] = array(
                    '<a href="' . $add_edit_url . '" title="' . $p->title . '" data-modal>' . $p->title . '</a>'
                    , $p->username
                    , $account_password->decrypt($cryptoKey, $p->iv, $p->password )
                    , '<a href="' . $p->url . '" title="' . $p->title . '">' . $p->url . '</a>'
                    , ''
                );
            }
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Get the password
     *
     * @return AjaxResponse
     */
    protected function get() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or no password id, return
        if ( $response->has_error() || !isset( $_GET['pid'] ) )
            return $response;

        // Setup Password
        $account_password = new AccountPassword();

        // Get Current Password
        if ( '0' != $_GET['pid'] ) {
            $cryptoKey = Config::key( 'crypto-key' );

            $account_password->get( $_GET['pid'] );
            $response->add_response( 'account_password', $account_password );
        }

        return $response;
    }

    /**
     * Add/Edit a password
     *
     * @return CustomResponse|AjaxResponse
     */
    protected function add_edit() {
        // Get the password if there is one
        $password_id = ( isset( $_GET['pid'] ) ) ? (int) $_GET['pid'] : false;
        $account_id = ( isset( $_GET['aid'] ) ) ? (int) $_GET['aid'] : false;

        // Setup Models
        $account_password = new AccountPassword();

        if ( $this->verified() ) {
            // If it exists, get it
            if ( $password_id )
                $account_password->get( $password_id );

            $account_password->title = $_POST['sTitle'];
            $account_password->username = $_POST['tUsername'];
            $account_password->password = $_POST['tPassword'];
            $account_password->url = $_POST['sUrl'];
            $account_password->notes = $_POST['tNotes'];

            if ( $password_id ) {
                $account_password->save();
                $message = _('Your password has been successfully updated!');

            } else {
                $account_password->website_id = $account_id;
                $account_password->create();
                $message = _('Your password has been successfully created!');
            }

            $response = $this->get();
            $response->notify( $message );

            return $response;
        }

        if ( $password_id ) {
            $account_password->get( $password_id );
            $title = $account_password->title;
            $username = $account_password->username;
            $password = $account_password->password;
            $url = $account_password->url;
            $notes = $account_password->notes;
        } else {
            $title = ( isset( $_POST['sTitle'] ) ) ? $_POST['sTitle'] : '';
            $username = ( isset( $_POST['tUsername'] ) ) ? $_POST['tUsername'] : '';
            $password = ( isset( $_POST['tPassword'] ) ) ? $_POST['tPassword'] : '';
            $url = ( isset( $_POST['sUrl'] ) ) ? $_POST['sUrl'] : '';
            $notes = ( isset( $_POST['tNotes'] ) ) ? $_POST['tNotes'] : '';
        }

        $response = new CustomResponse( $this->resources->javascript('pGenerator', 'pStrength', 'accounts/passwords'), 'accounts/passwords/add-edit' );
        $response->set( compact( 'account_password', 'title', 'username', 'password', 'url', 'notes' ) );

        return $response;
    }

    /**
     * Delete a password
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or no user id, return
        if ( $response->has_error() || !isset( $_GET['pid'] ) || 0 == $_GET['pid'] )
            return $response;

        // Get the password
        $account_password = new AccountPassword();
        $account_password->get( $_GET['pid'] );

        // Remove password
        if ( $account_password->id ) {
            $account_password->remove();
        }

        return $response;
    }
}