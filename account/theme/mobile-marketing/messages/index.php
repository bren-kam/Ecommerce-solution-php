<?php
/**
 * @page List Mobile Messages
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['mobile-marketing'] )
    url::redirect('/');

$selected = "mobile_marketing";
$title = _('Mobiles') . ' | ' . _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Mobile Messages'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/' ); ?>
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" width="100%" perPage="100,250,500" ajax="/ajax/mobile-marketing/mobiles/list/">
			<thead>
				<tr>
					<th width="50%"><?php echo _('Summary'); ?></th>
					<th width="20%"><?php echo _('Status'); ?></th>
					<th width="30%" sort="1 desc"><?php echo _('Date Posted'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
