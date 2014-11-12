<?php
class Analytics {
    /**
     * Hold Google analytics
     * @var GoogleAnalyticsAPI
     */
    public $ga;

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

        $this->set_ga_profile(
            $account
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
        if ( !is_null( $this->ga_filter ) ) {
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;
            $ga_filter = explode( ',', $ga_filter );
            foreach ($ga_filter as &$v)
                $v = "ga:$v";
            $ga_filter_str = implode( ',', $ga_filter );
        }

        // Prepare Parameters
        foreach ( $ga_dimensions as &$v )
            $v = "ga:$v";
        $ga_dimensions_str = implode( ',', $ga_dimensions );

        foreach ( $ga_metrics as &$v )
            $v = "ga:$v";
        $ga_metrics_str = implode( ',', $ga_metrics );

        // API Call
        try {
            $response = $this->ga->query( array(
                'dimensions' => $ga_dimensions_str
                , 'metrics' => $ga_metrics_str
                , 'sort' => 'ga:date'
                , 'filters' => $ga_filter_str
            ) );
        } catch ( Exception $e ) {
            throw new ModelException( $e->getMessage(), $e->getCode(), $e );
        }

        // Declare values
        $values = array();

        // Get the values
        if ( isset( $response['rows'] ) )
            foreach( $response['rows'] as $row )
                $values[strtotime($row[0]) . '000'] = $row[1];

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

        if ( !is_null( $this->ga_filter ) ) {
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;
            $ga_filter = explode( ',', $ga_filter );
            foreach ($ga_filter as &$v)
                $v = "ga:$v";
            $ga_filter_str = implode( ',', $ga_filter );
        }

        // Get data
        $response = $this->ga->query( array(
            'metrics' => 'ga:visitBounceRate,ga:pageviews,ga:visits,ga:avgTimeOnSite,ga:avgTimeOnPage,ga:exitRate,ga:pageviewsPerVisit,ga:percentNewVisits'
            , 'filters' => $ga_filter_str
        ) );

        if ( isset( $response['rows'] ) )
            foreach ( $response['rows'] as $row ) {
                $totals = array(
                    'bounce_rate' => number_format( $row[0], 2 )
                    , 'page_views' => $row[1]
                    , 'visits' => $row[2]
                    , 'time_on_site' => dt::sec_to_time( (float)$row[3] )
                    , 'time_on_page' => dt::sec_to_time( (float)$row[4] )
                    , 'exit_rate' => number_format( $row[5], 2 )
                    , 'pages_by_visits' => number_format( $row[6], 2 )
                    , 'new_visits' => number_format( $row[7], 2 )
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

        if ( !is_null( $this->ga_filter ) ) {
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;
            $ga_filter = explode( ',', $ga_filter );
            foreach ($ga_filter as &$v)
                $v = "ga:$v";
            $ga_filter_str = implode( ',', $ga_filter );
        }

        // Get data
        $response = $this->ga->query( array(
            'dimensions' => 'ga:medium'
            , 'metrics' => 'ga:visits'
            , 'filters' => $ga_filter_str
        ) );

        // Initialize variable
        $traffic_sources_totals['total'] = 0;

        if ( isset( $response['rows'] ) )
        foreach ( $response['rows'] as $row ) {

            $traffic_sources_totals['total'] += $row[1];

            switch ( $row[0] ) {
                case 'email':
                    $traffic_sources_totals['email'] = $row[1];
                break;

                case 'organic':
                     $traffic_sources_totals['search_engines'] = $row[1];
                break;

                case 'referral':
                     $traffic_sources_totals['referring'] = $row[1];
                break;

                case '(none)':
                default:
                     $traffic_sources_totals['direct'] = $row[1];
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

        if ( !is_null( $this->ga_filter ) ) {
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;
            $ga_filter = explode( ',', $ga_filter );
            foreach ($ga_filter as &$v)
                $v = "ga:$v";
            $ga_filter_str = implode( ',', $ga_filter );
        }

        // Get data
        $response = $this->ga->query( array(
            'dimensions' => 'ga:source,ga:medium'
            , 'metrics' => 'ga:visits,ga:pageviewsPerVisit,ga:avgTimeOnSite,ga:percentNewVisits,ga:visitBounceRate'
            , 'sort' => '-ga:visits'
            , 'filters' => $ga_filter_str
        ) );

        if ( isset( $response['rows'] ) )
        foreach ( $response['rows'] as $row ) {
            $traffic_sources[] = array(
                'source' => $row[0]
                , 'medium' => $row[1]
                , 'visits' => $row[2]
                , 'pages_by_visits' => number_format( $row[3], 2 )
                , 'time_on_site' => dt::sec_to_time( $row[4] )
                , 'new_visits' => number_format( $row[5], 2 )
                , 'bounce_rate' => number_format( $row[6], 2 )
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
        if ( !is_null( $this->ga_filter ) ) {
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;
            $ga_filter = explode( ',', $ga_filter );
            foreach ($ga_filter as &$v)
                $v = "ga:$v";
            $ga_filter_str = implode( ',', $ga_filter );
        }

        // Get data
        $response = $this->ga->query( array(
            'dimensions' => 'ga:keyword'
            , 'metrics' => 'ga:visits,ga:pageviewsPerVisit,ga:avgTimeOnSite,ga:percentNewVisits,ga:visitBounceRate'
            , 'sort' => '-ga:visits'
            , 'filters' => $ga_filter_str
        ) );

        if ( isset( $response['rows'] ) )
        foreach ( $response['rows'] as $row ) {

            $keywords[] = array(
                'keyword' => $row[0]
                , 'visits' => $row[1]
                , 'pages_by_visits' => number_format( $row[2], 2 )
                , 'time_on_site' => dt::sec_to_time( $row[3] )
                , 'new_visits' => number_format( $row[4], 2 )
                , 'bounce_rate' => number_format( $row[5], 2 )
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
            $limit = 4000;

        // Declare variables
        $content_overview = array();

        if ( !is_null( $this->ga_filter ) ) {
            $ga_filter = ( empty( $ga_filter ) ) ? $this->ga_filter : $ga_filter . ',' . $this->ga_filter;
            $ga_filter = explode( ',', $ga_filter );
            foreach ($ga_filter as &$v)
                $v = "ga:$v";
            $ga_filter_str = implode( ',', $ga_filter );
        }

        // Get data
        $response = $this->ga->query( array(
            'dimensions' => 'ga:pagePath'
            , 'metrics' => 'ga:pageviews,ga:avgTimeOnPage,ga:visitBounceRate,ga:exitRate'
            , 'sort' => '-ga:pageviews'
            , 'filters' => $ga_filter_str
        ) );

        if ( isset( $response['rows'] ) )
        foreach ( $response['rows'] as $row ) {

            $content_overview[] = array(
                'page' => $row[0]
                , 'page_views' => $row[1]
                , 'time_on_page' => dt::sec_to_time( $row[2] )
                , 'bounce_rate' => number_format( $row[3], 2 )
                , 'exit_rate' => number_format( $row[4], 2 )
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
            (int) $email->opens,
            (int) $email->clicks,
            (int) $email->bounces,
            (int) $email->requests
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
                            'top' => (int) $email->requests,
                            'tip' => '#val# Emails Sent'
                        ),
                        array(
                              'top' => (int) $email->opens,
                              'tip' => '#val# Opens'
                        ),
                        array(
                              'top' => (int) $email->clicks,
                              'tip' => '#val# Clicks'
                        ),
                        array(
                              'top' => $email->bounces,
                              'tip' => '#val# Bounces'
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
                        'Bounces'
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
     * Get Ga Profile
     *
     * @param Account $account
     * @throws Exception
     * @throws GoogleAnalyticsOAuthException
     */
    protected function set_ga_profile( $account ) {
        library( 'GoogleAnalyticsAPI' );
        $ga = new GoogleAnalyticsAPI();
        $this->ga = $ga;

        $oauth_info = $account->get_settings( 'google-access-token', 'google-refresh-token', 'google-token-expiration', 'google-token-created-at', 'google-token-issued-by' );

        $issued_by = $oauth_info['google-token-issued-by'] ? $oauth_info['google-token-issued-by'] : DOMAIN;
        $ga->auth->setClientId( Config::key( 'ga-client-id-' . $issued_by ) );
        $ga->auth->setClientSecret( Config::key( 'ga-client-secret-' . $issued_by ) );
        $ga->auth->setRedirectUri( Config::key( 'ga-redirect-uri-' . $issued_by ) );

        $ga_profile_id  = $account->ga_profile_id;
        $accessToken = $oauth_info['google-access-token'];
        $refreshToken = $oauth_info['google-refresh-token'];
        $tokenExpires = $oauth_info['google-token-expiration'];
        $tokenCreated = $oauth_info['google-token-created-at'];

        $log = "Saved Token for '{$account->id}#{$ga_profile_id}': {$accessToken}|{$refreshToken}|{$tokenExpires}|{$tokenCreated}";

        if ( $accessToken ) {
            if ( ( $tokenCreated + $tokenExpires ) > time() ) {
                try {
                    $log .= "\nToken is valid, using access token {$accessToken}";
                    $ga->setAccessToken( $accessToken );
                    $ga->setAccountId( "ga:{$ga_profile_id}" );

                    $log = "Success\n$log";
                    $api_ext_log = new ApiExtLog();
                    $api_ext_log->api = 'Analytics OAuth';
                    $api_ext_log->method = 'Token';
                    $api_ext_log->raw_response = $log;
                    $api_ext_log->date_created = date('Y-m-d H:i:s');
                    $api_ext_log->create();

                    // Set Query Defaults - Date Range
                    $ga->setDefaultQueryParams( array(
                        'start-date' => $this->date_start
                        , 'end-date' => $this->date_end
                        , 'max-results' => 5000
                    ) );

                    return true;

                } catch ( GoogleAnalyticsOAuthException $e ) { /* FOLLOW TO REFRESH TOKEN */ }
            }

            $log .= "\nToken expired, using refresh token {$refreshToken}";

            // Get who issued the token (GSR, IR, etc.)
            $refresh_ga = new GoogleAnalyticsAPI();
            $refresh_ga->auth->setClientId( Config::key( 'ga-client-id-' . $issued_by ) );
            $refresh_ga->auth->setClientSecret( Config::key( 'ga-client-secret-' . $issued_by ) );
            $refresh_ga->auth->setRedirectUri( Config::key( 'ga-redirect-uri-' . $issued_by ) );

            $auth = $refresh_ga->auth->refreshAccessToken( $refreshToken );
            if ($auth['http_code'] == 200) {
                $accessToken = $auth['access_token'];
                // $refreshToken = $auth['refresh_token'];
                $tokenExpires = $auth['expires_in'];
                $tokenCreated = time();

                $account->set_settings( array(
                    'google-access-token' => $accessToken
                    // , 'google-refresh-token' => $refreshToken
                    , 'google-token-expiration' => $tokenExpires
                    , 'google-token-created-at' => $tokenCreated
                ));

                $refresh_ga->setAccessToken( $accessToken );
                $refresh_ga->setAccountId( "ga:{$ga_profile_id}" );

                // Set Query Defaults - Date Range
                $refresh_ga->setDefaultQueryParams( array(
                    'start-date' => $this->date_start
                , 'end-date' => $this->date_end
                , 'max-results' => 5000
                ) );

                $this->ga = $refresh_ga;

                return true;
            } else {
                $log .= "\nFailed to Refresh Token";
            }

        } else {
            $log .= "\nNo Access Token Found";
        }

        $api_ext_log = new ApiExtLog();
        $api_ext_log->api = 'Analytics OAuth';
        $api_ext_log->method = 'Token';
        $api_ext_log->raw_response = $log;
        $api_ext_log->date_created = date('Y-m-d H:i:s');
        $api_ext_log->create();
        throw new GoogleAnalyticsOAuthException( $log );

    }

}