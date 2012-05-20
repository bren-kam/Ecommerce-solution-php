<?php
/**
 * @page Mobile Marketing
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

// Get data
$selected = "mobile_marketing";
$title = _('Mobile Pages') . ' | ' . TITLE;
javascript( 'mammoth', '' );
get_header();
?>

<div id="content">
	<h1><?php echo _('Mobile Pages'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'website' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/mobile-marketing/website/list/" perPage="30,50,100" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="65%" sort="1"><?php echo _('Title'); ?></th>
					<th width="15%"><?php echo _('Status'); ?></th>
					<th width="20%"><?php echo _('Last Updated'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>