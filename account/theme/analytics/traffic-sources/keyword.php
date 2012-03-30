<?php
/**
 * @page Analytics - Traffic Keywords
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

$keyword = $_GET['k'];

if ( empty( $keyword ) )
	url::redirect('/analytics/traffic-sources/keywords/');

// Instantiate class
$a = new Analytics( $user['website']['ga_profile_id'], $_GET['ds'], $_GET['de'] );

// Set global filter
$filter = "keyword==$keyword";
$a->set_ga_filter( $filter );

// Main Analytics
$records = $a->get_metric_by_date( 'visits' );
$total = $a->get_totals();

// Visits plotting
foreach ( $records as $r_date => $r_value ) {
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

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

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
$title = _('Keyword Details') . ' | ' . _('Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<div class="dates">
        <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
        -
        <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
    </div>
	<h1><?php echo _('Keyword:'), " $keyword"; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'traffic_sources_overview', 'keyword' ); ?>
	<div id="subcontent">
        <input type="hidden" id="hFilter" value="<?php echo $filter; ?>" />
		<?php nonce::field( 'get-graph', '_ajax_get_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Keyword Totals'); ?></p>
			<div class="info-box-content">
				<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
					<tr>
						<td width="15%"><a href="#visits" class="sparkline" title="<?php echo _('Visits Sparkline'); ?>"><img src="<?php echo $sparklines['visits']; ?>" width="150" height="36" alt="<?php echo _('Visits Sparkline'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['visits'] ); ?></span> <span class="label"><?php echo _('Visits'); ?></span></td>
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
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>