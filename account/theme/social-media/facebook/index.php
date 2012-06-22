<?php
/**
 * @page Social Media - Facebook - Email Sign Up
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['social_media'] )
    url::redirect('/');

$selected = "social_media";
$title = _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Facebook'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/', 'facebook_pages' ); ?>
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" width="100%" perPage="25,50,100" ajax="/ajax/social-media/facebook/list-pages/">
			<thead>
				<tr>
					<th width="70%" sort="1 asc"><?php echo _('Name'); ?></th>
					<th width="30%"><?php echo _('Date Posted'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>