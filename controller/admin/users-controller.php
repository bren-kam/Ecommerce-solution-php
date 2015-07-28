<?php
class UsersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'users/';
        $this->section = 'users';
    }

    /**
     * List
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->kb( 20 )
            ->select( 'users', 'users/index' );
    }

    /**
     * Add/Edit a user
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Determine if we're adding or editing the user
        $user_id = ( isset( $_GET['uid'] ) ) ? (int) $_GET['uid'] : false;


        // Select page
        $page = ( $user_id ) ? '' : 'add';

        // Get Page
        $template_response = $this->get_template_response( 'add-edit' )
            ->kb( 21 )
            ->select( 'users', $page )
            ->add_title( ( ( $user_id ) ? _('Edit') : _('Add') ) );

        // Initialize classes
        $user = new User();
        $company = new Company();

        // Get the user
        if ( $user_id ) {
            $user->get( $user_id );

            // Make sure they can access this user
            if ( ( !$this->user->has_permission( User::ROLE_ADMIN ) && $this->user->company_id != $user->company_id ) || $this->user->role < $user->role )
                return new RedirectResponse('/users/');
        }

        // Create new form table
        $ft = new BootstrapForm( 'fAddEditUser' );

        $ft->submit( ( $user_id ) ? _('Save') : _('Add') );

        $ft->add_field( 'title', _('Personal Information') );

        // Add companies if there is one
        if ( $this->user->has_permission( User::ROLE_ADMIN ) ) {
            $companies = ar::assign_key( $company->get_all( PDO::FETCH_ASSOC ), 'company_id', true );

            $ft->add_field( 'select', _('Company'), 'sCompany', $user->company_id )
                ->options( $companies );
        }

        $ft->add_field( 'text', _('Email'), 'tEmail', $user->email )
            ->attribute( 'maxlength', 100 )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email') );

        $ft->add_field( 'password', _('Password'), 'tPassword' )
            ->attribute( 'maxlength', 30 );

        $ft->add_field( 'text', _('Contact Name'), 'tContactName', $user->contact_name )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Contact Name" field is required') );

        $ft->add_field( 'text', _('Work Phone'), 'tWorkPhone', $user->work_phone )
            ->attribute( 'maxlength', 20 );
//            ->add_validation( 'phone', _('The "Work Phone" field must contain a valid phone number') );

        $ft->add_field( 'text', _('Cell Phone'), 'tCellPhone', $user->cell_phone )
            ->attribute( 'maxlength', 20 );
//            ->add_validation( 'phone', _('The "Cell Phone" field must contain a valid phone number') );

        $ft->add_field( 'text', _('Store Name'), 'tStoreName', $user->store_name )
            ->attribute( 'maxlength', 80 );

        
        $ft->add_field( 'text', _('Jira Username'), 'tJiraUserName', $user->jira_username )
            ->attribute( 'maxlength', 80 );
        $ft->add_field( 'text', _('Jira Password'), 'tJiraPassword', $user->jira_password )
            ->attribute( 'maxlength', 80 );

        $ft->add_field( 'select', _('Status'), 'sStatus', $user->status )
            ->options( array(
                User::STATUS_ACTIVE => _('Active')
                , User::STATUS_INACTIVE => _('Inactive')
            ));

        $roles = array(
            User::ROLE_AUTHORIZED_USER => User::ROLE_AUTHORIZED_USER . ' - ' ._('Authorized User')
            , User::ROLE_MARKETING_SPECIALIST => User::ROLE_MARKETING_SPECIALIST . ' - ' . _('Marketing Specialist')
            , User::ROLE_STORE_OWNER => User::ROLE_STORE_OWNER . ' - ' . _('Basic Account')
            , User::ROLE_COMPANY_ADMIN => User::ROLE_COMPANY_ADMIN . ' - ' . _('Company Admin')
            , User::ROLE_ONLINE_SPECIALIST => User::ROLE_ONLINE_SPECIALIST . ' - ' . _('Online Specialist')
            , User::ROLE_ADMIN => User::ROLE_ADMIN . ' - ' . _('Admin')
            , User::ROLE_SUPER_ADMIN => User::ROLE_SUPER_ADMIN . ' - ' . _('Super Admin')
        );

        $options = array();

        foreach ( $roles as $role => $name ) {
            if ( $this->user->has_permission( $role ) )
                $options[$role] = $name;
        }

        $ft->add_field( 'select', _('Role'), 'sRole', $user->role )
            ->options( $options );

        $ft->add_field( 'blank', '' );
        $ft->add_field( 'title', _('Billing Information') );

        $ft->add_field( 'text', _('First Name'), 'tFirstName', $user->billing_first_name )
            ->attribute( 'maxlength', 50 );

        $ft->add_field( 'text', _('Last Name'), 'tLastName', $user->billing_last_name )
            ->attribute( 'maxlength', 50 );

        $ft->add_field( 'text', _('Address'), 'tAddress', $user->billing_address1 )
            ->attribute( 'maxlength', 100 );

        $ft->add_field( 'text', _('City'), 'tCity', $user->billing_city )
            ->attribute( 'maxlength', 100 );

        $states[''] = _('-- Select a State --');
        $states = array_merge( $states, data::states( false ) );

        $ft->add_field( 'select', _('State'), 'sState', $user->billing_state )
            ->options( $states );

        $ft->add_field( 'text', _('Zip'), 'tZip', $user->billing_zip )
            ->attribute( 'maxlength', 10 )
            ->add_validation( 'zip', _('The "Zip" field must contain a valid zip code'));

//        $ft->add_field( 'textarea', _('Email Signature'), 'taEmailSignature', $user->email_signature )
//            ->attribute( 'maxlength', 500 );

        // Make sure it's posted and verified
        if ( $ft->posted() ) {
            if ( $user->email != $_POST['tEmail'] ) {
                $potential_user = new User();
                $potential_user->get_by_email( $_POST['tEmail'], false );

                if ( $potential_user->id ) {
                    if ( 1 == $potential_user->status ) {
                        $this->notify( _('That email is already taken by another active user. Please choose a different email.'), false );

                        // Generate the form
                        $template_response->set( 'form', $ft->generate_form() );

                        return $template_response;
                    } else {
                        // Override that user
                        $user = $potential_user;
                    }
                }
            }

            // Update all the fields
            $user->email = $_POST['tEmail'];
            $user->contact_name = $_POST['tContactName'];
            $user->work_phone = $_POST['tWorkPhone'];
            $user->cell_phone = $_POST['tCellPhone'];
            $user->store_name = $_POST['tStoreName'];
            $user->status = $_POST['sStatus'];
            $user->role = $_POST['sRole'];
            $user->jira_username = $_POST['tJiraUserName'];
            $user->jira_password = $_POST['tJiraPassword'];            
            $user->billing_first_name = $_POST['tFirstName'];
            $user->billing_last_name = $_POST['tLastName'];
            $user->billing_address1 = $_POST['tAddress'];
            $user->billing_city = $_POST['tCity'];
            $user->billing_state = $_POST['sState'];
            $user->billing_zip = $_POST['tZip'];
//            $user->email_signature = $_POST['taEmailSignature'];

            // Update or create the user
            if ( $user_id ) {
                // If they're an admin, then we need to adjust the company
                if ( ( $this->user->has_permission( 8 ) ) )
                    $user->company_id = $_POST['sCompany'];

                $user->save();
                $this->notify( _('Your user has been successfully updated!') );
            } else {
                // If they're an admin, then we need to adjust the company, if not, use the same as the user
                $user->company_id = ( ( $this->user->has_permission( 8 ) ) ) ? $_POST['sCompany'] : $this->user->company_id;

                try {
                    $user->create();
                    $this->notify( _('Your user has been successfully created!') );
                } catch ( ModelException $e ) {
                    switch ( $e->getCode() ) {
                        case ActiveRecordBase::EXCEPTION_DUPLICATE_ENTRY:
                            // Let's see if we can get the user they were trying to get
                            $user->get_by_email( $user->email, false );

                            // Let them know what happened
                            $this->notify( _('Your user was not created. The user already existed, we have redirected you to edit the existing user.' ), false );

                            return new RedirectResponse( url::add_query_arg( 'uid', $user->id, '/users/add-edit/' ) );
                        break;

                        default:
                            // Don't know what happened
                            $this->notify( _('An error occurred while trying to add your user: ') . $e->getCode() . '. Please create a support request so we can fix it.', false );

                            return new RedirectResponse( '/users/add-edit/' );
                        break;
                    }
                }
            }

            // Set the password
            if ( !empty( $_POST['tPassword'] ) )
                $user->set_password( $_POST['tPassword'] );

            return new RedirectResponse('/users/');
        }

        $this->resources
            ->javascript( 'fileuploader', 'media-manager', 'users/add-edit' )
            ->css( 'media-manager' );

        // Generate the form
        $template_response
            ->set( array( 'form' => $ft->generate_form(), 'user_id' => $user_id, 'user_photo' => $user->photo ) )
            ->select( 'users', 'users/add' );

        return $template_response;
    }

     /***** REDIRECTS *****/

    /**
     * Control
     *
     * @return RedirectResponse
     */
    protected function control() {
        if ( !isset( $_GET['uid'] ) )
            return new RedirectResponse('/accounts/');

        // Instantiate Class
        $account = new Account;

        // Get the user they are trying to control
        $user = new User();
        $user->get( $_GET['uid'] );

        // Make sure they're not trying to control someone with the same role or a higher role
        if ( $this->user->role <= $user->role || ( !$this->user->has_permission( User::ROLE_ADMIN ) && $this->user->company_id != $user->company_id ) )
            return new RedirectResponse( '/accounts/' );

        // Get the websites that user controls
        $accounts = $account->get_by_user( $_GET['uid'] );

        set_cookie( AUTH_COOKIE, base64_encode( security::encrypt( $user->email, security::hash( COOKIE_KEY, 'secure-auth' ) ) ), 172800 );
        set_cookie( 'wid', $accounts[0]->id, 172800 ); // 2 days
        set_cookie( 'action', base64_encode( security::encrypt( 'bypass', ENCRYPTION_KEY ) ), 172800 ); // 2 days

        $url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : str_replace( 'admin', 'account', $_SERVER['HTTP_HOST'] ) ) . '/';

        return new RedirectResponse( $url );
    }

    /***** AJAX *****/

    /**
     * List Users
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`contact_name`', '`email`', '`role`' );
        $dt->search( array( '`contact_name`' => true, '`email`' => true ) );

        // If they are below 8, that means they are a partner
		if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
			$dt->add_where( ' AND `company_id` = ' . (int) $this->user->company_id );

        // Get accounts
        $users = $this->user->list_all( $dt->get_variables() );
        $dt->set_row_count( $this->user->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this user? This cannot be undone.');
        $delete_user_nonce = nonce::create( 'delete' );

        /**
         * @var User $u
         */
        if ( is_array( $users ) )
        foreach ( $users as $u ) {
            $role = $u->get_role_name( $u->role );

            $data[] = array(
                $u->contact_name . '<div class="actions">' .
                    '<a href="/users/add-edit/?uid=' . $u->id . '" title="' . $u->contact_name . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'uid' => $u->id, '_nonce' => $delete_user_nonce ), '/users/delete/' ) . '" title="' . _('Delete User') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a></div>'
                , '<a href="mailto:' . $u->email . '" title="' . _('Email User') . '">' . $u->email . '</a>'
                , $role
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * List Articles
     *
     * @return DataTableResponse
     */
    protected function list_articles() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $user_id = (int) $_GET['uid'];

        // Set Order by
        $dt->order_by( 'kba.`title`', 'kbc.`section`', 'category', 'views', 'rating' );
        $dt->search( array( 'kba.`title`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false, 'kbp.`name`' => false ) );
        $dt->add_where( " AND ( kbar.`user_id` = $user_id OR kbar.`user_id` IS NULL ) AND kbav.`user_id` = $user_id" );

        // Get accounts
        $article = new KnowledgeBaseArticle();
        $articles = $article->list_by_user( $dt->get_variables() );
        $dt->set_row_count( $article->count_by_user( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        /**
         * @var KnowledgeBaseArticle $article
         */
        if ( is_array( $articles ) )
        foreach ( $articles as $article ) {
            switch ( $article->rating ) {
                case KnowledgeBaseArticleRating::POSITIVE:
                    $rating = _('Yes');
                break;

                case KnowledgeBaseArticleRating::NEGATIVE:
                    $rating = '<strong class="highlight">' . _('No') . '</strong>';
                break;

                default:
                    $rating = _('N/A');
                break;
            }

            $data[] = array(
                $article->title
                , ucwords( $article->section )
                , $article->category
                , $article->page
                , number_format( (int) $article->views )
                , $rating
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete a user
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['uid'] ) )
            return $response;

        // Get the user
        $user = new User();
        $user->get( $_GET['uid'] );

        // Deactivate user
        if ( $user->id && 1 == $user->status ) {
            $user->status = 0;
            $user->save();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }

    /**
     * Upload File
     *
     * @return AjaxResponse
     */
    protected function upload_file() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['fn'] ), _('File failed to upload') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File( 'retailcatalog.us' );
        $uploader = new qqFileUploader( array( 'pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml' ), 6144000 );

        // Change the name
        $extension = strtolower( f::extension( $_GET['qqfile'] ) );
        $file_name =  format::slug( f::strip_extension( $_GET['fn'] ) ) . '.' . $extension;

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $file_path = $file->upload_file( $result['file_path'], $file_name );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        $response->add_response( 'name', $file_name );
        $response->add_response( 'url', $file_path );

        return $response;
    }

    /**
     * Delete File
     *
     * @return AjaxResponse
     */
    protected function delete_file() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['key'] ), _('Image failed to upload') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate classes
        $file = new File( 'retailcatalog.us' );

        // Delete from Amazon
        $file->delete_file( $_GET['key'] );

        // Remove that file
        jQuery('#file-' . format::slug( $_GET['key'] ) )->remove();

        $files = $file->list_files();

        // Get the files, see how many there are
        if ( 0 == count( $files ) )
            jQuery('#file-list')->append( '<p class="no-files">' . _('You have not uploaded any files.') . '</p>'); // Add a message

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Get Files
     *
     * AJAX Call, returns a list of files, based on a pattern and pagination parameters
     *
     * @return AjaxResponse
     */
    protected function get_files() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $pattern = isset( $_GET['pattern'] ) ? $_GET['pattern'] : null;
        $page = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
        $limit = 50;
        $offset = $page * $limit;

        $file = new File( 'retailcatalog.us' );
        $files = $file->search_files($pattern, $offset, $limit);

        $files_response = array();
        foreach ( $files as $file_name => $file ) {
            $date = new DateTime();
            $date->setTimestamp( $file['time'] );
            $file_path = 'http://s3.amazonaws.com/retailcatalog.us/' . $file_name;
            $file_id = format::slug( $file_name );

            $files_response[] = array(
                'name' => $file_id
                , 'url' => $file_path
                , 'date' => $date->format( 'F jS, Y')
            );
        }

        // Add the response
        $response->add_response( 'files', $files_response );

        return $response;
    }

    /**
     * Update Photo
     * @return AjaxResponse
     */
    protected function update_photo() {
        $response = new AjaxResponse( $this->verified() );
        if ( $response->has_error() ) {
            $response->notify('Something happened, please try again', false);
            return $response;
        }

        $user = new User();
        $user->get( $_REQUEST['user_id'] );
        if ( !$user->id ) {
            $response->notify('Bad user, please try again', false);
            return $response;
        }

        $user->photo = $_REQUEST['photo'];
        $user->save();
        $response->notify('Profile Picture Updated!');
        return $response;
    }
}