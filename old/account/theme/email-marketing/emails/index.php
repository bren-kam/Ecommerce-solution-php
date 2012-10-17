<?php
/**
 * @page List Email Messages
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['email_marketing'] )
	url::redirect('/email-marketing/subscribers/');

$selected = "email_marketing";
$title = _('Emails') . ' | ' . _('Email Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Messages'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'send_email', 'emails' ); ?>
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" width="100%" perPage="100,250,500" ajax="/ajax/email-marketing/emails/list/">
			<thead>
				<tr>
					<th width="50%"><?php echo _('Subject'); ?></th>
					<th width="20%"><?php echo _('Status'); ?></th>
					<th width="30%" sort="1 desc"><?php echo _('Date'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
