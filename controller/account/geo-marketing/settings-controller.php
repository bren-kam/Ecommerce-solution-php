<?php

class SettingsController extends BaseController{

    public function __construct() {
        parent::__construct();
        $this->title = _('Settings | GeoMarketing');
    }

    /**
     * Index
     * @return TemplateResponse
     */
    public function index() {

        $settings_array = [
            'yext-review-send-email'
            , 'yext-review-email-address'
        ];

        $settings = $this->user->account->get_settings( $settings_array );

        $form = new BootstrapForm( 'fSettings' );

        $form->add_field( 'checkbox', 'Do not send me an email when a new Review is written', 'cbYextReviewDisableEmail', $settings['yext-review-disable-email'] );
        $form->add_field( 'text', 'Email Address', 'cbYextReviewEmailAddress', $settings['yext-review-email-address'] )
            ->attribute( 'placeholder', "Use Account's email" );

        if ( $form->posted() ) {
            $settings['yext-review-disable-email'] = isset( $_POST['cbYextReviewDisableEmail'] );
            $settings['yext-review-email-address'] = $_POST['cbYextReviewEmailAddress'];
            $this->user->account->set_settings( $settings );
            $this->notify('GeoMarketing Settings Updated');
            return new RedirectResponse('/geo-marketing/settings');
        }

        $form_html = $form->generate_form();

        return $this->get_template_response( 'geo-marketing/settings/index' )
            ->menu_item('geo-marketing/settings')
            ->set( compact( 'form_html' ) );
    }


}