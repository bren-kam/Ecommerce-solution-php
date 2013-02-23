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

        // Setup variables
        $email = new AnalyticsEmail();
        $email_count = count( $messages );
        $i = 0;

        if ( is_array( $messages ) ) {
        	// Get the analytics data
        	while ( $i < $email_count && !$email->mc_campaign_id ) {
                $message = $messages[$i];

                try {
                    $email->get_complete( $message->mc_campaign_id, $this->user->account->id );
                } catch( ModelException $e ) {
                    $this->notify( _('An error occurred while trying to get your email') . ', "' . $message->subject . '". ' . _('Please contact an online specialist for assistance.'), false );
                }

        		$i++;
        	}
        }

        $bar_chart = Analytics::bar_chart( $email );

        $this->resources
            ->css( 'email-marketing/dashboard' )
            ->javascript( 'swfobject', 'email-marketing/dashboard');

        return $this->get_template_response( 'index' )
            ->add_title( _('Dashboard') )
            ->select( 'dashboard' )
            ->set( compact( 'messages', 'subscribers', 'email', 'bar_chart', 'email_count' ) );
    }
}


