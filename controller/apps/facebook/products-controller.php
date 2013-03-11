<?php
class ProductsController extends BaseController {
    const APP_ID = '163636730371197';
    const APP_SECRET = '3dbe8bc58cf03523ad51603654ca50a6';
    const APP_URI = 'op-products';

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->section = _('Products');
    }

    /**
     * Products
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
            $products = new Products();

            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            if ( $page_id ) {
                $website = $products->get_connected_website( $page_id );
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
                $products->connect( $page_id, $_POST['tFBConnectionKey'] );

                $website = $products->get_connected_website( $page_id );
                $website_row->set_value( $website->title );
                $success = true;
            }

            // Get the string
            $form = $form->generate_form();
        }

        $response = $this->get_template_response( 'facebook/products/index', 'Connect' );
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
        $products = new Products;
        $signed_request = $fb->getSignedRequest();
        $tab = $products->get_tab( $signed_request['page']['id'] );

        // If it's secured, make the images secure
        if ( security::is_ssl() )
            $tab = ( stristr( $tab, 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab );

        // Add Admin URL
        if( $signed_request['page']['admin'] ) {
            $admin = '<p><strong>Admin:</strong> <a href="#" onclick="top.location.href=' . "'";
            $admin .= url::add_query_arg(
                'app_data'
                , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) )
                , 'http://apps.facebook.com/' . self::APP_URI . '/'
            );
            $admin .= "'" . ';">Update Settings</a></p>';

            $tab = $admin . $tab;
        }

        $this->resources->css( 'facebook/products' );
        $response = $this->get_template_response( 'facebook/products/tab' );
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
    protected function settings() {
        $fb = new Fb( self::APP_ID, self::APP_SECRET, self::APP_URI );

        // Redirect to correct location
        return new RedirectResponse( url::add_query_arg(
            'app_data'
            , url::encode( array( 'uid' => security::encrypt( $fb->user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $_GET['fb_page_id'], 'sEcrEt-P4G3!' ) ) )
            , '/facebook/products/'
        ) );
    }
}