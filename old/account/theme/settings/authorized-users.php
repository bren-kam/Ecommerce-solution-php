<?php
/**
 * @page Settings
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Can't be here if they are an authorized user
if ( 1 == $user['role'] )
	urll::redirect('/settings/');

$title = _('Authorized Users') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Authorized Users'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'settings/', 'authorized_users' ); ?>
	<div id="subcontent">
		<br />
		<table ajax="/ajax/settings/list-authorized-users/" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="40%" sort="1"><?php echo _('Email'); ?></th>
					<th width="10%"><?php echo _('Pages'); ?></th>
					<th width="10%"><?php echo _('Products'); ?></th>
					<th width="10%"><?php echo _('Analytics' ); ?></th>
					<th width="10%"><?php echo _('Blog'); ?></th>
					<th width="10%"><?php echo _('Email Marketing'); ?></th>
					<th width="10%"><?php echo _('Shopping Cart'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>