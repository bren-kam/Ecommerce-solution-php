<?php
/**
 * @page Analytics - Content Overview
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

// Main Analytics
$records = $a->get_metric_by_date( 'page_views' );
$total = $a->get_totals();
$content_overview_pages = $a->get_content_overview( '', '', 0 );
$page_views_plotting_array = array();

// Visits plotting
if ( is_array( $records ) )
foreach ( $records as $r_date => $r_value ) {
	$page_views_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
}

$page_views_plotting = implode( ',', $page_views_plotting_array );

// Get the dates
$dates = $a->dates();
$date_start = date( 'M j, Y', strtotime( $dates[0] ) );
$date_end = date( 'M j, Y', strtotime( $dates[1] ) );

// Sparklines
$sparklines['page_views'] = $a->create_sparkline( $records );
$sparklines['bounce_rate'] = $a->sparkline( 'bounce_rate' );
$sparklines['time_on_page'] = $a->sparkline( 'time_on_page' );
$sparklines['exit_rate'] = $a->sparkline( 'exit_rate' );

css( 'analytics' );
javascript(  'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'analytics/dashboard' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

add_javascript_callback("$.plot($('#dLargeGraph'),[
			{ label: '" . _('Page Views') . "', data: [$page_views_plotting], color: '#FFA900' }
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
$title = _('Content Overview | Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<div class="dates">
        <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
        -
        <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
    </div>
	<h1><?php echo _('Content Overview'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'content_overview' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'get-graph', '_ajax_get_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Content Performance'); ?></p>
			<div class="info-box-content">
				<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
					<tr>
						<td width="15%"><a href="#page_views" class="sparkline" title="<?php echo _('Page Views Sparkline'); ?>"><img src="<?php echo $sparklines['page_views']; ?>" width="150" height="36" alt="<?php echo _('Page Views Sparkline'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['page_views'] ); ?></span> <span class="label"><?php echo _('Page Views'); ?></span></td>
						<td width="15%"><a href="#time_on_page" class="sparkline" title="<?php echo _('Time On Page Sparkline'); ?>"><img src="<?php echo $sparklines['time_on_page']; ?>" width="150" height="36" alt="<?php echo _('Time On Page'); ?>" /></a></td>
						<td><span class="data"><?php echo $total['time_on_page']; ?></span> <span class="label"><?php echo _('Time On Page'); ?></span></td>
					</tr>
					<tr>
						<td><a href="#bounce_rate" class="sparkline" title="<?php echo _('Bounce Rate Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Bounce Rate'); ?></span></td>
						<td><a href="#exit_rate" class="sparkline" title="<?php echo _('Exit Rate Sparkline'); ?>"><img src="<?php echo $sparklines['exit_rate']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Exit Rate Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo number_format( $total['exit_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Exit Rate'); ?></span></td>
					</tr>
				</table>
			</div>
		</div>
		<br clear="both" /><br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Pages'); ?></p>
			<div class="info-box-content">
				<br /><br />
				<br /><br />
				<table cellpadding="0" cellspacing="0" width="100%" class="dt" perPage="30,50,100">
					<thead>
						<tr>
							<th width="40%"><?php echo _('Pages'); ?></th>
							<th width="15%" class="text-right" sort="1 desc" column="formatted-num"><?php echo _('Page Views'); ?></th>
							<th width="15%" class="text-right"><?php echo _('Avg. Page Time'); ?></th>
							<th width="15%" class="text-right" column="formatted-num"><?php echo _('Bounce Rate'); ?></th>
							<th width="15%" class="text-right" column="formatted-num"><?php echo _('% Exit'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					if ( is_array( $content_overview_pages ) )
					foreach ( $content_overview_pages as $top ) { 
						$top['page'] = str_replace( '&', '&amp;', $top['page'] ); 
					?>
					<tr>
						<td><a href="/analytics/page/?p=<?php echo urlencode( $top['page'] ); ?>" title="<?php echo $top['page']; ?>"><?php echo ( '/' == $top['page'] ) ? 'Home' : $top['page']; ?></a></td>
						<td class="text-right"><?php echo number_format( $top['page_views'] ); ?></td>
						<td class="text-right"><?php echo $top['time_on_page']; ?></td>
						<td class="text-right"><?php echo number_format( $top['bounce_rate'], 2 ); ?>%</td>
						<td class="text-right last"><?php echo number_format( $top['exit_rate'], 2 ); ?>%</td>
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