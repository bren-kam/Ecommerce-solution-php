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
        $date_start = $analytics->date_start;
        $date_end = $analytics->date_end;

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/swfobject', 'analytics/analytics' );

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
        $date_start = $analytics->date_start;
        $date_end = $analytics->date_end;

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
        $date_start = $analytics->date_start;
        $date_end = $analytics->date_end;

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'analytics/analytics' )
            ->javascript( 'analytics/jquery.flot/jquery.flot', 'analytics/jquery.flot/excanvas', 'analytics/analytics' );

        return $this->get_template_response( 'page' )
            ->add_title( _('Page') )
            ->select( 'content-overview' )
            ->set( compact( 'sparklines', 'page_views_plotting', 'total', 'date_start', 'date_end' ) );
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
}


