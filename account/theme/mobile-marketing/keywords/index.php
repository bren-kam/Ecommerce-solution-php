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

$m = new Mobile_Marketing();

list( $used_keywords, $total_keywords ) = $m->get_keyword_usage();

$selected = "keywords";
$title = _('Keywords') . ' | ' . _('Mobile Marketing') . ' |' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Keywords'), " ($used_keywords/$total_keywords)"; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'keywords' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/mobile-marketing/keywords/list/" perPage="20,50,100" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
                    <th width="20%" sort="1"><?php echo _('Keyword'); ?></th>
                    <th width="60%"><?php echo _('Response'); ?></th>
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
