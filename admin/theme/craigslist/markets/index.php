<?php
/**
 * @page Craigslist - Markets
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools', 'data-tables/ui', 'craigslist/markets/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard', 'data-tables/jquery.tableTools', 'craigslist/markets/list' );

$selected = 'craigslist';
$title = _('Markets') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Craigslist Markets'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'markets' ); ?>
	<div id="subcontent">
        <?php nonce::field( 'delete-craigslist-market', '_ajax_delete_craigslist_market' ); ?>
		<br clear="left" /><br />
		<br />
		<table cellpadding="0" cellspacing="0" width="100%" id="tListCraigslistMarkets">
			<thead>
				<tr>
					<th width="70%"><?php echo _('Market'); ?></th>
					<th width="30%"><?php echo _('Created'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>