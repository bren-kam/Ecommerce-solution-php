<?php
class AnalyticsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'analytics/';
        $this->title = 'Analytics';
    }

    /**
     * Get dashboard
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        $analytics = new Analytics( $date_start, $date_end );

        try {
            $analytics->setup( $this->user->account );

            // Get all the data
            $records = $analytics->get_metric_by_date( 'visits' );
            $total = $analytics->get_totals();
            $traffic_sources = $analytics->get_traffic_sources_totals();
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/';
            return new RedirectResponse( '/analytics/oauth2/' );
        } catch ( ModelException $e ) {
            $this->notify( _('Please contact your online specialist in order to view analytics.'), false );
            return new RedirectResponse('/');
        }


        // Setup Javascript chart
        $visits_plotting_array = array();

        // Pie Chart
        $pie_chart = $analytics->pie_chart( $traffic_sources );

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = array($r_date, $r_value);
        }

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['page_views'] = $analytics->sparkline( 'page_views' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );

        $content_overview_pages = $analytics->get_content_overview();

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'index' )
            ->kb( 62 )
            ->add_title( _('Dashboard') )
            ->menu_item( 'analytics/index' )
            ->set( compact( 'sparklines', 'traffic_sources', 'pie_chart', 'visits_plotting_array', 'total', 'content_overview_pages', 'date_start', 'date_end' ) );
    }

    /**
     * Get Content Overview
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function content_overview() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/content-overview/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }

        // Get all the data
        $records = $analytics->get_metric_by_date( 'page_views' );
        $total = $analytics->get_totals();
        $content_overview_pages = $analytics->get_content_overview( 0 );

        // Setup Javascript chart
        $page_views_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $page_views_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['page_views'] = $analytics->create_sparkline( $records );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );
        $sparklines['time_on_page'] = $analytics->sparkline( 'time_on_page' );
        $sparklines['exit_rate'] = $analytics->sparkline( 'exit_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'content-overview' )
            ->add_title( _('Content Overview') )
            ->menu_item( 'analytics/content-overview' )
            ->set( compact( 'sparklines', 'page_views_plotting_array', 'total', 'content_overview_pages', 'date_start', 'date_end' ) );
    }

    /**
     * Get Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function page() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        if ( !isset( $_GET['p'] ) )
            return new RedirectResponse('/analytics/content-overview/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/page/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }
        $analytics->set_ga_filter( 'pagePath==' . $_GET['p'] );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'page_views' );
        $total = $analytics->get_totals();

        // Setup Javascript chart
        $page_views_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $page_views_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['page_views'] = $analytics->create_sparkline( $records );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );
        $sparklines['time_on_page'] = $analytics->sparkline( 'time_on_page' );
        $sparklines['exit_rate'] = $analytics->sparkline( 'exit_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'page' )
            ->add_title( _('Page') )
            ->menu_item( 'analytics/content-overview' )
            ->set( compact( 'sparklines', 'page_views_plotting_array', 'total', 'date_start', 'date_end' ) );
    }

    /**
     * Get Traffic Sources Overview
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function traffic_sources_overview() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/traffic-sources-overview/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $traffic_sources = $analytics->get_traffic_sources_totals();

        // Pie Chart
        $pie_chart = $analytics->pie_chart( $traffic_sources );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['direct'] = $analytics->sparkline( 'direct' );
        $sparklines['referring'] = $analytics->sparkline( 'referring' );
        $sparklines['search_engines'] = $analytics->sparkline( 'search_engines' );

        $top_traffic_sources = $analytics->get_traffic_sources();
        $top_keywords = $analytics->get_keywords();

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'traffic-sources-overview' )
            ->add_title( _('Traffic Sources Overview') )
            ->menu_item( 'analytics/traffic-sources-overview' )
            ->set( compact( 'sparklines', 'visits_plotting_array', 'traffic_sources', 'top_traffic_sources', 'top_keywords', 'pie_chart', 'date_start', 'date_end' ) );
    }

    /**
     * Get Traffic Sources
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function traffic_sources() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/traffic-sources/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = array_merge( $analytics->get_traffic_sources_totals(), $analytics->get_totals() );
        $traffic_sources = $analytics->get_traffic_sources( 0 );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'traffic-sources' )
            ->add_title( _('Traffic Sources') )
            ->menu_item( 'analytics/traffic-sources' )
            ->set( compact( 'sparklines', 'visits_plotting_array', 'total', 'traffic_sources', 'date_start', 'date_end' ) );
    }

    /**
     * Get Keywords
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function keywords() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/keywords/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = $analytics->get_totals();
        $keywords = $analytics->get_keywords( 0 );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'keywords' )
            ->add_title( _('Traffic Keywords') )
            ->menu_item( 'analytics/keywords' )
            ->set( compact( 'sparklines', 'total', 'visits_plotting_array', 'keywords', 'date_start', 'date_end' ) );
    }

    /**
     * Get Keyword
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function keyword() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        if ( !isset( $_GET['k'] ) )
            return new RedirectResponse('/analytics/keywords/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/keyword/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }
        $analytics->set_ga_filter( 'keyword==' . $_GET['k'] );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = $analytics->get_totals();

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'keyword' )
            ->add_title( _('Keyword') )
            ->menu_item( 'analytics/keywords' )
            ->set( compact( 'sparklines', 'visits_plotting_array', 'total', 'date_start', 'date_end' ) );
    }

    /**
     * Get Source
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function source() {
        if ( !$this->user->account->live )
            return new RedirectResponse('/');

        if ( !isset( $_GET['s'] ) )
            return new RedirectResponse('/analytics/traffic-sources/');

        // Get analytics
        $date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '';
        $date_end = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';

        // Setup analytics
        try {
            $analytics = new Analytics( $date_start, $date_end );
            $analytics->setup( $this->user->account );
        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/source/';
            return new RedirectResponse( '/analytics/oauth2/' );
        }
        $analytics->set_ga_filter( 'source==' . $_GET['s'] );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = array_merge( $analytics->get_traffic_sources_totals(), $analytics->get_totals() );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = array( $r_date, $r_value );
        }

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('n/j/Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('n/j/Y');

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'analytics/analytics', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'source' )
            ->add_title( _('Source') )
            ->menu_item( 'analytics/traffic-sources' )
            ->set( compact( 'sparklines', 'visits_plotting_array', 'total', 'date_start', 'date_end' ) );
    }

    /**
     * Email Marketing
     *
     * @return TemplateResponse
     */
    protected function email_marketing() {
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $this->user->account );
        $sendgrid->setup_subuser();
        $username = $this->user->account->get_settings('sendgrid-username');

        // Get last 10 messages
        $email_message = new EmailMessage();
        $emails = $email_message->get_dashboard_messages_by_account( $this->user->account->id, 10 );

        $stats = array();

        foreach ( $emails as $email ) {
            $date_send = new DateTime( $email->date_sent );
            $stats[$email->id] = $sendgrid->subuser->stats( $username, $email->id, $date_send->format('Y-m-d') );
        }

        return $this->get_template_response( 'email-marketing' )
            ->add_title( _('Email Marketing') )
            ->menu_item( 'analytics/email-marketing' )
            ->kb( 70 )
            ->set( compact( 'emails', 'stats' ) );
    }

    /**
     * Email Marketing > Email
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function email() {
        if ( !$this->user->account->email_marketing )
        	return new RedirectResponse('/analytics/');

        if ( !isset( $_GET['eid'] ) )
            return new RedirectResponse('/analytics/email-marketing/');

        $email_message = new EmailMessage();
        $email_message->get( $_GET['eid'], $this->user->account->id );

        if ( !$email_message->id )
            return new RedirectResponse('/analytics/email-marketing/');

        // Get report total
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $this->user->account );
        $sendgrid->setup_subuser();

        $date_send = new DateTime( $email_message->date_sent );
        $email = $sendgrid->subuser->stats( $this->user->account->get_settings('sendgrid-username'), $email_message->id, $date_send->format('Y-m-d') );

        // Get the bar chart
        $bar_chart = Analytics::bar_chart( $email );

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'swfobject' );

        return $this->get_template_response( 'email' )
            ->add_title( _('Email') . ' | ' . _('Email Marketing') . ' | ' . _('Email Marketing') )
            ->menu_item( 'analytics/email-marketing' )
            ->set( compact( 'email', 'email_message', 'bar_chart' ) );
    }

    /***** AJAX *****/

    /**
     * Get Graph
     *
     * @return AjaxResponse
     */
    protected function get_graph() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['metric'] ), _('Failed to get graph') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get analytics
        $date_start = ( isset( $_POST['ds'] ) ) ? $_POST['ds'] : '';
        $date_end = ( isset( $_POST['de'] ) ) ? $_POST['de'] : '';

        // Setup analytics
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );

        // Set global filter
        if ( isset( $_POST['f'] ) && !empty( $_POST['f'] ) )
            $analytics->set_ga_filter( $_POST['f'] );

        $records = $analytics->get_metric_by_date( $_POST['metric'] );

        $plotting_array = array();

        foreach ( $records as $r_date => $r_value ) {
            if ( in_array( $_POST['metric'], array( 'time_on_page', 'time_on_site') ) )
                $r_value *= 1000;

        	$plotting_array[] = array( $r_date, $r_value );
        }

        $response->add_response( 'plotting_array', $plotting_array );

        return $response;
    }
    
    public function oauth2() {
        try {
            $analytics = new Analytics();
            $analytics->setup($this->user->account);
        } catch ( Exception $e ) { /* DO NOTHING */ }

        $login_url = $analytics->ga->auth->buildAuthUrl();

        $settings = $this->user->account->get_settings( 'ga-username', 'ga-password' );
        $ga_username = security::decrypt( base64_decode( $settings['ga-username'] ), ENCRYPTION_KEY );
        $ga_password = security::decrypt( base64_decode( $settings['ga-password'] ), ENCRYPTION_KEY );

        $this->resources->javascript( 'analytics/oauth2' );
        return $this->get_template_response( 'oauth2' )
            ->set( compact( 'login_url', 'ga_username', 'ga_password' ) );
    }

    public function oauth2callback() {
        library("GoogleAnalyticsAPI");

        $ga = new GoogleAnalyticsAPI();
        $ga->auth->setClientId( Config::key( 'ga-client-id-' . DOMAIN ) );
        $ga->auth->setClientSecret( Config::key( 'ga-client-secret-' . DOMAIN ) );
        $ga->auth->setRedirectUri( Config::key( 'ga-redirect-uri-' . DOMAIN ) );

        if ( isset( $_GET['code'] ) ) {
            $auth = $ga->auth->getAccessToken( $_GET['code'] );
            if ($auth['http_code'] == 200) {
                $accessToken = $auth['access_token'];
                $refreshToken = $auth['refresh_token'];
                $tokenExpires = $auth['expires_in'];
                $tokenCreated = time();

                $this->user->account->set_settings( array(
                    'google-access-token' => $accessToken
                    , 'google-refresh-token' => $refreshToken
                    , 'google-token-expiration' => $tokenExpires
                    , 'google-token-created-at' => $tokenCreated
                ));

            }
        }

        return new HtmlResponse('<script>window.opener.location = "/analytics/"; window.close();</script>'); // RedirectResponse( $_SESSION['google-analytics-callback'] );
    }
}


