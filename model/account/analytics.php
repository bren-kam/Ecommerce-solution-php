<?php
class Analytics {
    /**
     * Hold Google analytics
     * @var GAPI
     */
    public $ga;

    /**
     * Hold Google Analytics profile ID
     * @var int
     */
    public $ga_profile_id;

    /**
     * Hold a filter to be used for every other part
     * @var string
     */
    public $ga_filter;

    /**
     * Setup the account initial data
     *
     * @param string $date_start
     * @param string $date_end
	 */
    public function __construct( $date_start = '', $date_end = '') {
        if ( empty( $date_start ) ) {
            $date_start = new DateTime();
            $date_start->sub( new DateInterval('P1M') ); // 1 Month ago
        } else {
            $date_start = new DateTime( $date_start );
        }

        if ( empty( $date_end ) ) {
            $date_end = new DateTime();
            $date_end->sub( new DateInterval('P2D') ); // 1 day ago
        } else {
            $date_end = new DateTime( $date_end );
        }

        $this->date_start = $date_start->format('Y-m-d');
        $this->date_end = $date_end->format('Y-m-d');
    }

    /**
     * Setup Analytics
     *
     * @param Account $account
     * @return Analytics
     */
    public function setup( Account $account ) {
        // Setup the profile
        $settings = $account->get_settings( 'ga-username', 'ga-password' );

        $this->set_ga_profile(
            $account->ga_profile_id
            , security::decrypt( base64_decode( $settings['ga-username'] ), ENCRYPTION_KEY )
            , security::decrypt( base64_decode( $settings['ga-password'] ), ENCRYPTION_KEY )
        );
    }

    /**
     * Set's the GA Filter for use throughout everything else
     *
     * @param string $ga_filter
     * @return void
     */
    public function set_ga_filter( $ga_filter ) {
        $this->ga_filter = $ga_filter;
    }

    /***** ANALYTICS FUNCTIONS *****/

    /**
     * Gets the amount of (metric) by date
     *
     * @param string $metric a dimension to grab data about ( visits, page views )
     * @return array
     */
    public function get_metric_by_date( $metric ) {
        // Determine what it's supposed to be
        list( $ga_dimension, $ga_metric, $ga_filter ) = $this->metric_sql_calculation( $metric );

        $ga_dimensions = ( is_null( $ga_dimension ) ) ? array('date') : array( 'date', $ga_dimension );
        $ga_metrics = ( is_null( $ga_metric ) ) ? NULL : array( $ga_metric );

        // Handle the GA Filter
        if ( !is_null( $this->ga_filter ) )
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;

        // API Call
        try {
            $this->ga->requestReportData( $this->ga_profile_id, $ga_dimensions, $ga_metrics, array('date'), $ga_filter, $this->date_start, $this->date_end, 1, 10000 );
        } catch ( Exception $e ) {
            throw new ModelException( $e->getMessage(), $e->getCode(), $e );
        }

        // Process results
        $results = $this->ga->getResults();

        // Declare values
        $values = array();

        // Get the values
        if ( is_array( $results ) )
        foreach( $results as $result ) {
            $metrics = $result->getMetrics();
            $dimensions = $result->getDimensions();
            $values[strtotime($dimensions['date']) . '000'] = $metrics[$ga_metric];
        }

        return $values;
    }

    /**
     * Gets the total amounts for a date range
     *
     * @return array
     */
    public function get_totals() {
        // Declare variables
        $totals = array();

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, NULL, array( 'visitBounceRate', 'pageviews', 'visits', 'avgTimeOnSite', 'avgTimeOnPage', 'exitRate', 'pageviewsPerVisit', 'percentNewVisits' ), NULL, $this->ga_filter, $this->date_start, $this->date_end, 1, 10000 );

        // See if there were any results
        $results = $this->ga->getResults();

        if ( is_array( $results ) )
        foreach ( $this->ga->getResults() as $result ) {
            $metrics = $result->getMetrics();

            $totals = array(
                'bounce_rate' => number_format( $metrics['visitBounceRate'], 2 )
                , 'page_views' => $metrics['pageviews']
                , 'visits' => $metrics['visits']
                , 'time_on_site' => dt::sec_to_time( $metrics['avgTimeOnSite'] )
                , 'time_on_page' => dt::sec_to_time( $metrics['avgTimeOnPage'] )
                , 'exit_rate' => number_format( $metrics['exitRate'], 2 )
                , 'pages_by_visits' => number_format( $metrics['pageviewsPerVisit'], 2 )
                , 'new_visits' => number_format( $metrics['percentNewVisits'], 2 )
            );
        }

        return $totals;
    }

    /**
     * Gets the totals for traffic sources for a date range
     *
     * @return array
     */
    public function get_traffic_sources_totals() {
        // Declare variables
        $traffic_sources_totals = array();

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, array('medium'), array( 'visits' ), NULL, $this->ga_filter, $this->date_start, $this->date_end, 1, 10000 );

        // See if there were any results
        $results = $this->ga->getResults();

        // Initialize variable
        $traffic_sources_totals['total'] = 0;

        if ( is_array( $results ) )
        foreach ( $this->ga->getResults() as $result ) {
            $metrics = $result->getMetrics();
            $dimensions = $result->getDimensions();

            $traffic_sources_totals['total'] += $metrics['visits'];

            switch ( $dimensions['medium'] ) {
                case 'email':
                    $traffic_sources_totals['email'] = $metrics['visits'];
                break;

                case 'organic':
                     $traffic_sources_totals['search_engines'] = $metrics['visits'];
                break;

                case 'referral':
                     $traffic_sources_totals['referring'] = $metrics['visits'];
                break;

                case '(none)':
                default:
                     $traffic_sources_totals['direct'] = $metrics['visits'];
                break;
            }
        }

        return $traffic_sources_totals;
    }

    /**
     * Gets the rows for all traffic sources
     *
     * @param int $limit [optional]
     * @return array
     */
    public function get_traffic_sources( $limit = 5 ) {
        // Make sure we can get any number we want
        if ( 0 == $limit )
            $limit = 10000;

        // Declare variables
        $traffic_sources = array();

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, array( 'source', 'medium' ), array( 'visits', 'pageviewsPerVisit', 'avgTimeOnSite', 'percentNewVisits', 'visitBounceRate' ), array( '-visits' ), $this->ga_filter, $this->date_start, $this->date_end, 1, $limit );

        // See if there were any results
        $results = $this->ga->getResults();

        if ( is_array( $results ) )
        foreach ( $this->ga->getResults() as $result ) {
            $metrics = $result->getMetrics();
            $dimensions = $result->getDimensions();

            $traffic_sources[] = array(
                'source' => $dimensions['source']
                , 'medium' => $dimensions['medium']
                , 'visits' => $metrics['visits']
                , 'pages_by_visits' => number_format( $metrics['pageviewsPerVisit'], 2 )
                , 'time_on_site' => dt::sec_to_time( $metrics['avgTimeOnSite'] )
                , 'new_visits' => number_format( $metrics['percentNewVisits'], 2 )
                , 'bounce_rate' => number_format( $metrics['visitBounceRate'], 2 )
            );
        }

        return $traffic_sources;
    }

    /**
     * Gets the rows for all keywords
     *
     * @param int $limit [optional]
     * @return array
     */
    public function get_keywords( $limit = 5 ) {
        // Make sure we can get any number we want
        if ( 0 == $limit )
            $limit = 10000;

         // Declare variables
        $keywords = array();

        // Set the GA Filter
        $ga_filter = 'keyword!=(not set)';

        // Add on global filter
        if ( !is_null( $this->ga_filter ) )
            $ga_filter .= ',' . $this->ga_filter;

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, array( 'keyword' ), array( 'visits', 'pageviewsPerVisit', 'avgTimeOnSite', 'percentNewVisits', 'visitBounceRate' ), array( '-visits' ), $ga_filter, $this->date_start, $this->date_end, 1, $limit );

        // See if there were any results
        $results = $this->ga->getResults();

        if ( is_array( $results ) )
        foreach ( $this->ga->getResults() as $result ) {
            $metrics = $result->getMetrics();
            $dimensions = $result->getDimensions();

            $keywords[] = array(
                'keyword' => $dimensions['keyword']
                , 'visits' => $metrics['visits']
                , 'pages_by_visits' => number_format( $metrics['pageviewsPerVisit'], 2 )
                , 'time_on_site' => dt::sec_to_time( $metrics['avgTimeOnSite'] )
                , 'new_visits' => number_format( $metrics['percentNewVisits'], 2 )
                , 'bounce_rate' => number_format( $metrics['visitBounceRate'], 2 )
            );
        }

        return $keywords;
    }

    /**
     * Gets the totals for Content Overview for a date range
     *
     * @param int $limit
     * @return array
     */
    public function get_content_overview( $limit = 5 ) {
        // Make sure we can get any number we want
        if ( 0 == $limit )
            $limit = 10000;

        // Declare variables
        $content_overview = array();

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, array('pagePath'), array( 'pageviews', 'avgTimeOnPage', 'visitBounceRate', 'exitRate' ), array( '-pageviews' ), $this->ga_filter, $this->date_start, $this->date_end, 1, $limit );

        // See if there were any results
        $results = $this->ga->getResults();

        if ( is_array( $results ) )
        foreach ( $this->ga->getResults() as $result ) {
            $metrics = $result->getMetrics();
            $dimensions = $result->getDimensions();

            $content_overview[] = array(
                'page' => $dimensions['pagePath']
                , 'page_views' => $metrics['pageviews']
                , 'time_on_page' => dt::sec_to_time( $metrics['avgTimeOnPage'] )
                , 'bounce_rate' => number_format( $metrics['visitBounceRate'], 2 )
                , 'exit_rate' => number_format( $metrics['exitRate'], 2 )
            );
        }

        return $content_overview;
    }

    /***** CALCULATIVE FUNCTIONS *****/

    /**
     * Gets the array for a sparkline
     *
     * @param string $metric
     * @return AnalyticsSparkline
     */
    public function sparkline( $metric ) {
        return $this->create_sparkline( $this->get_metric_by_date( $metric ) );
    }

    /**
     * Create Sparkline
     *
     * @param array $sparkline_array
     * @param int $width (optional)
     * @param int $height (optional)
     * @return string
     */
    public function create_sparkline( array $sparkline_array, $width = 150, $height = 36 ) {
        // Pad the array
        $sparkline_array = array_pad( $sparkline_array, -30, 0 );

        // Get Sparkline Max
        $sparkline_max = max( $sparkline_array );

        // Tricky tricky
        0 == $sparkline_max && $sparkline_max = 1;

        // 4095 is the top of sparklines (like 100%)
        $factor = 4095 / $sparkline_max;

        $sparkline = array();

        // Show the values
        foreach ( $sparkline_array as $sa ) {
            $sparkline[] = round( $sa * $factor );
        }

        return "http://chart.apis.google.com/chart?cht=ls&amp;chs={$width}x{$height}&amp;chf=bg,s,FFFFFF00&amp;chm=B,f8e6b2,0,0.0,0.0&amp;chco=edc240&amp;chd=e:" . ar::extended_encoding( $sparkline );
    }

    /**
     * Pie Chart data
     *
     * @param array $traffic_sources
     * @return string (json encoded)
     */
    public function pie_chart( $traffic_sources ) {
        // Set the colors
        $colors = array(
            '#008DC9',
            '#00B518',
            '#FF530C'
        );

        // Set the values
        $values = array(
            (int) $traffic_sources['direct'],
            (int) $traffic_sources['referring'],
            (int) $traffic_sources['search_engines']
        );

        // If there is more
        if ( $traffic_sources['email'] > 0 ) {
            $colors[] = '#EDE500';

            $values[] = (int) $traffic_sources['email'];
        }

        // Create the pie chart
        $pie_chart = array(
            'elements' => array(
                array(
                    'type' => 'pie',
                    'alpha' => 1,
                    'start-angle' => 0,
                    'no-labels' => true,
                    'gradient-fill' => true,
                    'animate' => array(
                        array( 'type' => 'fade' )
                    ),
                    'colours' => $colors,
                    'values' => $values
                )
            ),
            'x_axis' => NULL,
            'bg_colour' => -1
        );

        return json_encode( $pie_chart );
    }

    /**
     * Bar Chart data
     *
     * @param object $email
     * @return string (json encoded)
     */
    public static function bar_chart( $email ) {
        $max = max( array(
            (int) $email->send_amt,
            (int) $email->uniqueopens,
            (int) $email->uniquelinkclicks,
            (int) $email->forwards,
            (int) $email->totalbounces,
            (int) $email->unsubscribes
        ) );

        // Create the bar chart
        $bar_chart = array(
            'elements' => array(
                array(
                    'type' => 'bar_glass',
                    'colour' => '#FFA900',
                    'on-show' => array(
                        'type' => 'grow-up',
                        'cascade' => 1,
                        'delay' => 0.5
                    ),
                    'values' => array(
                        array(
                            'top' => (int) $email->send_amt,
                            'tip' => '#val# Emails Sent'
                        ),
                        array(
                              'top' => (int) $email->uniqueopens,
                              'tip' => '#val# Opens'
                        ),
                        array(
                              'top' => (int) $email->uniquelinkclicks,
                              'tip' => '#val# Clicks'
                        ),
                        array(
                              'top' => (int) $email->forwards,
                              'tip' => '#val# Forwards'
                        ),
                        array(
                              'top' => $email->totalbounces,
                              'tip' => '#val# Bounces'
                        ),
                        array(
                              'top' => (int) $email->unsubscribes,
                              'tip' => '#val# Unsubscribes'
                        )
                    ),
                    'tip' => '#val#'
                )
            ),
            'x_axis' => array(
                'labels' => array(
                    'labels' => array(
                        'Emails Sent',
                        'Opens',
                        'Clicks',
                        'Forwards',
                        'Bounces',
                        'Unsubscribes'
                    ),
                    'colour' => '#545454'
                ),
                'colour' => '#545454',
                'grid-colour' => '#D9D9D9'
            ),
            'y_axis' => array(
                'min' => 0,
                'max' => $max,
                'steps' => ceil( $max / 6 ),
                'colour' => '#545454',
                'grid-colour' => '#D9D9D9'
            ),
            'bg_colour' => '#FFFFFF'
        );

        return json_encode( $bar_chart );
    }

    /***** PROTECTED FUNCTIONS ****/

    /**
     * Gives the correct calculation for a metric
     *
     * @param string $metric
     * @return array
     */
    protected function metric_sql_calculation( $metric ) {
        // Initialize variables
        $ga_dimension = $ga_filter = NULL;

        // Determine what it's supposed to be
        switch ( $metric ) {
            case 'bounce_rate':
                $ga_metric = 'entranceBounceRate';
            break;

            case 'direct':
                $ga_metric = 'visits';
                $ga_dimension = 'medium';
                $ga_filter = 'medium==(none)';
            break;

            case 'exit_rate':
                $ga_metric = 'exitRate';
            break;

            case 'new_visits':
                $ga_metric = 'percentNewVisits';
            break;

            case 'pages_by_visits':
                $ga_metric = 'pageviewsPerVisit';
            break;

            case 'page_views':
                $ga_metric = 'pageviews';
            break;

            case 'referring':
                $ga_metric = 'visits';
                $ga_dimension = 'medium';
                $ga_filter = 'medium==referral';
            break;

            case 'search_engines':
                $ga_metric = 'visits';
                $ga_dimension = 'medium';
                $ga_filter = 'medium==organic';
            break;

            case 'time_on_site':
                $ga_metric = 'avgTimeOnSite';
            break;

            case 'time_on_page':
                $ga_metric = 'avgTimeOnPage';
            break;

            default:
                $ga_metric = $metric;
            break;
        }

        return array( $ga_dimension, $ga_metric, $ga_filter );
    }

    /**
     * Set GA Profile
     *
     * @throws ModelException
     *
     * @param int $ga_profile_id
     * @param string $ga_username
     * @param string $ga_password
     */
    protected function set_ga_profile( $ga_profile_id, $ga_username, $ga_password ) {
        library( 'GAPI' );

        // Determine if their username and password are empty (use our account) or not (use their account)
        if ( !empty( $ga_username ) && !empty( $ga_password ) ) {
            try {
                $this->ga = new GAPI( $ga_username, $ga_password );
            } catch ( Exception $e ) {
                throw new ModelException( $e->getMessage(), $e->getCode(), $e );
            }
        } else {
            $this->ga = new GAPI( Config::key('ga-username'), Config::key('ga-password') );
        }

        $this->ga_profile_id = (int) $ga_profile_id;
    }

    /***** EMAIL MARKETING *****/
}