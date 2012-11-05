<?php
/**
 * @page Companies - List
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure they have permission
if ( $user['role'] < 8 )
    url::redirect('/');

css( 'companies/list', 'data-tables/TableTools.css', 'data-tables/ui.css' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'companies/list' );

$selected = 'companies';
$title = _('Companies') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Companies'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'accounts/', 'companies' ); ?>
	<div id="subcontent">
	<table cellpadding="0" cellspacing="0" width="100%" id="tListCompanies">
		<thead>
			<tr>
				<th width="50%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="30%"><?php echo _('Domain'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="20%"><?php echo _('Created'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	</div>
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php get_footer(); ?>