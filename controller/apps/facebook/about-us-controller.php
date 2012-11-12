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
        $about_us = new AboutUs();

        // Make sure they are validly editing the app
        if ( isset( $_REQUEST['app_data'] ) ) {
            // Get App Data
            $app_data = url::decode( $_REQUEST['app_data'] );
            $other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
            $page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );

            $form = new FormTable( 'fAboutUs' );

            $form->add_field( 'text', _('Facebook Connection Key'), 'tFBConnectionKey' )
                ->add_validation( 'req', _('The "Facebook Connection Key" field is required') );

            $form->add_field( 'hidden', $_REQUEST['app_data'] );

            // Make sure it's a valid request
            if( $other_user_id == $this->fb->user_id && $page_id ) {
                if( nonce::verify( $_POST['_nonce'], 'connect-to-field' ) ) {
                    $errs = $v->validate();

                    // if there are no errors
                    if( empty( $errs ) )
                        $about_us->connect( $page_id, $_POST['tFBConnectionKey'] );
                }
            }

            if( $page_id )
                $website = $about_us->get_connected_website( $page_id );
        }

        add_footer('<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "' . self::APP_ID . '", status: true, cookie: true,
             xfbml: true});
	FB.setSize({ width: 720, height: 500 });
  };
  (function() {
    var e = document.createElement("script"); e.async = true;
    e.src = document.location.protocol +
      "//connect.facebook.net/en_US/all.js";
    document.getElementById("fb-root").appendChild(e);
  }());
</script>');

        return new JsonResponse( $api_request->get_response() );
    }
}