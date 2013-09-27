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

        // Create new form table
        $ft = new FormTable( 'fCustomCSS' );

        $ft->submit(  _('Save') );

        $ft->add_field( 'textarea', _('CSS'), 'taCSS', $account->get_settings('css') );

        // Update the company if posted
        if ( $ft->posted() ) {
            $account->set_settings( array( 'css' => $_POST['taCSS'] ) );
            $this->notify( 'CSS has been successfully updated!');
        }

        return $this->get_template_response( 'css' )
            ->kb( 10 )
            ->select( 'customize', 'css' )
            ->set( 'form', $ft->generate_form() )
            ->add_title( _('CSS') );
    }

       /**
     * Add/Edit A Company
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function favicon() {
         if (!$this->user->has_permission(User::ROLE_ADMIN) || !isset($_GET['aid']))
            return new RedirectResponse('/accounts/');
         
        // Get Accoubt
        $account = new Account();
        $account->get($_GET['aid']);

        $favicon = $account->get_settings("favicon");
        $this->resources->javascript('fileuploader', 'customize/favicon');

        return $this->get_template_response('favicon')
                        ->set('favicon', $favicon)
                        ->add_title(_('CSS'));
    }

    /*     * *** AJAX **** */

    /**
     * Upload Logo
     *
     * @return AjaxResponse
     */
    protected function upload_logo() {
      
        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());
       
        
        $account = new Account();
        $account->get($_GET['aid']);
        
        // If there is an error or now user id, return
        if ($response->has_error())
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File();
        $account_file = new AccountFile();

        $uploader = new qqFileUploader(array('gif', 'jpg', 'jpeg', 'png'), 6144000);

        // Upload file
        $result = $uploader->handleUpload('gsr_');

        $response->check($result['success'], _('Failed to upload image'));

        // If there is an error or now user id, return
        if ($response->has_error())
            return $response;

        $filename_name = 'favicon';

        // Create the different versions we need
        $favicon_dir = $account->id . '/favicon/';

        // Normal and large
        $file->upload_image($result['file_path'], $favicon_name, 700, 200, 'websites', $favicon_dir);
        $file_url = $file->upload_image($result['file_path'], $favicon_name, 700, 700, 'websites', $favicon_dir . 'large/');
        $file_url = 'http://websites.retailcatalog.us/' . $account->id . '/favicon/' . $file_url;

        // Delete file
        if (is_file($result['file_path']))
            unlink($result['file_path']);

        // Create account file
        $account_file->website_id = $account->id;
        $account_file->file_path = $file_url;
        $account_file->create();

        // Update account logo
        $account->set_settings(array('favicon' => $account_file->file_path));
           

        jQuery('#dLogoContent')->html('<img src="' . $account_file->file_path . '" style="padding-bottom:10px" alt="' . _('Logo') . '" /><br />');

        // Add the response
        $response->add_response('jquery', jQuery::getResponse());

        return $response;
    }
}