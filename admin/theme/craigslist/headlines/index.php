<?php
/**
 * @page Craigslist - Headlines
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools', 'data-tables/ui', 'craigslist/headlines/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard', 'data-tables/jquery.tableTools', 'craigslist/headlines/list' );

$selected = 'craigslist';
$title = _('Headlines') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Craigslist Headlines'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'headlines' ); ?>
	<div id="subcontent">
        <?php nonce::field( 'delete-craigslist-headline', '_ajax_delete_craigslist_headline' ); ?>
		<br clear="left" /><br />
		<br />
		<table cellpadding="0" cellspacing="0" width="100%" id="tListCraigslistHeadlines">
			<thead>
				<tr>
					<th width="50%"><?php echo _('Headline'); ?></th>
					<th width="30%"><?php echo _('Category'); ?></th>
					<th width="20%"><?php echo _('Created'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>