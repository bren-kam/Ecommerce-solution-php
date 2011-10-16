<?php
/**
 * @page Feedback
 * @package Real Statistics
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

$_SESSION['status'] = '0';

css( 'feedback', 'data-tables/TableTools.css', 'data-tables/ui.css', 'jquery.ui' );
javascript( 'jquery', 'jquery.common', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'jquery.ui', 'feedback' );

$selected = 'feedback';
$title = _('Feedback | Admin') . ' | ' . TITLE;
get_header();
?>

<div class="narrowcolumn" style="position: relative">
	<h1><?php echo _('Feedback'); ?></h1>
	<?php nonce::field( 'change-status', '_ajax_change_status' ); ?>
	<div id="dStatuses">
		<input type="radio" class="rb status" name="rbStatus" id="rbStatus1" value="" /> <label for="rbStatus1"><?php echo _('All'); ?></label> 
		<input type="radio" class="rb status" name="rbStatus" id="rbStatus2" value="0" checked="checked" /> <label for="rbStatus2"><?php echo _('Open'); ?></label> 
		<input type="radio" class="rb status" name="rbStatus" id="rbStatus3" value="1" /> <label for="rbStatus3"><?php echo _('Closed'); ?></label>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" id="tFeedback" class="data-table">
		<thead>
			<tr>
				<th width="15%"><?php echo _('Name'); ?></th>
				<th width="30%"><?php echo _('Message'); ?></th>
				<th width="10%"><?php echo _('Priority'); ?></th>
				<th width="15%"><?php echo _('Status'); ?></th>
				<th width="15%"><?php echo _('Assigned To'); ?></th>
				<th width="15%"><?php echo _('Date Created'); ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<?php get_footer(); ?>