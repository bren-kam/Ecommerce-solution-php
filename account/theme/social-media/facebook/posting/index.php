<?php
/**
 * @page Facebook - Posting
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$selected = "social_media";
$title = _('Posting') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Posting'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/', 'posting' ); ?>
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" width="100%" perPage="25,50,100" ajax="/ajax/social-media/facebook/posting/list/">
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