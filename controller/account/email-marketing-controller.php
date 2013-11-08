<?php
class EmailMarketingController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/';
        $this->section = 'email-marketing';
        $this->title = _('Email Marketing');
    }

    /**
     * Show dashboard
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        $email_message = new EmailMessage();
        $messages = $email_message->get_dashboard_messages_by_account( $this->user->account->id );

        $email = new Email();
        $subscribers = $email->get_dashboard_subscribers_by_account( $this->user->account->id );

        // Get report total
        $ac = EmailMarketing::setup_ac( $this->user->account );
        $ac->setup_campaign();

        $message = $messages[0];
        $email = $ac->campaign->report_totals( $message->ac_campaign_id );

        // Get the bar chart
        $bar_chart = Analytics::bar_chart( $email );

        $this->resources
            ->css( 'email-marketing/dashboard' )
            ->javascript( 'swfobject', 'email-marketing/dashboard');

        return $this->get_template_response( 'index' )
            ->kb( 72 )
            ->add_title( _('Dashboard') )
            ->select( 'email-dashboard' )
            ->set( compact( 'messages', 'subscribers', 'email', 'bar_chart', 'email_count' ) );
    }

    /**
     * Settings
     *
     * @return TemplateResponse
     */
    protected function settings() {
         // Instantiate classes
        $form = new FormTable( 'fSettings' );

        // Get settings
        $settings_array = array( 'from_name', 'from_email', 'timezone', 'remove-header-footer' );
        $settings = $this->user->account->get_settings( $settings_array );

        // Create form
        $form->add_field( 'text', _('From Name'), 'from_name', $settings['from_name'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('From Email'), 'from_email', $settings['from_email'] )
            ->attribute( 'maxlength', '200' )
            ->add_validation( 'email', _('The "From Email" field must contain a valid email' ) );

        $form->add_field( 'select', _('Timezone'), 'timezone', $settings['timezone'] )
            ->options( data::timezones( false, false, true ) );

        $form->add_field( 'checkbox', _('Remove Header and Footer from Custom Emails'), 'remove-header-footer', $settings['remove-header-footer'] );

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }

            $this->user->account->set_settings( $new_settings );

            // Edit sender address
            $settings = $this->user->account->get_settings( 'sendgrid-username', 'sendgrid-password', 'address', 'city', 'state', 'zip' );
            $sendgrid = new SendGridAPI( $this->user->account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
            $sendgrid->setup_sender_address();
            $name = ( empty ( $_POST['from_name'] ) ) ? $this->user->contact_name : $_POST['from_email'];
            $email = ( empty( $_POST['from_email'] ) ) ? 'noreply@' . url::domain( $this->user->account->domain, false ) : $_POST['from_email'];
            $sendgrid->sender_address->edit( $this->user->account->id, $name, $email, $settings['address'], $settings['city'], $settings['state'], $settings['zip'] );

            $this->notify( _('Your email settings have been successfully saved!') );

            // Refresh to get all the changes
            return new RedirectResponse('/email-marketing/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->kb( 84 )
            ->add_title( _('Settings') )
            ->select( 'settings' )
            ->set( array( 'form' => $form->generate_form() ) );
    }
}


