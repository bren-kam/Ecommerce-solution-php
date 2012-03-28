<?php
/**
 * @page Tickets
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$_SESSION['tickets']['status'] = '0';
$_SESSION['tickets']['assigned-to'] = '0';

css( 'tickets/list', 'data-tables/TableTools.css', 'data-tables/ui.css' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'tickets/list' );

$admin_users = $u->get_users( " AND `role` > 5 AND `status` = 1 AND '' <> `contact_name`" );

$selected = 'tickets';
$title = _('Tickets') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Tickets'); ?></h1>
	<br clear="all" /><br />
	<?php 
	nonce::field( 'change-status', '_ajax_change_status' );
	nonce::field( 'change-assigned-to', '_ajax_change_assigned_to' );
	?>
	<div id="dListTickets">
	<select id="sStatuses">
		<option value="0"><?php echo _('Open'); ?></option>
		<option value="1"><?php echo _('Closed'); ?></option>
	</select>
	<select id="sAssignedTo">
		<option value="0"><?php echo _('All'); ?></option>
		<option value="-1"><?php echo _('Peers'); ?></option>
		<?php foreach ( $admin_users as $au ) { ?>
		<option value="<?php echo $au['user_id']; ?>"><?php echo $au['contact_name']; ?></option>
		<?php } ?>
	</select>
	<table cellpadding="0" cellspacing="0" width="100%" id="tListTickets">
		<thead>
			<tr>
				<th width="26%"><?php echo _('Summary'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="15%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="18%"><?php echo _('Website'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="10%" class="center"><?php echo _('Priority'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="16%"><?php echo _('Assigned To'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
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