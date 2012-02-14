<?php
/**
 * @page List Unsubscribed
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['mobile-marketing'] )
    url::redirect('/');

$selected = "subscribers";
$title = _('Mobile Lists') . ' | ' . _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Unsubscribed'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'subscribers' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/mobile-marketing/subscribers/list/?s=0<?php if ( isset( $_GET['mlid'] ) ) echo '&mlid=' . $_GET['mlid']; ?>" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="70%" sort="1"><?php echo _('Phone'); ?></th>
					<th width="30%"><?php echo _('Date Unsubscribed'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>