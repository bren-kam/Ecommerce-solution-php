<?php
/**
 * @page List Email Subscribers
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$selected = "subscribers";
$title = _('Email Subscribers') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Subscribers'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'subscribers' ); ?>
	<div id="subcontent">
		<?php if ( !$user['website']['email_marketing'] ) { ?>
		<p class="warning"><?php echo _('You are only able to manage your subscribers. To have full use of the Email Marketing section you can sign up for it by calling our Online Specialists at (800) 549-9206.'); ?></p>
		<br /><br />
		<br /><br />	
		<?php } ?>
		<table ajax="/ajax/email-marketing/subscribers/list/?s=1<?php if ( isset( $_GET['elid'] ) ) echo '&elid=' . $_GET['elid']; ?>" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="30%" sort="1"><?php echo _('Email'); ?></th>
					<th width="30%"><?php echo _('Name'); ?></th>
					<th width="20%"><?php echo _('Phone'); ?></th>
					<th width="20%"><?php echo _('Date Signed Up'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
