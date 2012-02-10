<?php
/**
 * @page List Keywords
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['mobile_marketing'] )
    url::redirect('/');

$selected = "keywords";
$title = _('Keywords') . ' | ' . _('Mobile Marketing') . ' |' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Keywords'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'keywords' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/mobile-marketing/keywords/list/" perPage="20,50,100" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="50%" sort="1"><?php echo _('Name'); ?></th>
                    <th width="30%" sort="2"><?php echo _('Keyword'); ?></th>
					<th width="20%"><?php echo _('Date Started'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
