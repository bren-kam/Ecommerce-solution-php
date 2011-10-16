<?php
/**
 * @page Analytics - Traffic Sources
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

// Redirect to main section if they don't have email marketing
if( !$user['website']['live'] )
	url::redirect('/');

// Instantiate class
$a = new Analytics( $user['website']['ga_profile_id'] );

// Main Analytics
$records = $a->get_metric_by_date( 'visits' );
$total = array_merge( $a->get_traffic_sources_totals(), $a->get_totals() );
$traffic_sources = $a->get_traffic_sources( '', '', 0 );

// Visits plotting
foreach( $records as $r_date => $r_value ) {
	$visits_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
}

$visits_plotting = implode( ',', $visits_plotting_array );

// Get the dates
$dates = $a->dates();
$date_start = date( 'M j, Y', strtotime( $dates[0] ) );
$date_end = date( 'M j, Y', strtotime( $dates[1] ) );

// Sparklines
$sparklines['visits'] = $a->create_sparkline( $records );
$sparklines['pages_by_visits'] = $a->sparkline( 'pages_by_visits' );
$sparklines['time_on_site'] = $a->sparkline( 'time_on_site' );
$sparklines['new_visits'] = $a->sparkline( 'new_visits' );
$sparklines['bounce_rate'] = $a->sparkline( 'bounce_rate' );

css( 'analytics' );
javascript(  'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'analytics/dashboard' );

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
");

$selected = "analytics";
$title = _('Traffic Sources | Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h3><?php echo $date_start, ' - ', $date_end; ?></h3>
	<h1><?php echo _('Traffic Sources'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'traffic_sources_overview', 'traffic_sources' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'get-graph', '_ajax_get_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Traffic Source Totals'); ?></p>
			<div class="info-box-content">
				<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
					<tr>
						<td width="15%"><a href="#visits" class="sparkline" title="<?php echo _('Visits Sparkline'); ?>"><img src="<?php echo $sparklines['visits']; ?>" width="150" height="36" alt="<?php echo _('Visits Sparkline'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['total'] ); ?></span> <span class="label"><?php echo _('Visits'); ?></span></td>
						<td width="15%"><a href="#pages_by_visits" class="sparkline" title="<?php echo _('Pages/Visits Sparkline'); ?>"><img src="<?php echo $sparklines['pages_by_visits']; ?>" width="150" height="36" alt="<?php echo _('Pages/Visits Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo $total['pages_by_visits']; ?></span> <span class="label"><?php echo _('Pages/Visits'); ?></span></td>
					</tr>
					<tr>
						<td><a href="#time_on_site" class="sparkline" title="<?php echo _('Avg. Time On Site Sparkline'); ?>"><img src="<?php echo $sparklines['time_on_site']; ?>" width="150" height="36" alt="<?php echo _('Avg. Time On a Page Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo $total['time_on_site']; ?></span> <span class="label"><?php echo _('Avg. Time on Site'); ?></span></td>
						<td><a href="#new_visits" class="sparkline" title="<?php echo _('New Visits Sparkline'); ?>"><img src="<?php echo $sparklines['new_visits']; ?>" width="150" height="36" alt="<?php echo _('New Visits Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo number_format( $total['new_visits'], 2 ); ?>%</span> <span class="label"><?php echo _('New Visits'); ?></span></td>
					</tr>
					<tr>
						<td><a href="#bounce_rate" class="sparkline" title="<?php echo _('Bounce Rate Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Bounce Rate'); ?></span></td>
					</tr>
				</table>
			</div>
		</div>
		<br clear="both" /><br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Traffic Sources'); ?></p>
			<div class="info-box-content">
				<br /><br />
				<br /><br />
				<table cellpadding="0" cellspacing="0" width="100%" class="dt" perPage="30,50,100">
					<thead>
						<tr>
							<th><?php echo _('Source/Medium'); ?></th>
							<th class="text-right" sort="1 desc" column="formatted-num"><?php echo _('Visits'); ?></th>
							<th class="text-right" column="formatted-num"><?php echo _('Pages/Visit'); ?></th>
							<th class="text-right"><?php echo _('Avg. Time on Site'); ?></th>
							<th class="text-right" column="formatted-num"><?php echo _('% New Visits'); ?></th>
							<th class="text-right" column="formatted-num"><?php echo _('Bounce Rate'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach( $traffic_sources as $ts ) { ?>
					<tr>
						<td><a href="/analytics/source/?s=<?php echo urlencode( $ts['source'] ); ?>" title="<?php echo $ts['source'], ' / ', $ts['medium']; ?>"><?php echo $ts['source'], ' / ', $ts['medium']; ?></a></td>
						<td class="text-right"><?php echo number_format( $ts['visits'], 2 ); ?></td>
						<td class="text-right"><?php echo number_format( $ts['pages_by_visits'], 2 ); ?></td>
						<td class="text-right"><?php echo $ts['time_on_site']; ?></td>
						<td class="text-right"><?php echo $ts['new_visits']; ?>%</td>
						<td class="text-right last"><?php echo $ts['bounce_rate']; ?>%</td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
				<br />
			</div>
		</div>
		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>