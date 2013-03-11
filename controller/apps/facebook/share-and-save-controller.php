<?php
class ShareAndSaveController extends BaseController {
    const APP_ID = '118945651530886';
    const APP_SECRET = 'ef922d64f1f526079f48e0e0efa47fb7';
    const APP_URI = 'share-and-save';

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->section = _('Share and Save');
    }

    /**
     * Share and Save
     *
     * @return TemplateResponse
     */
    protected function index() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI );
        $form = new stdClass();
        $success = $website = $page_id = false;

        // Make sure they are validly editing the app
        if ( isset( $_REQUEST['app_data'] ) ) {
            // Instantiate App
            $share_and_save = new ShareAndSave();

            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            if ( $page_id ) {
                $website = $share_and_save->get_connected_website( $page_id );
                $website_title = $website->title;
            } else {
                $website_title = 'N/A';
            }

            $form = new FormTable( 'fConnect' );
            $form->submit( 'Connect' );

            $website_row = $form->add_field( 'row', _('Website'), $website_title );

            $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
                ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

            $form->add_field( 'hidden', 'app_data', $_REQUEST['app_data'] );

            // Make sure it's a valid request
            if( $other_user_id == $fb->user_id && $page_id && $form->posted() ) {
                $share_and_save->connect( $page_id, $_POST['tFBConnectionKey'] );

                $website = $share_and_save->get_connected_website( $page_id );
                $website_row->set_value( $website->title );
                $success = true;
            }

            // Get the string
            $form = $form->generate_form();
        }

        $response = $this->get_template_response( 'facebook/share-and-save/index', 'Connect' );
        $response
            ->set_sub_includes('facebook')
            ->set( array( 'form' => $form, 'page_id' => $page_id, 'app_id' => self::APP_ID, 'success' => $success, 'website' => $website ) );

        return $response;
    }

    /**
     * Show the tab
     *
     * @return TemplateResponse
     */
    protected function tab() {
        // Setup variables
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI, true );
        $share_and_save = new ShareAndSave;
        $signed_request = $fb->getSignedRequest();

        $tab = $share_and_save->get_tab( $signed_request['page']['id'], $signed_request['page']['liked'] );

        // If it's secured, make the images secure
        if ( security::is_ssl() )
            $tab->content = ( stristr( $tab->content, 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab->content ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab->content );

        $form = new FormTable( 'fSignUp' );

        $form->add_field( 'text', 'Name', 'tName' )
            ->add_validation( 'req', 'The "Name" field is required' )
            ->add_validation( '!val=Name:', 'The "Name" field is required' );

        $form->add_field( 'text', 'Email', 'tEmail' )
            ->add_validation( 'req', 'The "Email" field is required' )
            ->add_validation( '!val=Email:', 'The "Email" field is required' )
            ->add_validation( 'email', 'The "Email" field must contain a valid email' );

        $form->add_field( 'hidden', 'signed_request', $_REQUEST['signed_request'] );

        $success = false;

        if ( $form->posted() ) {
            $share_and_save->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );
            $success = true;
        }

        $admin = '';

        // Add Admin URL
        if( $signed_request['page']['admin'] ) {
            $admin = '<p><strong>Admin:</strong> <a href="#" onclick="top.location.href=' . "'";
            $admin .= url::add_query_arg(
                'app_data'
                , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) )
                , 'http://apps.facebook.com/' . self::APP_URI . '/'
            );
            $admin .= "'" . ';">Update Settings</a></p>';
        }

		// Get page information
		$page = $fb->api( '/' . $signed_request['page']['id'] );

        $response = $this->get_template_response( 'facebook/share-and-save/tab' );
        $response
            ->set_sub_includes( 'facebook/tabs' )
            ->set( array(
                'share_and_save' => $tab
                , 'success' => $success
                , 'form' => $form->generate_form()
                , 'signed_request' => $signed_request
                , 'app_id' => self::APP_ID
                , 'url' => url::add_query_arg( 'sk', 'app_' . self::APP_ID, $page['link'] )
                , 'admin' => $admin
        ) );

        return $response;
    }

    /**
     * Settings
     *
     * @return RedirectResponse
     */
    protected function settings() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI );

        // Redirect to correct location
        return new RedirectResponse( url::add_query_arg(
            'app_data'
            , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) )
            , '/facebook/share-and-save/'
        ) );
    }
}