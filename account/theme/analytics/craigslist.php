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
$a = new Analytics( NULL, $_GET['ds'], $_GET['de'] );

// Main Analytics
$records = $a->get_craigslist_metric_by_date( 'market', 'views' );
$total = $a->get_craigslist_totals( 'market' );

$views_plotting_array = array();

// Visits plotting
if ( is_array( $records ) )
foreach ( $records as $r_date => $r_value ) {
	$views_plotting_array[] = '[' . $r_date . ', ' . $r_value . ']';
}

$views_plotting = implode( ',', $views_plotting_array );

// Get the dates
$dates = $a->dates();
$date_start = date( 'M j, Y', strtotime( $dates[0] ) );
$date_end = date( 'M j, Y', strtotime( $dates[1] ) );

// Sparklines
$sparklines['views'] = $a->create_sparkline( $records );
$sparklines['unique'] = $a->craigslist_sparkline( 'market', 'unique' );
$sparklines['posts'] = $a->craigslist_sparkline( 'market', 'posts' );

$markets = $a->get_craigslist_overview( 'markets' );

css( 'analytics' );
javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'analytics/craigslist-dashboard' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

add_javascript_callback("$.plot($('#dLargeGraph'),[
			{ label: 'Visits', data: [$views_plotting], color: '#FFA900' }
		],{
			lines: { show: true, fill: true },
			points: { show: true },
			selection: { mode: 'x' },
			grid: { hoverable: true, clickable: true },
			legend: { position: 'se' },
			xaxis: { mode: 'time' },
			yaxis: { min: 0 }
	});
	
	active_graph = 'Views', percent = '', time = false;
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
		<?php nonce::field( 'get-craigslist-graph', '_ajax_get_craigslist_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo _('Craigslist'); ?></p>
			<div class="info-box-content">
				<table cellpadding="0" cellspacing="0" width="100%" id="sparklines">
					<tr>
						<td width="15%"><a href="#views" rel="market" class="sparkline" title="<?php echo _('Views Sparkline'); ?>"><img src="<?php echo $sparklines['views']; ?>" width="150" height="36" alt="<?php echo _('Views Sparkline'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['views'] ); ?></span> <span class="label"><?php echo _('Views'); ?></span></td>
						<td width="15%"><a href="#unique" rel="market" class="sparkline" title="<?php echo _('Unique Views Sparkline'); ?>"><img src="<?php echo $sparklines['unique']; ?>" width="150" height="36" alt="<?php echo _('Unique Views'); ?>" /></a></td>
						<td width="35%"><span class="data"><?php echo number_format( $total['unique'] ); ?></span> <span class="label"><?php echo _('Unique Views'); ?></span></td>
					</tr>
					<tr>
						<td><a href="#posts" rel="market" class="sparkline" title="<?php echo _('Posts Sparkline'); ?>"><img src="<?php echo $sparklines['posts']; ?>" width="150" height="36" alt="<?php echo _('Posts Sparkline'); ?>" /></a></td>
						<td><span class="data"><?php echo number_format( $total['posts'] ); ?></span> <span class="label"><?php echo _('Posts'); ?></span></td>
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
                        <th width="40%"><strong><?php echo _('Market'); ?></strong></th>
                        <th class="text-right"><strong><?php echo _('Views'); ?></strong></th>
                        <th class="text-right"><strong><?php echo _('Unique Views'); ?></strong></th>
                        <th class="text-right"><strong><?php echo _('Posts'); ?></strong></th>
                    </tr>
                    <?php
                    if ( is_array( $markets ) )
                    foreach ( $markets as $market ) {
                    ?>
                    <tr>
                        <td><a href="/analytics/market/?cmid=<?php echo $market['craigslist_market_id']; ?>" title="<?php echo $market['market']; ?>"><?php echo $market['market']; ?></a></td>
                        <td class="text-right"><?php echo number_format( $market['views'] ); ?></td>
                        <td class="text-right"><?php echo number_format( $market['unique'] ); ?></td>
                        <td class="text-right"><?php echo number_format( $market['posts'] ); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>