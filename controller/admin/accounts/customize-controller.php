<?php
class CustomizeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/customize/';
        $this->section = 'accounts';
        $this->title = 'Customize';
    }

    /**
     * List Companies
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
            return new RedirectResponse('/accounts/');

        return $this->get_template_response( 'index' )
            ->kb( 9 )
            ->select( 'customize', 'view' );
    }

    /**
     * Add/Edit A Company
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function stylesheet() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) || !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get Accoubt
        $account = new Account();
        $account->get( $_GET['aid'] );

        $css = $account->get_settings('css');

        $this->resources
            ->css('accounts/customize/css')
            ->javascript('accounts/customize/css');

        return $this->get_template_response( 'css' )
            ->kb( 10 )
            ->select( 'customize', 'css' )
            ->set( compact( 'css', 'account' ) )
            ->add_title( _('CSS') );
    }

     /**
     * Add/Edit Favicon
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function favicon() {
         if (!$this->user->has_permission(User::ROLE_ADMIN) || !isset($_GET['aid']))
            return new RedirectResponse('/accounts/');
         
        // Get Account
        $account = new Account();
        $account->get($_GET['aid']);

        $favicon = $account->get_settings("favicon");
        $this->resources->javascript('fileuploader', 'accounts/customize/favicon');

        return $this->get_template_response('favicon')
                        ->set('favicon', $favicon)
                        ->add_title(_('Favicon'));
    }

    /***** AJAX *****/

    /**
     * Save CSS
     *
     * @return AjaxResponse
     */
    protected function save_css() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());
       
        // Get account
        $account = new Account();
        $account->get($_GET['aid']);
        
        // If there is an error or now user id, return
        if ( $response->has_error() )
             return $response;

        $account->set_settings( array( 'css' => $_POST['css'] ) );
        $response->notify( 'CSS has been successfully updated!' );

        return $response;
    }

    /**
     * Upload Favicon
     *
     * @return AjaxResponse
     */
    protected function upload_favicon() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());

        // Get account
        $account = new Account();
        $account->get($_GET['aid']);

        // If there is an error or now user id, return
        if ( $response->has_error() )
             return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File( 'websites' . Config::key('aws-bucket-domain') );
        $account_file = new AccountFile();

        // Create uploader
        $uploader = new qqFileUploader( array( 'ico' ), 6144000 );

        // Upload file
        $result = $uploader->handleUpload('gsr_');

        $response->check( $result['success'], _('Failed to upload favicon') );

        // If there is an error or now user id, return
        if ( $response->has_error() ) {
             $response->add_response( "error", $result["error"] );
             return $response;
        }

        //Create favicon name
        $favicon_name =  'favicon.ico';

        // Create the different versions we need
        $favicon_dir = $account->id . '/favicon/';

        // Normal and large
        $file_url =  $file->upload_file( $result['file_path'], $favicon_name, $favicon_dir );


        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Create account file
        $account_file->website_id = $account->id;
        $account_file->file_path = $file_url;
        $account_file->create();

        $response->add_response( 'file', $account_file->file_path );
        // Update account favicon
        $account->set_settings( array( 'favicon' => $account_file->file_path ) );
        jQuery('#dFaviconContent')->html('<img src="' . $account_file->file_path . '" style="padding-bottom:10px" alt="' . _('Favicon') . '" /><br />');

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}