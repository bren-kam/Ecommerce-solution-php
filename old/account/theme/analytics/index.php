<?php
/**
 * @page Analytics
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['live'] )
	url::redirect('/');

// Get start and end dates
$date_start = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : NULL;
$date_end = (isset( $_GET['de'] ) ) ? $_GET['de'] : NULL;

// Instantiate class
$a = new Analytics( $user['website']['ga_profile_id'], $date_start, $date_end );
$response = $a->get_response();

if ( $response->success() ) {
	// Main Analytics
	$records = $a->get_metric_by_date( 'visits' );
	$total = $a->get_totals();
	$traffic_sources = $a->get_traffic_sources_totals();
	$visits_plotting_array = array();
	
	// Pie Chart
	$pie_chart = $a->pie_chart( $traffic_sources );
	
	// Visits plotting
	if ( is_array( $records ) )
	foreach ( $records as $r_date => $r_value ) {
		$visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
	}
	
	$visits_plotting = implode( ',', $visits_plotting_array );
	
	// Sparklines
	$sparklines['visits'] = $a->create_sparkline( $records );
	$sparklines['page_views'] = $a->sparkline( 'page_views' );
	$sparklines['bounce_rate'] = $a->sparkline( 'bounce_rate' );
	$sparklines['time_on_site'] = $a->sparkline( 'time_on_site' );
	
	$content_overview_pages = $a->get_content_overview();
	
	// Get the dates
	$dates = $a->dates();
	$date_start = date( 'M j, Y', strtotime( $dates[0] ) );
	$date_end = date( 'M j, Y', strtotime( $dates[1] ) );

	// Load the jQuery UI CSS
	add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
	
	if ( !$a instanceof Response ) {
		add_before_javascript('function open_flash_chart_data() { return JSON.stringify(' . $pie_chart . ');}');
		add_javascript_callback("$.plot($('#dLargeGraph'),[
					{ label: 'Visits', data: [$visits_plotting], color: '#FFA900' }
				],{
					lines: { show: true, fill: true },
					points: { show: true },
					selection: { mode: 'x' },
					grid: { hoverable: true, clickable: true },
					legend: { position: 'se' },
					xaxis: { mode: 'time' },
					yaxis: { min: 0 }
			});
			
			active_graph = 'Visits', percent = '', time = false;
			
			swfobject.embedSWF('/media/flash/open-flash-chart.swf', 'dTrafficSources', '200', '200', '9.0.0', '', null, { wmode:'transparent' } );
		");
	}
}

css( 'analytics' );
javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'JSON', 'analytics/dashboard' );


$selected = "analytics";
$title = _('Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<?php if ( $response->success() ) { ?>
	<div class="dates">
        <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
        -
        <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
    </div>
	<?php } ?>
	<h1><?php echo _('Dashboard'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'dashboard' ); ?>
	<div id="subcontent">
		<?php if ( $response->success() ) { ?>
			<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
			<br />
			<div class="info-box col-1">
				<p class="info-box-title"><?php echo _('Site Usage'); ?></p>
				<div class="info-box-content">
					<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
						<tr>
							<td width="15%"><a href="#visits" class="sparkline" title="<?php echo _('Visits Sparkline'); ?>"><img src="<?php echo $sparklines['visits']; ?>" width="150" height="36" alt="<?php echo _('Visits Sparkline'); ?>" /></a></td>
							<td width="35%"><span class="data"><?php echo number_format( $total['visits'] ); ?></span> <span class="label"><?php echo _('Visits'); ?></span></td>
							<td width="15%"><a href="#bounce_rate" class="sparkline" title="<?php echo _('Bounce Rate Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
							<td width="35%"><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Bounce Rate'); ?></span></td>
						</tr>
						<tr>
							<td><a href="#page_views" class="sparkline" title="<?php echo _('Page Views Sparkline'); ?>"><img src="<?php echo $sparklines['page_views']; ?>" width="150" height="36" alt="<?php echo _('Page Views Sparkline'); ?>" /></a></td>
							<td><span class="data"><?php echo number_format( $total['page_views'] ); ?></span> <span class="label"><?php echo _('Page Views'); ?></span></td>
							<td><a href="#time_on_site" class="sparkline" title="<?php echo _('Avg. Time On Site Sparkline'); ?>"><img src="<?php echo $sparklines['time_on_site']; ?>" width="150" height="36" alt="<?php echo _('Avg. Time On Site Sparkline'); ?>" /></a></td>
							<td><span class="data"><?php echo $total['time_on_site']; ?></span> <span class="label"><?php echo _('Avg. Time on Site'); ?></span></td>
						</tr>
					</table>
				</div>
			</div>
			<br clear="both" /><br />
			<div class="col-2 float-left">
				<div class="info-box">
					<p class="info-box-title"><?php echo _('Traffic Sources Overview'); ?></p>
					<div class="info-box-content">
						<div class="pie-chart-container" style="width:190px; right:0; left: 30px;"><div id="dTrafficSources"></div></div>
						<div id="dTrafficSourcesData">
							<p class="blue-marker">
								<span class="label"><?php echo _('Direct Traffic'); ?></span><br />
								<span class="data"><?php echo number_format( $traffic_sources['direct'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['direct'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
							</p>
							<p class="green-marker">
								<span class="label"><?php echo _('Referring Sites'); ?></span><br />
								<span class="data"><?php echo number_format( $traffic_sources['referring'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['referring'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
							</p>
							<p class="orange-marker">
								<span class="label"><?php echo _('Search Engines'); ?></span><br />
								<span class="data"><?php echo number_format( $traffic_sources['search_engines'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['search_engines'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
							</p>
							<?php if ( $traffic_sources['email'] > 0 ) { ?>
							<p class="yellow-marker">
								<span class="label"><?php echo _('Campaigns'); ?></span><br />
								<span class="data"><?php echo number_format( $traffic_sources['email'] ); ?> (<?php echo round( $traffic_sources['email'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
							</p>
							<?php } ?>
						</div>
						<br clear="left" />
						<p align="right";><a href="/analytics/traffic-sources/overview/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="gray"><?php echo _('Report'); ?></span></a></p>
					</div>
				</div>
			</div>
			<div class="col-2 float-left">
				<div class="info-box">
					<p class="info-box-title"><?php echo _('Content Overview'); ?></p>
					<div class="info-box-content">
						<table cellpadding="0" cellspacing="0" width="100%" class="form">
							<tr>
								<th width="40%"><strong><?php echo _('Pages'); ?></strong></th>
								<th align="right"><strong><?php echo _('Page Views'); ?></strong></th>
								<th align="right"><strong><?php echo _('% Page Views'); ?></strong></th>
							</tr>
							<?php 
							if ( is_array( $content_overview_pages ) )
							foreach ( $content_overview_pages as $top ) {
							?>
							<tr>
								<td><a href="/analytics/page/?p=<?php echo urlencode( $top['page'] ); ?>" title="<?php echo $top['page']; ?>"><?php echo ( '/' == $top['page'] ) ? 'Home' : $top['page']; ?></a></td>
								<td align="right"><?php echo number_format( $top['page_views'] ); ?></td>
								<td align="right"><?php echo round( $top['page_views'] / $total['page_views'] * 100, 2 ); ?>%</td>
							</tr>
							<?php } ?>
						</table>
						<br />
						<p align="right"><a href="/analytics/content-overview/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
					</div>
				</div>
			</div>
			<?php 
			nonce::field( 'get-graph', '_ajax_get_graph'); 
		} else {
			$errs = '';
			
			switch ( $response->error_code() ) {
				case 0:
				default:
					$errs = _("Oops! We had a little trouble getting your data. But don't worry, it's still there. Please contact your Online Specialist about connecting your account with your analytics.");
				break;
			}
			
			echo '<p class="error">' . $errs . '</p><br /><br /><br /><br />';
		}
		?>

		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>