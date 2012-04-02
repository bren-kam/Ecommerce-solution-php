<?php
/**
 * @page List Website Pages
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$selected = "website";
$title = _('Website Pages') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Website Pages'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/website/list-pages/" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
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
