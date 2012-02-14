<?php
/**
 * @page Analytics
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

// Get the dates
$dates = $a->dates();
$date_start = date( 'M j, Y', strtotime( $dates[0] ) );
$date_end = date( 'M j, Y', strtotime( $dates[1] ) );

// Sparklines
$sparklines['visits'] = $a->create_sparkline( $records );
$sparklines['page_views'] = $a->sparkline( 'page_views' );
$sparklines['bounce_rate'] = $a->sparkline( 'bounce_rate' );
$sparklines['time_on_site'] = $a->sparkline( 'time_on_site' );

$content_overview_pages = $a->get_content_overview();

css( 'analytics' );
javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'swfobject', 'JSON', 'analytics/dashboard' );

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
$title = _('Craigslist') . ' | ' . _('Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<div class="dates">
        <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
        -
        <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
    </div>
	<h1><?php echo _('Craigslist'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'craigslist' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'get-graph', '_ajax_get_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Craigslist'); ?></p>
			<div class="info-box-content">
				<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
					<tr>
						<td width="15%"><a href="#views" class="sparkline" title="<?php echo _('Views Sparkline'); ?>"><img src="<?php echo $sparklines['visits']; ?>" width="150" height="36" alt="<?php echo _('Visits Sparkline'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['visits'] ); ?></span> <span class="label"><?php echo _('Views'); ?></span></td>
						<td width="15%"><a href="#unique_views" class="sparkline" title="<?php echo _('Unique Views Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Unique Views'); ?></span></td>
					</tr>
					<tr>
						<td><a href="#posts" class="sparkline" title="<?php echo _('Posts Sparkline'); ?>"><img src="<?php echo $sparklines['page_views']; ?>" width="150" height="36" alt="<?php echo _('Posts Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo number_format( $total['page_views'] ); ?></span> <span class="label"><?php echo _('Posts'); ?></span></td>
					</tr>
				</table>
			</div>
		</div>
		<br clear="both" /><br />
        <div class="info-box">
            <p class="info-box-title"><?php echo _('Markets'); ?></p>
            <div class="info-box-content">
                <table cellpadding="0" cellspacing="0" width="100%" class="form">
                    <tr>
                        <th width="40%"><strong><?php echo _('Markets'); ?></strong></th>
                        <th class="text-right"><strong><?php echo _('Views'); ?></strong></th>
                        <th class="text-right"><strong><?php echo _('Unique Views'); ?></strong></th>
                        <th class="text-right"><strong><?php echo _('Posts'); ?></strong></th>
                    </tr>
                    <?php
                    if ( is_array( $content_overview_pages ) )
                    foreach ( $content_overview_pages as $top ) {
                    ?>
                    <tr>
                        <td><a href="/analytics/page/?p=<?php echo urlencode( $top['page'] ); ?>" title="<?php echo $top['page']; ?>"><?php echo $top['page']; ?></a></td>
                        <td class="text-right"><?php echo number_format( $top['page_views'] ); ?></td>
                        <td class="text-right"><?php echo round( $top['page_views'] / $total['page_views'] * 100, 2 ); ?>%</td>
                    </tr>
                    <?php } ?>
                </table>
                <br />
                <p align="right"><a href="/analytics/content-overview/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
            </div>
        </div>
		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>