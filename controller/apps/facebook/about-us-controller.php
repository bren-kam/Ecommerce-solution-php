<?php
class AboutUsController extends BaseController {
    const APP_ID = '233746136649331';
    const APP_SECRET = '298bb76cda7b2c964e0bf752cf239799';
    const APP_URI = 'op-about-us';

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

        $this->fb = new FB( self::APP_ID, self::APP_SECRET, self::APP_URI );
        $this->section = _('About Us');
    }

    /**
     * Reports Search Page
     *
     * @return JsonResponse
     */
    protected function index() {
        $form = new stdClass();

        // Make sure they are validly editing the app
        if ( isset( $_REQUEST['app_data'] ) ) {
            // Instantiate App
            $about_us = new AboutUs();

            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            if ( $page_id ) {
                $website = $about_us->get_connected_website( $page_id );
                $website_title = $website->title;
            } else {
                $website_title = 'N/A';
            }

            $form = new FormTable( 'fAboutUs' );

            $website_row = $form->add_field( 'row', _('Website'), $website_title );

            $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
                ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

            $form->add_field( 'hidden', 'app_data', $_REQUEST['app_data'] );

            // Make sure it's a valid request
            if( $other_user_id == $this->fb->user_id && $page_id && $form->posted() ) {
                $about_us->connect( $page_id, $_POST['tFBConnectionKey'] );

                $website = $about_us->get_connected_website( $page_id );
                $website_row->set_value( $website->title );
            }
        }

        $response = $this->get_template_response( 'facebook/about-us', 'Connect' );
        $response->set( array( 'form' => $form, 'app_id' => self::APP_ID ) );

        return $response;
    }
}