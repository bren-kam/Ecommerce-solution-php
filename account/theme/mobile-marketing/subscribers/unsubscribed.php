<?php
/**
 * @page List Unsubscribed
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$selected = "subscribers";
$title = _('Email Lists') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Lists'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'subscribers' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/email-marketing/subscribers/list/?s=0<?php if ( isset( $_GET['elid'] ) ) echo '&elid=' . $_GET['elid']; ?>" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="30%" sort="1"><?php echo _('Email'); ?></th>
					<th width="30%"><?php echo _('Name'); ?></th>
					<th width="20%"><?php echo _('Phone'); ?></th>
					<th width="20%"><?php echo _('Date Unsubscribed'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>