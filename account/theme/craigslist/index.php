<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$title = _('Craigslist Ads') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Craigslist Ads'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/' ); ?>
	<div id="subcontent">
		<br clear="left" /><br />
		<br />
		<table ajax="/ajax/craigslist/list/" perPage="100,250,500" sort="1" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="20%"><?php echo _('Title'); ?></th>
					<th width="40%"><?php echo _('Content'); ?></th>
					<th width="10%"><?php echo _('Product Name'); ?></th>
					<th width="10%"><?php echo _('SKU' ); ?></th>
					<th width="10%"><?php echo _('Status'); ?></th>
					<th width="10%"><?php echo _('Created'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>