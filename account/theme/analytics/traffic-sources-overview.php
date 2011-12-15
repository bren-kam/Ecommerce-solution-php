<?php
/**
 * @page Analytics - Traffic Sources Overview
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['live'] )
	url::redirect('/');

// Instantiate class
$a = new Analytics( $user['website']['ga_profile_id'], $_GET['ds'], $_GET['de'] );

// Main Analytics
$records = $a->get_metric_by_date( 'visits' );
$traffic_sources = $a->get_traffic_sources_totals();

// Pie Chart
$pie_chart = $a->pie_chart( $traffic_sources );

// Initialize
$visits_plotting_array = array();

// Visits plotting
if ( is_array( $records ) )
foreach ( $records as $r_date => $r_value ) {
	$visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
}

$visits_plotting = implode( ',', $visits_plotting_array );

// Get the dates
$dates = $a->dates();
$date_start = date( 'M j, Y', strtotime( $dates[0] ) );
$date_end = date( 'M j, Y', strtotime( $dates[1] ) );

// Sparklines
$sparklines['direct'] = $a->sparkline( 'direct' );
$sparklines['referring'] = $a->sparkline( 'referring' );
$sparklines['search_engines'] = $a->sparkline( 'search_engines' );

$top_traffic_sources = $a->get_traffic_sources();
$top_keywords = $a->get_keywords();

css( 'analytics' );
javascript(  'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'JSON', 'analytics/dashboard' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

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

$selected = "analytics";
$title = _('Traffic Sources Overview | Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<div class="dates">
        <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
        -
        <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
    </div>
	<h1><?php echo _('Traffic Sources Overview'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'traffic_sources_overview' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'get-graph', '_ajax_get_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Traffic Source Totals'); ?></p>
			<div class="info-box-content">
				<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
					<tr>
						<td width="50%">
							<div id="dSmallContainer">
								<p><a href="#direct" class="sparkline" title="<?php echo _('Direct Traffic Sparkline'); ?>"><img src="<?php echo $sparklines['direct']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Direct Traffic Sparkline'); ?>" /></a> <span class="data"><?php echo ( 0 == $traffic_sources['total'] ) ? '0' : number_format( $traffic_sources['direct'] / $traffic_sources['total'] * 100, 2 ); ?>%</span> <span class="label"><?php echo _('Direct Traffic'); ?></span></p>
								<p><a href="#referring" class="sparkline" title="<?php echo _('Referring Sites Sparkline'); ?>"><img src="<?php echo $sparklines['referring']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Referring Sites Sparkline'); ?>" /></a> <span class="data"><?php echo ( 0 == $traffic_sources['total'] ) ? '0' : number_format( $traffic_sources['referring'] / $traffic_sources['total'] * 100, 2 ); ?>%</span> <span class="label"><?php echo _('Referring Sites'); ?></span></p>
								<p><a href="#search_engines" class="sparkline" title="<?php echo _('Search Engines Sparkline'); ?>"><img src="<?php echo $sparklines['search_engines']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Search Engines Sparkline'); ?>" /></a> <span class="data"><?php echo ( 0 == $traffic_sources['total'] ) ? '0' : number_format( $traffic_sources['search_engines'] / $traffic_sources['total'] * 100, 2 ); ?>%</span> <span class="label"><?php echo _('Search Engines'); ?></span></p>
							</div>
						</td>
						<td style="padding-top: 20px">
							<div class="pie-chart-container"><div id="dTrafficSources"></div></div>
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
							<br clear="all" />
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br clear="both" /><br />
		<div class="col-2 float-left">
			<div class="info-box">
				<p class="info-box-title"><?php echo _('Top Traffic Sources'); ?></p>
				<div class="info-box-content">
					<table cellpadding="0" cellspacing="0" width="100%" class="form">
						<tr>
							<th width="60%"><strong><?php echo _('Sources'); ?></strong></th>
							<th width="20%" class="text-right"><strong><?php echo _('Visits'); ?></strong></th>
							<th width="20%" class="text-right"><strong><?php echo _('% New Visits'); ?></strong></th>
						</tr>
						<?php 
						if ( is_array( $top_traffic_sources ) )
						foreach ( $top_traffic_sources as $tts ) { 
						?>
						<tr>
							<td><a href="/analytics/source/?s=<?php echo urlencode( $tts['source'] ); ?>" title="<?php echo $tts['source'], ' / ', $tts['medium']; ?>"><?php echo $tts['source']; ?></a></td>
							<td class="text-right"><?php echo number_format( $tts['visits'] ); ?></td>
							<td class="text-right"><?php echo $tts['new_visits']; ?>%</td>
						</tr>
						<?php } ?>
					</table>
					<br />
					<p align="right";><a href="/analytics/traffic-sources/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="gray"><?php echo _('Report'); ?></span></a></p>
				</div>
			</div>
		</div>
		<div class="col-2 float-left">
			<div class="info-box">
				<p class="info-box-title"><?php echo _('Top Keywords'); ?></p>
				<div class="info-box-content">
					<table cellpadding="0" cellspacing="0" width="100%" class="form">
						<tr>
							<th width="60%"><strong><?php echo _('Keywords'); ?></strong></th>
							<th width="20%" class="text-right" column="formatted-num"><strong><?php echo _('Visits'); ?></strong></th>
							<th width="20%" class="text-right" column="formatted-num"><strong><?php echo _('% New Visits'); ?></strong></th>
						</tr>
						<?php 
						if ( is_array( $top_keywords ) )
						foreach ( $top_keywords as $tk ) {
						?>
						<tr>
							<td><a href="/analytics/keyword/?k=<?php echo urlencode( $tk['keyword'] ); ?>" title="<?php echo $tk['keyword']; ?>"><?php echo $tk['keyword']; ?></a></td>
							<td class="text-right"><?php echo number_format( $tk['visits'] ); ?></td>
							<td class="text-right"><?php echo $tk['new_visits']; ?>%</td>
						</tr>
						<?php } ?>
					</table>
					<br />
					<p align="right"><a href="/analytics/traffic-keywords/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
				</div>
			</div>
		</div>
		
		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>