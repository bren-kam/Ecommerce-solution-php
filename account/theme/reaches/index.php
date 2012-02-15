<?php
/**
 * @page Tickets
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

//$_SESSION['tickets']['status'] = '0';
//$_SESSION['tickets']['assigned-to'] = '0';

//css( 'reaches/index' );
//javascript( 'reaches/index' );

$selected = 'reaches';
$title = _('Reaches') . ' | ' . TITLE;
get_header();

?>
<div id="content">
	<h1><?php echo _('Reaches'); ?></h1>
	<br clear="all" /><br />
	<?php 
	nonce::field( 'change-status', '_ajax_change_status' );
	nonce::field( 'change-assigned-to', '_ajax_change_assigned_to' );
	?>
	<div id="dListTickets">

	<table ajax="/ajax/reaches/list/" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%" id="tListTickets">
		<thead>
			<tr>
				<th width="15%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="18%"><?php echo _('Assigned To'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="15%"><?php echo _('Priority'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
				<th width="8%"><?php echo _('Created'); ?><img src="/images/trans.gif" width="10" height="8" alt="" /></th>
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