<?php
/**
 * @page Product Catalog > Coupons
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$title = _('Coupons') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Coupons'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'coupons' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/products/coupons/list/" perPage="30,50,100" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="40%"><?php echo _('Name'); ?></th>
					<th width="13%"><?php echo _('Amount'); ?></th>
					<th width="13%"><?php echo _('Type'); ?></th>
					<th width="14%"><?php echo _('Item Limit'); ?></th>
					<th width="20%" sort="1"><?php echo _('Date Created'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>