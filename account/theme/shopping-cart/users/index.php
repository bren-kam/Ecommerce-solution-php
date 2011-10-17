<?php
/**
 * @page Users
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$title = _('Shopping Cart') . ' | ' . TITLE;

$page = 'users';
get_header();
?>

<div id="content">
	<h1><?php echo _('Shopping Cart'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'users' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/shopping-cart/users/list/" perPage="25,50,100" sort="1" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="40%"><?php echo _('Email'); ?></th>
					<th width="20%"><?php echo _('First Name'); ?></th>
					<th width="20%"><?php echo _('Status'); ?></th>
					<th width="20%"><?php echo _('Date' ); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>