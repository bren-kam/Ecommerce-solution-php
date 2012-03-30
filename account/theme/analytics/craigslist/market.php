<?php
/**
 * @page Craigslist - Analytics Markets
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

// Get the market ID
$craigslist_market_id = (int) $_GET['cmid'];

// Make sure they have a market ID
if( !$craigslist_market_id )
    url::redirect('/analytics/craigslist/');

// Instantiate class
$c = new Craigslist();
$a = new Analytics( NULL, $_GET['ds'], $_GET['de'] );

// Get the market
$market = $c->get_craigslist_market( $craigslist_market_id );

// Main Analytics
$records = $a->get_craigslist_metric_by_date( 'market', 'views', $craigslist_market_id );
$total = $a->get_craigslist_totals( 'market', $craigslist_market_id );

$views_plotting_array = array();

// Views plotting
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
$sparklines['unique'] = $a->craigslist_sparkline( 'market', 'unique', $craigslist_market_id );
$sparklines['posts'] = $a->craigslist_sparkline( 'market', 'posts', $craigslist_market_id );

$categories = $a->get_craigslist_overview( 'categories', $craigslist_market_id );
$products = $a->get_craigslist_overview( 'products', $craigslist_market_id );

css( 'analytics' );
javascript( 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'analytics/craigslist-dashboard' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

add_javascript_callback("$.plot($('#dLargeGraph'),[
			{ label: 'Views', data: [$views_plotting], color: '#FFA900' }
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
$title = $market['market'] . ' | ' . _('Craigslist') . ' | ' . _('Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<div class="dates">
        <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
        -
        <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
    </div>
	<h1><?php echo $market['market']; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'craigslist' ); ?>
	<div id="subcontent">
        <input type="hidden" id="hCraigslistMarketID" value="<?php echo $craigslist_market_id; ?>" />
		<?php nonce::field( 'get-craigslist-graph', '_ajax_get_craigslist_graph'); ?>
		<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
		<br />
		<div class="info-box col-1">
			<p class="info-box-title"><?php echo $market['market']; ?></p>
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
        <div class="col-2 float-left">
            <div class="info-box">
                <p class="info-box-title"><?php echo _('Categories'); ?></p>
                <div class="info-box-content">
                    <table cellpadding="0" cellspacing="0" width="100%" class="form">
                        <tr>
                            <th width="40%"><strong><?php echo _('Category'); ?></strong></th>
                            <th class="text-right"><strong><?php echo _('Views'); ?></strong></th>
                            <th class="text-right"><strong><?php echo _('Unique Views'); ?></strong></th>
                            <th class="text-right"><strong><?php echo _('Posts'); ?></strong></th>
                        </tr>
                        <?php
                        if ( is_array( $categories ) )
                        foreach ( $categories as $category ) {
                        ?>
                        <tr>
                            <td><a href="/analytics/craigslist/category/?cmid=<?php echo $craigslist_market_id; ?>&amp;cid=<?php echo $category['category_id']; ?>" title="<?php echo $category['category']; ?>"><?php echo $category['category']; ?></a></td>
                            <td class="text-right"><?php echo number_format( $category['views'] ); ?></td>
                            <td class="text-right"><?php echo number_format( $category['unique'] ); ?></td>
                            <td class="text-right"><?php echo number_format( $category['posts'] ); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <br />
					<p align="right"><a href="/analytics/craigslist/categories/?cmid=<?php echo $craigslist_market_id; ?>" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
				</div>
            </div>
        </div>
        <div class="col-2 float-left">
            <div class="info-box">
                <p class="info-box-title"><?php echo _('Products'); ?></p>
                <div class="info-box-content">
                    <table cellpadding="0" cellspacing="0" width="100%" class="form">
                        <tr>
                            <th width="40%"><strong><?php echo _('Product'); ?></strong></th>
                            <th class="text-right"><strong><?php echo _('Views'); ?></strong></th>
                            <th class="text-right"><strong><?php echo _('Unique Views'); ?></strong></th>
                            <th class="text-right"><strong><?php echo _('Posts'); ?></strong></th>
                        </tr>
                        <?php
                        if ( is_array( $products ) )
                        foreach ( $products as $product ) {
                        ?>
                        <tr>
                            <td><a href="/analytics/craigslist/product/?cmid=<?php echo $craigslist_market_id; ?>&amp;pid=<?php echo $product['product_id']; ?>" title="<?php echo $product['product']; ?>"><?php echo $product['product']; ?></a></td>
                            <td class="text-right"><?php echo number_format( $product['views'] ); ?></td>
                            <td class="text-right"><?php echo number_format( $product['unique'] ); ?></td>
                            <td class="text-right"><?php echo number_format( $product['posts'] ); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <br />
					<p align="right"><a href="/analytics/craigslist/products/?cmid=<?php echo $craigslist_market_id; ?>" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
				</div>
            </div>
        </div>
		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>