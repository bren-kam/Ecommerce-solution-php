<?php
class EmailSignUpController extends BaseController {
    const APP_ID = '165553963512320';
    const APP_SECRET = 'b4957be2dbf78991750bfa13f844cb68';
    const APP_URI = 'op-email-sign-up';

    /**
     * FB Class
     *
     * @var FB $fb;
     */
    protected $fb;

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI );
        $this->section = _('Email Sign Up');
    }

    /**
     * About Us
     *
     * @return JsonResponse
     */
    protected function index() {
        $form = new stdClass();
        $success = $website = false;

        // Make sure they are validly editing the app
        if ( isset( $_REQUEST['app_data'] ) ) {
            // Instantiate App
            $email_sign_up = new EmailSignUp();

            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            if ( $page_id ) {
                $website = $email_sign_up->get_connected_website( $page_id );
                $website_title = $website->title;
            } else {
                $website_title = 'N/A';
            }

            $form = new FormTable( 'fEmailSignUp' );

            $website_row = $form->add_field( 'row', _('Website'), $website_title );

            $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
                ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

            $form->add_field( 'hidden', 'app_data', $_REQUEST['app_data'] );

            // Make sure it's a valid request
            if( $other_user_id == $this->fb->user_id && $page_id && $form->posted() ) {
                $email_sign_up->connect( $page_id, $_POST['tFBConnectionKey'] );

                $website = $email_sign_up->get_connected_website( $page_id );
                $website_row->set_value( $website->title );
                $success = true;
            }
        }

        $response = $this->get_template_response( 'facebook/email-sign-up/index', 'Connect' );
        $response
            ->set_sub_includes('facebook')
            ->set( array( 'form' => $form, 'app_id' => self::APP_ID, 'success' => $success, 'website' => $website ) );

        return $response;
    }

    /**
     * Show the tab
     *
     * @return TemplateResponse
     */
    public function tab() {
        // Setup variables
        $email_sign_up = new EmailSignUp;
        $signed_request = $this->fb->getSignedRequest();


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

        if ( $form->posted() )
            $success = $email_sign_up->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );

        $tab = $email_sign_up->get_tab( $signed_request['page']['id'] );
        $tab .= $form->generate_form();

        if ( $success )
            $tab = '<p>Your have been successfully added to our email list!</p>' . $tab;

        // If it's secured, make the images secure
        if ( security::is_ssl() )
            $tab = ( stristr( $tab, 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab );

        // Add Admin URL
        if( $signed_request['page']['admin'] ) {
            $admin = '<p><strong>Admin:</strong> <a href="#" onclick="top.location.href=' . "'";
            $admin .= url::add_query_arg(
                'app_data'
                , url::encode( array( 'uid' => security::encrypt( $this->fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) )
                , 'http://apps.facebook.com/' . self::APP_URI . '/'
            );
            $admin .= "'" . ';">Update Settings</a></p>';

            $tab = $admin . $tab;
        }

        $response = $this->get_template_response( 'facebook/email-sign-up/tab' );
        $response
            ->set_sub_includes( 'facebook/tabs' )
            ->set( compact( 'tab' ) );

        return $response;
    }


    /**
     * Settings
     *
     * @return RedirectResponse
     */
    public function settings() {
        // Redirect to correct location
        return new RedirectResponse( url::add_query_arg(
            'app_data'
            , url::encode( array( 'uid' => security::encrypt( $this->fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) )
            , '/facebook/email-sign-up/'
        ) );
    }
}