<?php
/**
 * @page List Email Lists
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

// Redirect to main section if they don't have email marketing
if( !$user['website']['email_marketing'] )
	url::redirect('/email-marketing/subscribers/');

$selected = "email_marketing";
$title = _('Email Lists') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Lists'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'email_lists' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/email-marketing/email-lists/list/" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="40%" sort="1"><?php echo _('Name'); ?></th>
					<th width="40%"><?php echo _('Description'); ?></th>
					<th width="20%"><?php echo _('Date Created'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>