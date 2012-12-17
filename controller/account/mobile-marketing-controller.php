<?php
class MobileMarketingController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'mobile-marketing/';
        $this->title = 'Mobile Marketing';
    }

    /**
     * Allow them to login to mobile marketing
     *
     * @return TemplateResponse
     */
    protected function index() {
        $settings = $this->user->account->get_settings( 'trumpia-username', 'trumpia-password' );

        $url = 'http://greysuitmobile.com/action/index.act?mode=signin';
        $post = array(
            'id' => $settings['trumpia-username']
            , 'password' => $settings['trumpia-password']
            , 'version' => '2'
        );

        $trumpia_response = json_decode( curl::post( $url, $post ) );

        $logged_in = '1' == $trumpia_response->result;

        $response = $this->get_template_response( 'index' )
            ->select('mobile-marketing')
            ->set( compact( 'logged_in' ) );

        return $response;
    }

    /**
     * Trumpia Form
     *
     * @return CustomResponse
     */
    protected function trumpia_form() {
        $settings = $this->user->account->get_settings( 'trumpia-username', 'trumpia-password' );

        $response = new CustomResponse( $this->resources, 'mobile-marketing/trumpia-form' );
        $response->set( array(
                'trumpia_username' => $settings['trumpia-username']
                , 'trumpia_password' => $settings['trumpia-password']
            )
        );

        return $response;
    }
}


