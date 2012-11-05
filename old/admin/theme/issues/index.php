<?php
/**
 * @page Issues
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

if ( $user['role'] < 10 )
	url::redirect( '/' );
	
$_SESSION['issues']['status'] = '0';

css( 'issues/list', 'data-tables/TableTools.css', 'data-tables/ui.css' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'issues/list' );

$selected = 'issues';
$title = _('Issues') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Issues'); ?></h1>
	<br clear="all" /><br />
	<?php nonce::field( 'change-status', '_ajax_change_status' ); ?>
	<div id="dListIssues">
	<select id="sStatuses">
		<option value="0"><?php echo _('Open'); ?></option>
		<option value="1"><?php echo _('Closed'); ?></option>
	</select>
	<table cellpadding="0" cellspacing="0" width="100%" id="tListIssues">
		<thead>
			<tr>
				<th width="65%"><?php echo _('Messages'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="10%"><?php echo _('Occurences'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="10%"><?php echo _('Priority'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="15%"><?php echo _('Created'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
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