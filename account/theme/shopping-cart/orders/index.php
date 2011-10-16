<?php
/**
 * @page Orders
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$title = _('Shopping Cart - List Orders') . ' | ' . TITLE;

$page = 'orders';
get_header();
?>

<div id="content">
	<h1><?php echo _('Orders'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/shopping-cart/orders/list/" perPage="50,100,200" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="15%" sort="1 desc"><?php echo _('Order Number'); ?></th>
					<th width="30%"><?php echo _('Price'); ?></th>
					<th width="30%"><?php echo _('Status'); ?></th>
					<th width="25%"><?php echo _('Date'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>