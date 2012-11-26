<?php
class FanOfferController extends BaseController {
    const APP_ID = '165348580198324';
    const APP_SECRET = 'dbd93974b5b4ee0c48ae34cb3aab9c4a';
    const APP_URI = 'op-fan-offer';

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
        $this->section = _('Fan Offer');
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
            $fan_offer = new FanOffer();

            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            if ( $page_id ) {
                $website = $fan_offer->get_connected_website( $page_id );
                $website_title = $website->title;
            } else {
                $website_title = 'N/A';
            }

            $form = new FormTable( 'fFanOffer' );

            $website_row = $form->add_field( 'row', _('Website'), $website_title );

            $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
                ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

            $form->add_field( 'hidden', 'app_data', $_REQUEST['app_data'] );

            // Make sure it's a valid request
            if( $other_user_id == $this->fb->user_id && $page_id && $form->posted() ) {
                $fan_offer->connect( $page_id, $_POST['tFBConnectionKey'] );

                $website = $fan_offer->get_connected_website( $page_id );
                $website_row->set_value( $website->title );
                $success = true;
            }

            // Get the string
            $form = $form->generate_form();
        }

        $response = $this->get_template_response( 'facebook/fan-offer/index', 'Connect' );
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
        $fan_offer = new FanOffer;
        $signed_request = $this->fb->getSignedRequest();

        $tab = $fan_offer->get_tab( $signed_request['page']['id'], $signed_request['page']['liked'] );

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

        if ( $form->posted() )
            $success = $fan_offer->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );

        // Add Admin URL
        if( $signed_request['page']['admin'] ) {
            $admin = '<p><strong>Admin:</strong> <a href="#" onclick="top.location.href=' . "'";
            $admin .= url::add_query_arg(
                'app_data'
                , url::encode( array( 'uid' => security::encrypt( $this->fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) )
                , 'http://apps.facebook.com/' . self::APP_URI . '/'
            );
            $admin .= "'" . ';">Update Settings</a></p>';

            $tab->content = $admin . $tab->content;
        }

        $response = $this->get_template_response( 'facebook/fan-offer/tab' );
        $response
            ->set_sub_includes( 'facebook/tabs' )
            ->set( array(
                'fan_offer' => $tab
                , 'success' => $success
                , 'form' => $form->generate_form()
                , 'signed_request' => $signed_request
        ) );

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
            , '/facebook/fan-offer/'
        ) );
    }
}