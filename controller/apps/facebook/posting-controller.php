<?php
class PostingController extends BaseController {
    const APP_ID = '484616121701707';
    const APP_SECRET = 'ff73583d0f102b3a7131871e90712dda';
    const APP_URI = 'op-posting';

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->section = _('Posting');
    }

    /**
     * Posting
     *
     * @return TemplateResponse|HtmlResponse
     */
    protected function index() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI, false, array( 'scope' => 'manage_pages,publish_actions' ) );
        $success = false;
        $options = array();
        $posting = new Posting();

        // For auto-reconnecting to the posting app
        if ( isset( $_REQUEST["code"] ) ) {
            $token_url = url::add_query_arg( array(
                    'client_id' => self::APP_ID
                    , 'redirect_uri' => url::add_query_arg( array(
                        'fb_page_id' => $_REQUEST['fb_page_id']
                        , 'gsr_redirect' => $_REQUEST['gsr_redirect']
                    ), 'http://apps.facebook.com/' . self::APP_URI . '/'
                )
                , 'client_secret' => self::APP_SECRET
                , 'code' => $_REQUEST['code']
                , 'display' => 'popup'
            ), 'https://graph.facebook.com/oauth/access_token' );

            $response = file_get_contents( $token_url );
            $params = null;
            parse_str( $response, $params );

            $fb->setAccessToken( $params['access_token'] );
            $fb->setExtendedAccessToken();
			
            $posting->update_access_token( $fb->getAccessToken(), $_REQUEST['fb_page_id'] );

            return new HtmlResponse("<script>top.location.href='" . $_REQUEST['gsr_redirect'] . "'</script>");
        }

        $form = new FormTable( 'fFacebookSite' );
        $form->submit( 'Connect' );

        $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
            ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

        // Make sure it's a valid request
        if ( $form->posted() ) {
            $fb->setExtendedAccessToken();
            $success = $posting->connect( $fb->user_id, $_POST['sFBPageID'], $fb->getAccessToken(), $_POST['tFBConnectionKey'] );
        }

        // See if we're connected
        $connected = $posting->connected( $fb->user_id );

        // Connected pages
        $pages = ( $connected ) ? $posting->get_connected_pages( $fb->user_id ) : array();

        // Get the accounts of the user
        $accounts = $fb->api( "/{$fb->user_id}/accounts" );
		
        // Get a list of the pages they have available
        if ( is_array( $accounts['data'] ) )
        foreach ( $accounts['data'] as $page ) {
            if ( 'Application' == $page['category'] || in_array( $page['id'], $pages ) )
                continue;

            $options[$page['id']] = $page['name'];
        }
		
        // Add the last field (didn't have validation so it can be after the posting)
        $form->add_field( 'select', 'Facebook Page', 'sFBPageID' )
			->options( $options );

        // Get template response
        $response = $this->get_template_response( 'facebook/posting/index', 'Connect' );
        $response
            ->set_sub_includes('facebook')
            ->set( array( 'form' => $form->generate_form(), 'app_id' => self::APP_ID, 'success' => $success, 'connected' => $connected, 'pages' => $pages ) );

        return $response;
    }

    /**
     * Settings
     *
     * @return RedirectResponse
     */
    protected function settings() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI, false, array( 'scope' => 'manage_pages,publish_stream' ) );

        // Redirect to correct location
        return new RedirectResponse( url::add_query_arg(
            'app_data'
            , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) )
            , '/facebook/posting/'
        ) );
    }
}