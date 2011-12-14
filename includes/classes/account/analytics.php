<?php
/**
 * Handles all the Analytics
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Analytics extends Base_Class {
	/**
	 * The start date for analytics
	 * @var string
	 */
	private $date_start;
	
	/**
	 * The end date for analytics
	 * @var string
	 */
	private $date_end;

	/**
	 * The Google Analytics Profile ID
	 * @var string
	 */
	private $ga_profile_id;

	/**
	 * The Facebook Token
	 * @var string
	 */
	private $fb_token;
	
	/**
	 * The Facebook Page ID
	 * @var int
	 */
	private $fb_page_id;

	/**
	 * Any extra where data
	 * @fix this should be public -- The following pages modify this data that is directly going into the database:
	 * 		analytics/keyword.php
	 *		analytics/page.php
	 *		analytics/source.php
	 *		analytics/traffic-keywords.php
	 * @var string
	 */
	public $extra_where = '';

	/**
	 * Construct initializes data
     *
     * @param int $ga_profile_id
	 */
	public function __construct( $ga_profile_id = 0 ) {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
        // Call Google Analytics API
        library( 'GAPI' );

        $this->ga = new GAPI( 'web@imagineretailer.com', 'imagine1010' );
		$this->ga_profile_id = (int) $ga_profile_id;
		$this->date_start = dt::date( 'Y-m-d', time() - 2678400 ); // 30 days ago
		$this->date_end = dt::date( 'Y-m-d', time() - 86400 ); // Yesterday

	}
	
	/***** DASHBOARD *****/
	
	/**
	 * Gets the amount of (metric) by date
	 *
	 * @param string $metric a dimension to grab data about ( visits, page views )
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @return array
	 */
	public function get_metric_by_date( $metric, $date_start = '', $date_end = '' ) {
		// Make sure they have google analytics
		if ( empty( $this->ga_profile_id ) )
			return false;
		
		// Get dates
		list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );

		// Determine what it's supposed to be
		list( $ga_dimension, $ga_metric, $ga_filter ) = $this->metric_sql_calculation( $metric );

        $ga_dimensions = ( is_null( $ga_dimension ) ) ? NULL : array( $ga_dimension );
        $ga_metrics = ( is_null( $ga_metric ) ) ? NULL : array( $ga_metric );

        // API Call
        $this->ga->requestReportData( $this->ga_profile_id, array('date'), $ga_metrics, array('date'), $ga_filter, $date_start, $date_end, 1, 10000 );

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
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @return array
	 */
	public function get_totals( $date_start = '', $date_end = '' ) {
		// Make sure that we have a google analytics profile to work with
		if ( empty( $this->ga_profile_id ) )
			return false;

        // Declare variables
        $totals = array();

        list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, NULL, array( 'visitBounceRate', 'pageviews', 'visits', 'avgTimeOnSite', 'avgTimeOnPage', 'exitRate', 'pageviewsPerVisit', 'percentNewVisits' ), NULL, NULL, $date_start, $date_end, 1, 10000 );

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
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @return array
	 */
	public function get_traffic_sources_totals( $date_start = '', $date_end = '' ) {
		// Make sure that we have a google analytics profile to work with
		if ( empty( $this->ga_profile_id ) )
			return false;

        // Declare variables
        $traffic_sources_totals = array();

        list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, array('medium'), array( 'visits' ), NULL, NULL, $date_start, $date_end, 1, 10000 );

        // See if there were any results
        $results = $this->ga->getResults();

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
	 * Gets the totals for Content Overview for a date range
	 *
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @param int $limit
	 * @return array
	 */
	public function get_content_overview( $date_start = '', $date_end = '', $limit = 5 ) {
		// Make sure that we have a google analytics profile to work with
		if ( empty( $this->ga_profile_id ) )
			return false;

        // Declare variables
        $traffic_sources_totals = array();

        list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );

        // Get data
        $this->ga->requestReportData( $this->ga_profile_id, array('pagePath'), array( 'visits' ), NULL, NULL, $date_start, $date_end, 1, 10000 );

		// Limit
		$limit = ( 0 == $limit ) ? '' : ' LIMIT ' . (int) $limit;
		
		// Get dates
		list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );
		
		$content_overview = $this->db->get_results( "SELECT `page`, SUM( `page_views` ) AS page_views, SEC_TO_TIME( SUM( `time_on_page` ) / ( SUM( `page_views` ) - SUM( `exits` ) ) ) AS time_on_page, ROUND( SUM( `bounces` ) / SUM( `entrances` ) * 100, 2 ) AS bounce_rate, ROUND( SUM( `exits` ) / SUM( `page_views` ) * 100, 2 ) AS exit_rate FROM `analytics_data`  WHERE `date` >= '" . $this->db->escape( $date_start ) . "' AND `date` <= '" . $this->db->escape( $date_end ) . "' AND `ga_profile_id` = " . $this->ga_profile_id . " GROUP BY `page` ORDER BY `page_views` DESC $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get content overview.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $content_overview;
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
	
	/***** TRAFFIC SOURCES OVERVIEW *****/
	/**
	 * Gets the rows for all traffic sources
	 *
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @param int $limit
	 * @return array
	 */
	public function get_traffic_sources( $date_start = '', $date_end = '', $limit = 5 ) {
		// Make sure that we have a google analytics profile to work with
		if ( empty( $this->ga_profile_id ) )
			return false;
		
		// Limit
		$limit = ( 0 == $limit ) ? '' : ' LIMIT ' . (int) $limit;
		
		// Get dates
		list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );
		
		$traffic_sources = $this->db->get_results( "SELECT `source`, `medium`, SUM( `visits` ) AS visits, ROUND( SUM( `page_views` ) / SUM( `visits` ), 2 ) AS pages_by_visits, SEC_TO_TIME( SUM( `time_on_page` ) / SUM( `visits` ) ) AS time_on_site, ROUND( SUM( `new_visits` ) / SUM( `visits` ) * 100, 2 ) AS new_visits, ROUND( SUM( `bounces` ) / SUM( `entrances` ) * 100, 2 ) AS bounce_rate FROM `analytics_data`  WHERE `date` >= '" . $this->db->escape( $date_start ) . "' AND `date` <= '" . $this->db->escape( $date_end ) . "' AND `ga_profile_id` = " . $this->ga_profile_id . " GROUP BY `source` ORDER BY `visits` DESC $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get traffic sources.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $traffic_sources;
	}
	
	/**
	 * Gets the rows for all keywords
	 *
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @param int $limit
	 * @return array
	 */
	public function get_keywords( $date_start = '', $date_end = '', $limit = 5 ) {
		// Make sure that we have a google analytics profile to work with
		if ( empty( $this->ga_profile_id ) )
			return false;
		
		// Limit
		$limit = ( 0 == $limit ) ? '' : ' LIMIT ' . (int) $limit;
		
		// Get dates
		list( $date_start, $date_end ) = $this->dates( $date_start, $date_end );
		
		$keywords = $this->db->get_results( "SELECT `keyword`, SUM( `visits` ) AS visits, ROUND( SUM( `page_views` ) / SUM( `visits` ), 2 ) AS pages_by_visits, SEC_TO_TIME( SUM( `time_on_page` ) / SUM( `visits` ) ) AS time_on_site, ROUND( SUM( `new_visits` ) / SUM( `visits` ) * 100, 2 ) AS new_visits, ROUND( SUM( `bounces` ) / SUM( `entrances` ) * 100, 2 ) AS bounce_rate FROM `analytics_data`  WHERE `keyword` <> '(not set)' AND `date` >= '" . $this->db->escape( $date_start ) . "' AND `date` <= '" . $this->db->escape( $date_end ) . "' AND `ga_profile_id` = " . $this->ga_profile_id . " GROUP BY `keyword` ORDER BY `visits` DESC $limit", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get keywords.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $keywords;
	}
	
	/***** SPARKLINES *****/
	
	/**
	 * Gets the array for a sparkline
	 *
	 * @param string $metric
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @return array
	 */
	public function sparkline( $metric, $date_start = '', $date_end = '' ) {
		// Make sure that we have a google analytics profile to work with
		if ( empty( $this->ga_profile_id ) )
			return false;
		
		return $this->create_sparkline( $this->get_metric_by_date( $metric, $date_start, $date_end ) );
	}
	
	/**
	 * Creates a sparkline from an array of data
	 *
	 * @param array $sparkline_array the array of values to become a sparkline
	 * @param int $width (optional) the width of the sparkline
	 * @param int $height (optional) the height of the sparkline
	 * @return string|bool an image source for a sparkline
	 */
	public function create_sparkline( $sparkline_array, $width = 150, $height = 36 ) {
		// Make sure there are values
		if ( !is_array( $sparkline_array ) )
			return false;
		
		// Pad the array
		$sparkline_array = array_pad( $sparkline_array, -30, 0 );
		
		// Get Sparkline Max
		$sparkline_max = max( $sparkline_array );
		
		// Tricky tricky
		0 == $sparkline_max && $sparkline_max = 1;
		
		// 4095 is the top of sparklines (like 100%)
		$factor = 4095 / $sparkline_max;
		
		// Show the values
		foreach ( $sparkline_array as $sa ) {
			$sparkline[] = round( $sa * $factor );
		}
		
		return "http://chart.apis.google.com/chart?cht=ls&amp;chs={$width}x{$height}&amp;chf=bg,s,FFFFFF00&amp;chm=B,f8e6b2,0,0.0,0.0&amp;chco=edc240&amp;chd=e:" . ar::extended_encoding( $sparkline );
	}
	
	/***** EMAIL MARKETING *****/
	
	/**
	 * Gets all the emails and their details
	 *
	 * @return array
	 */
	public function get_emails() {
		global $user;
		
		// Find out what emails don't have any statistics
		$emails_without_statistics = $this->db->get_col( "SELECT `mc_campaign_id` FROM `email_messages` WHERE `website_id` = " . $user['website']['website_id'] . " AND `status` = 2 AND `mc_campaign_id` NOT IN ( SELECT a.`mc_campaign_id` FROM `analytics_emails` AS a LEFT JOIN `email_messages` AS b ON ( a.`mc_campaign_id` = b.`mc_campaign_id` ) WHERE b.`website_id` = " . $user['website']['website_id'] . " AND `status` = 2 )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get emails without statistics.', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there are any statistics to get
		if ( is_array( $emails_without_statistics ) && count( $emails_without_statistics ) > 0 ) {
			$mc = $this->mailchimp_instance();
			
			$values = '';
			
			// Loop through each one
			foreach ( $emails_without_statistics as $mc_campaign_id ) {
				// Get the statistics
				$s = $mc->campaignStats( $mc_campaign_id );
				
				// Handle errors
				if ( $mc->errorCode ) {
					$this->err( "MailChimp: Unable to get Campaign Statistics\n\nCampaign ID: " . $e['mc_campaign_id'] . "\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
					return false;
				} 
				
				if ( !empty( $values ) )
					$values .= ",";
				
				$values .= sprintf( "('%s', %d, %d, %d, %d, %d, %d, %d, %d, %d, '%s', %d, %d, '%s', %d, %d )", $e['mc_campaign_id'], $s['syntax_errors'], $s['hard_bounces'], $s['soft_bounces'], $s['unsubscribes'], $s['abuse_reports'], $s['forwards'], $s['forwards_opens'], $s['opens'], $s['unique_opens'], $s['last_open'], $s['clicks'], $s['unique_clicks'], $s['last_click'], $s['users_who_clicked'], $s['emails_sent'] );
			}
			
			// Insert them into our database
			$this->db->query( "INSERT INTO `analytics_emails` ( `mc_campaign_id`, `syntax_errors`, `hard_bounces`, `soft_bounces`, `unsubscribes`, `abuse_reports`, `forwards`, `forwards_opens`, `opens`, `unique_opens`, `last_open`, `clicks`, `unique_clicks`, `last_click`, `users_who_clicked`, `emails_sent` ) VALUES $values" );
			
			// Handle SQL errors
			if ( $this->db->errno() )
				$this->err( 'Failed to add Analytics Email Statistics',  __LINE__, __METHOD__ );
		}
		
		$emails = $this->db->get_results( 'SELECT b.`email_message_id`, a.`mc_campaign_id`, b.`subject`, a.`opens`, a.`clicks`, a.`emails_sent`, UNIX_TIMESTAMP( b.`date_sent` ) AS date_sent, UNIX_TIMESTAMP( a.`last_updated` ) AS last_updated FROM `analytics_emails` AS a INNER JOIN `email_messages` AS b ON ( a.`mc_campaign_id` = b.`mc_campaign_id` ) WHERE b.`status` = 2 AND b.`website_id` = ' . $user['website']['website_id'], ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get emails.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $emails;
	}
	
	/**
	 * Gets an individual email
	 *
     * @param string $mc_campaign_id
	 * @return array
	 */
	public function get_email( $mc_campaign_id ) {
		global $user;
		
		$mc = $this->mailchimp_instance();
		
		// Get the statistics
		$s = $mc->campaignStats( $mc_campaign_id );
		
		// Handle errors
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to get Campaign Statistics\n\nCampaign ID: $mc_campaign_id\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		// Update the analytics
		$this->update_analytics( $mc_campaign_id, $s );
		
		// Do not $this->db->prepare this statement. It actually causes a PHP error:
		// "PHP Fatal Flex Scanner Internal Error"
		$email = $this->db->get_row( 'SELECT a.*, b.`mc_campaign_id`, b.`subject`, UNIX_TIMESTAMP( b.`date_sent` ) AS date_sent, UNIX_TIMESTAMP( a.`last_updated` ) AS last_updated FROM `analytics_emails` AS a INNER JOIN `email_messages` AS b ON ( a.`mc_campaign_id` = b.`mc_campaign_id` ) WHERE a.`mc_campaign_id` = "' . $this->db->escape( $mc_campaign_id ) . '" AND b.`website_id` = ' .  $user['website']['website_id'], ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get email.', __LINE__, __METHOD__ );
			return false;
		}
		
		$email['advice'] = $mc->campaignAdvice( $mc_campaign_id );
		
		// Handle errors
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to get Campaign Advice\n\nCampaign ID: $mc_campaign_id\nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		// Get clicks
		$email['click_overlay'] = $mc->campaignClickStats( $mc_campaign_id );
		
		// Handle errors
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to get Campaign Click Stats\n\nCampaign ID: $mc_campaign_id \nCode: " . $mc->errorCode . "\nError Message: " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		return $email;
	}
	
	/**
	 * Update a Campaign's analytics
	 *
	 * @param string $mc_campaign_id
	 * @param array $s
	 * @return bool
	 */
	private function update_analytics( $mc_campaign_id, $s ) {
		$this->db->prepare( 'INSERT INTO `analytics_emails` ( `mc_campaign_id`, `syntax_errors`, `hard_bounces`, `soft_bounces`, `unsubscribes`, `abuse_reports`, `forwards`, `forwards_opens`, `opens`, `unique_opens`, `last_open`, `clicks`, `unique_clicks`, `last_click`, `users_who_clicked`, `emails_sent` ) 
							VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) 
							ON DUPLICATE KEY UPDATE `syntax_errors` = ?, `hard_bounces` = ?, `soft_bounces` = ?, `unsubscribes` = ?, `abuse_reports` = ?, `forwards` = ?, `forwards_opens` = ?, `opens` = ?, `unique_opens` = ?, `last_open` = ?, `clicks` = ?, `unique_clicks` = ?, `last_click` = ?, `users_who_clicked` = ?, `emails_sent` = ?', 
							'siiiiiiiiisiisii' . 'iiiiiiiiisiisii', 
							$mc_campaign_id, $s['syntax_errors'], $s['hard_bounces'], $s['soft_bounces'], $s['unsubscribes'], $s['abuse_reports'], $s['forwards'], $s['forwards_opens'], $s['opens'], $s['unique_opens'], (int) $s['last_open'], $s['clicks'], $s['unique_clicks'], (int) $s['last_click'], $s['users_who_clicked'], $s['emails_sent'], 
							$s['syntax_errors'], $s['hard_bounces'], $s['soft_bounces'], $s['unsubscribes'], $s['abuse_reports'], $s['forwards'], $s['forwards_opens'], $s['opens'], $s['unique_opens'], (int) $s['last_open'], $s['clicks'], $s['unique_clicks'], (int) $s['last_click'], $s['users_who_clicked'], $s['emails_sent'] )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update analytics.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}

	/***** OTHER FUNCTIONS *****/
	
	/**
	 * Gets the click overlay email
	 *
     * @param string $mc_campaign_id
	 * @return array
	 */
	public function click_overlay_html( $mc_campaign_id ) {
		global $user;
		
		$email_message_id = $this->db->prepare( 'SELECT `email_message_id` FROM `email_messages` WHERE `mc_campaign_id` = ? AND `website_id` = ?', 'si', $mc_campaign_id, $user['website']['website_id'] )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get email message id.', __LINE__, __METHOD__ );
			return false;
		}
		
		$mc = $this->mailchimp_instance();
		
		$message = $mc->campaignContent( $mc_campaign_id );
		
		// Handle errors
		if ( $mc->errorCode ) {
			$this->err( "MailChimp: Unable to get Campaign Content\n\nCampaign ID: $mc_campaign_id\nCode: " . $mc->errorCode . "\nError Message:  " . $mc->errorMessage, __LINE__, __METHOD__ );
			return false;
		}
		
		return $message['html'];
	}
	
	/**
	 * Initiate Mailchimp
	 *
	 * @return pointer
	 */
	private function mailchimp_instance() {
		// If it's set, use it
		if ( isset( $this->mc ) )
			return $this->mc;
		
		// Include the library and instantiate it
		library( 'MCAPI' );
		$this->mc = new MCAPI( config::key('mc-api') );
		
		return $this->mc;
	}
	
	/**
	 * Gives the correct calculation for a metric
	 *
	 * @param string $metric
	 * @return string
	 */
	private function metric_sql_calculation( $metric ) {
        // Initialize variables
        $ga_metric = $ga_dimension = $ga_filter = NULL;

		// Determine what it's supposed to be
		switch ( $metric ) {
			case 'bounce_rate':
				// $sql_select = "ROUND( SUM( `bounces` ) / SUM(`entrances`) * 100, 2 )";
                $ga_metric = 'entranceBounceRate';
			break;
			
			case 'direct':
				// $sql_select = "ROUND( SUM( IF( '(none)' = `medium`, 1, 0 ) ) / SUM( 1 ) * 100, 2 )";
                $ga_dimension = 'medium';
                $ga_filter = 'medium==(none)';
			break;
			
			case 'exit_rate':
				//$sql_select = "ROUND( SUM( `exits` ) / SUM( `page_views` ) * 100, 2 )";
                $ga_metric = 'exitRate';
			break;
				
			case 'new_visits':
				//$sql_select = "ROUND( SUM( `new_visits` ) / SUM( `visits` ) * 100, 2 )";
                $ga_metric = 'percentNewVisits';
			break;
			
			case 'pages_by_visits':
				//$sql_select = "ROUND( SUM( `page_views` ) / SUM( `visits` ), 2 )";
                $ga_metric = 'pageviewsPerVisit';
			break;

            case 'page_views':
                $ga_metric = 'pageviews';
            break;
			
			case 'referring':
				//$sql_select = "ROUND( SUM( IF( 'referral' = `medium`, 1, 0 ) ) / SUM( 1 ) * 100, 2 )";
                $ga_dimension = 'medium';
                $ga_filter = 'medium==referral';
			break;

			case 'search_engines':
				//$sql_select = "ROUND( SUM( IF( 'organic' = `medium`, 1, 0 ) ) / SUM( 1 ) * 100, 2 )";
                $ga_dimension = 'medium';
                $ga_filter = 'medium==organic';
			break;

			case 'time_on_site':
                //$sql_select = "( SUM( `time_on_page` ) / SUM(`visits`) ) * 1000";
                $ga_metric = 'avgTimeOnSite';
			break;
			
			case 'time_on_page':
				//$sql_select = "SUM( `time_on_page` ) / ( SUM( `page_views` ) - SUM( `exits` ) ) * 1000";
                $ga_metric = 'avgTimeOnPage';
			break;

			default:
				$ga_metric = $metric;
			break;
		}

		return array( $ga_dimension, $ga_metric, $ga_filter );
	}
	
	/**
	 * Pads an array with 0 values
	 *
	 * @param array $array (optional) the array where the key is a date
	 * @param int $start_interval where to start the key filling in the array
	 * @param int $end_interval where to end the key killing in the array
	 * @return array
	 */
	private function pad_dates( $array, $start_interval, $end_interval ) {
		if ( !is_array( $array ) )
			return false;
		
		// Create an empty array with all the keys necessary
		$date_padding = array_fill_keys( range( $start_interval, $end_interval, 86400000 ), 0 );
		
		// Merge the arrays
		foreach ( $date_padding as $k => $v ) {
			if ( array_key_exists( $k, $array ) ) {
				$padded_array[$k] = $array[$k];
				continue;
			} elseif ( array_key_exists( $k - 3600000, $array ) ) {
				$padded_array[$k] = $array[$k - 3600000];
				continue;
			} elseif ( array_key_exists( $k + 3600000, $array ) ) {
				$padded_array[$k] = $array[$k + 3600000];
				continue;
			}
			
			$padded_array[$k] = 0;
		}
		
		return $padded_array;
	}
	
	/**
	 * Gets the dates
	 *
	 * @param string $date_start (optional|)
	 * @param string $date_end (optional|)
	 * @return array
	 */
	public function dates( $date_start = '', $date_end = '' ) {
		if ( !empty( $date_start ) )
			$this->date_start = $date_start;
		
		if ( !empty( $date_end ) )
			$this->date_end = $date_end;
		
		return array( $this->date_start, $this->date_end );
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}