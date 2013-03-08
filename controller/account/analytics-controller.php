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
        $analytics->setup( $this->user->account );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = $analytics->get_totals();
        $traffic_sources = $analytics->get_traffic_sources_totals();

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Pie Chart
        $pie_chart = $analytics->pie_chart( $traffic_sources );

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $visits_plotting = implode( ',', $visits_plotting_array );

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['page_views'] = $analytics->sparkline( 'page_views' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );

        $content_overview_pages = $analytics->get_content_overview();

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'swfobject', 'analytics/analytics' );

        return $this->get_template_response( 'index' )
            ->add_title( _('Dashboard') )
            ->select( 'dashboard' )
            ->set( compact( 'sparklines', 'traffic_sources', 'pie_chart', 'visits_plotting', 'total', 'content_overview_pages', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'page_views' );
        $total = $analytics->get_totals();
        $content_overview_pages = $analytics->get_content_overview( 0 );

        // Setup Javascript chart
        $page_views_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $page_views_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $page_views_plotting = implode( ',', $page_views_plotting_array );

        // Sparklines
        $sparklines['page_views'] = $analytics->create_sparkline( $records );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );
        $sparklines['time_on_page'] = $analytics->sparkline( 'time_on_page' );
        $sparklines['exit_rate'] = $analytics->sparkline( 'exit_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'content-overview' )
            ->add_title( _('Content Overview') )
            ->select( 'content-overview' )
            ->set( compact( 'sparklines', 'page_views_plotting', 'total', 'content_overview_pages', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );
        $analytics->set_ga_filter( 'pagePath==' . $_GET['p'] );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'page_views' );
        $total = $analytics->get_totals();

        // Setup Javascript chart
        $page_views_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $page_views_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $page_views_plotting = implode( ',', $page_views_plotting_array );

        // Sparklines
        $sparklines['page_views'] = $analytics->create_sparkline( $records );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );
        $sparklines['time_on_page'] = $analytics->sparkline( 'time_on_page' );
        $sparklines['exit_rate'] = $analytics->sparkline( 'exit_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'page' )
            ->add_title( _('Page') )
            ->select( 'content-overview' )
            ->set( compact( 'sparklines', 'page_views_plotting', 'total', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );

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
            $visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $visits_plotting = implode( ',', $visits_plotting_array );

        // Sparklines
        $sparklines['direct'] = $analytics->sparkline( 'direct' );
        $sparklines['referring'] = $analytics->sparkline( 'referring' );
        $sparklines['search_engines'] = $analytics->sparkline( 'search_engines' );

        $top_traffic_sources = $analytics->get_traffic_sources();
        $top_keywords = $analytics->get_keywords();

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'swfobject', 'analytics/analytics' );

        return $this->get_template_response( 'traffic-sources-overview' )
            ->add_title( _('Traffic Sources Overview') )
            ->select( 'traffic-sources' )
            ->set( compact( 'sparklines', 'visits_plotting', 'traffic_sources', 'top_traffic_sources', 'top_keywords', 'pie_chart', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = array_merge( $analytics->get_traffic_sources_totals(), $analytics->get_totals() );
        $traffic_sources = $analytics->get_traffic_sources( 0 );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $visits_plotting = implode( ',', $visits_plotting_array );

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'traffic-sources' )
            ->add_title( _('Traffic Sources') )
            ->select( 'traffic-sources', 'sources' )
            ->set( compact( 'sparklines', 'visits_plotting', 'total', 'traffic_sources', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = $analytics->get_totals();
        $keywords = $analytics->get_keywords( 0 );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $visits_plotting = implode( ',', $visits_plotting_array );

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'keywords' )
            ->add_title( _('Traffic Keywords') )
            ->select( 'traffic-sources', 'keywords' )
            ->set( compact( 'sparklines', 'total', 'visits_plotting', 'keywords', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );
        $analytics->set_ga_filter( 'keyword==' . $_GET['k'] );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = $analytics->get_totals();

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $visits_plotting = implode( ',', $visits_plotting_array );

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'keyword' )
            ->add_title( _('Keyword') )
            ->select( 'traffic-sources', 'keywords' )
            ->set( compact( 'sparklines', 'visits_plotting', 'total', 'date_start', 'date_end' ) );
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
        $analytics = new Analytics( $date_start, $date_end );
        $analytics->setup( $this->user->account );
        $analytics->set_ga_filter( 'source==' . $_GET['s'] );

        // Get all the data
        $records = $analytics->get_metric_by_date( 'visits' );
        $total = array_merge( $analytics->get_traffic_sources_totals(), $analytics->get_totals() );

        // Setup Javascript chart
        $visits_plotting_array = array();

        // Visits plotting
        if ( is_array( $records ) )
        foreach ( $records as $r_date => $r_value ) {
            $visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
        }

        $visits_plotting = implode( ',', $visits_plotting_array );

        // Sparklines
        $sparklines['visits'] = $analytics->create_sparkline( $records );
        $sparklines['pages_by_visits'] = $analytics->sparkline( 'pages_by_visits' );
        $sparklines['time_on_site'] = $analytics->sparkline( 'time_on_site' );
        $sparklines['new_visits'] = $analytics->sparkline( 'new_visits' );
        $sparklines['bounce_rate'] = $analytics->sparkline( 'bounce_rate' );

        // Get the dates
        $date_start = new DateTime( $analytics->date_start );
        $date_start = $date_start->format('M d, Y');
        $date_end = new DateTime( $analytics->date_end );
        $date_end = $date_end->format('M d, Y');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'source' )
            ->add_title( _('Source') )
            ->select( 'traffic-sources', 'sources' )
            ->set( compact( 'sparklines', 'visits_plotting', 'total', 'date_start', 'date_end' ) );
    }

    /**
     * Email Marketing
     *
     * @return TemplateResponse
     */
    protected function email_marketing() {
        $analytics_email = new AnalyticsEmail();
        $analytics_email->update_by_account( $this->user->account->id );

        return $this->get_template_response( 'email-marketing' )
            ->add_title( _('Email Marketing') )
            ->select( 'email-marketing' );
    }

    /**
     * Email Marketing > Email
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function email() {
        if ( !$this->user->account->email_marketing )
        	return new RedirectResponse('/analytics/');

        if ( !isset( $_GET['mcid'] ) )
            return new RedirectResponse('/analytics/email-marketing/');

        $email = new AnalyticsEmail();
        $email->get_complete( $_GET['mcid'], $this->user->account->id );

        // Get the bar chart
        $bar_chart = Analytics::bar_chart( $email );

        $this->resources
            ->css( 'analytics/analytics' )
            ->javascript( 'swfobject', 'analytics/email' );

        return $this->get_template_response( 'email' )
            ->add_title( _('Email') . ' | ' . _('Email Marketing') . ' | ' . _('Email Marketing') )
            ->select( 'email-marketing' )
            ->set( compact( 'email', 'bar_chart' ) );
    }

    /**
     * Email Marketing
     *
     * @return CustomResponse
     */
    protected function email_click_overlay() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/analytics/');

        if ( !isset( $_GET['mcid'] ) )
            return new RedirectResponse('/analytics/email-marketing/');

        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );
        $message = $mc->campaignContent( $_GET['mcid'] );

        if ( $message ) {
            $html = $message['html'];
        } else {
            $html = '<p>' . _('There is no Click Overlay available for this email') . '</p>';
        }

        return new HtmlResponse( $html );
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

    /**
     * List emails
     *
     * @return DataTableResponse
     */
    protected function list_emails() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $analytics_email = new AnalyticsEmail();

        // Set Order by
        $dt->order_by( 'em.`subject`', 'ae.`emails_sent`', 'ae.`open`', 'ae.`clicks`', 'em.`date_sent`' );
        $dt->add_where( ' AND em.`website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( 'em`subject`' => false ) );

        // Get items
        $emails = $analytics_email->list_all( $dt->get_variables() );
        $dt->set_row_count( $analytics_email->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        /**
         * @var AnalyticsEmail $email
         */
        if ( is_array( $emails ) )
        foreach ( $emails as $email ) {
            $date = new DateTime( $email->date_sent );

            $data[] = array(
                '<a href="' . url::add_query_arg( 'mcid', $email->mc_campaign_id, '/analytics/email/' ) . '" title="' . $email->subject . '">' . $email->subject . '</a>'
                , $email->emails_sent
                , $email->opens
                , $email->clicks
                , $date->format( 'F jS, Y g:i a' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}


