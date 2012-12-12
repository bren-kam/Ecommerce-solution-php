<?php
class SweepstakesController extends BaseController {
    const APP_ID = '113993535359575';
    const APP_SECRET = '16937c136a9c5237b520b075d0ea83c8';
    const APP_URI = 'op-sweepstakes';

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->section = _('Sweepstakes');
    }

    /**
     * Sweepstakes
     *
     * @return TemplateResponse
     */
    protected function index() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI );
        $form = new stdClass();
        $success = $website = false;

        // Make sure they are validly editing the app
        if ( isset( $_REQUEST['app_data'] ) ) {
            // Instantiate App
            $sweepstakes = new Sweepstakes();

            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            if ( $page_id ) {
                $website = $sweepstakes->get_connected_website( $page_id );
                $website_title = $website->title;
            } else {
                $website_title = 'N/A';
            }

            $form = new FormTable( 'fSweepstakes' );
            $form->submit( 'Connect' );

            $website_row = $form->add_field( 'row', _('Website'), $website_title );

            $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
                ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

            $form->add_field( 'hidden', 'app_data', $_REQUEST['app_data'] );

            // Make sure it's a valid request
            if( $other_user_id == $fb->user_id && $page_id && $form->posted() ) {
                $sweepstakes->connect( $page_id, $_POST['tFBConnectionKey'] );

                $website = $sweepstakes->get_connected_website( $page_id );
                $website_row->set_value( $website->title );
                $success = true;
            }

            // Get the string
            $form = $form->generate_form();
        }
		
        $response = $this->get_template_response( 'facebook/sweepstakes/index', 'Connect' );
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
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI, true );
        $sweepstakes = new Sweepstakes;
        $signed_request = $fb->getSignedRequest();
		
        $tab = $sweepstakes->get_tab( $signed_request['page']['id'], $signed_request['page']['liked'] );

        // Add on the Sweepstakes Rules URL if it's not empty
		if ( isset( $tab->contest_rules_url ) && !empty( $tab->contest_rules_url ) )
			$tab->content .= '<p><a href="' . $tab->contest_rules_url . '" title="View Sweepstakes Rules" target="_blank">View Sweepstakes Rules</a></p>';

        // If it's secured, make the images secure
        if ( security::is_ssl() )
            $tab->content = ( stristr( $tab->content, 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab->content ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab->content );

        $form = new FormTable( 'fSignUp' );
        $form->submit( 'Subscribe' );

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
            $sweepstakes->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );
            $success = true;
        }

        // Add Admin URL
        if( $signed_request['page']['admin'] ) {
            $admin = '<p><strong>Admin:</strong> <a href="#" onclick="top.location.href=' . "'";
            $admin .= url::add_query_arg(
                'app_data'
                , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) )
                , 'http://apps.facebook.com/' . self::APP_URI . '/'
            );
            $admin .= "'" . ';">Update Settings</a></p>';

            $tab->content = $admin . $tab->content;
        }
		
		// Get page information
		$page = $fb->api( '/' . $signed_request['page']['id'] );
		
        $response = $this->get_template_response( 'facebook/sweepstakes/tab' );
        $response
            ->set_sub_includes( 'facebook/tabs' )
            ->set( array(
                'sweepstakes' => $tab
                , 'success' => $success
                , 'form' => $form->generate_form()
				, 'signed_request' => $signed_request
                , 'app_id' => self::APP_ID
                , 'url' => url::add_query_arg( 'sk', 'app_' . self::APP_ID, $page['link'] )
        ) );

        return $response;
    }


    /**
     * Settings
     *
     * @return RedirectResponse
     */
    public function settings() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI );

        // Redirect to correct location
        return new RedirectResponse( url::add_query_arg(
            'app_data'
            , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) )
            , '/facebook/sweepstakes/'
        ) );
    }
}