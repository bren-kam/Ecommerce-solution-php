<?php
/**
 * @page Craigslist - Accounts
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools', 'data-tables/ui', 'craigslist/accounts/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard', 'data-tables/jquery.tableTools', 'craigslist/accounts/list' );

$selected = 'craigslist';
$title = _('Accounts') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Craigslist Accounts'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'accounts' ); ?>
	<div id="subcontent">
		<br clear="left" /><br />
		<br />
		<table cellpadding="0" cellspacing="0" width="100%" id="tListCraigslistAccounts">
			<thead>
				<tr>
					<th width="40%"><?php echo _('Account'); ?></th>
					<th width="20%"><?php echo _('Plan'); ?></th>
					<th width="40%"><?php echo _('Markets'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>