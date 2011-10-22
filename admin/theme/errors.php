<?php
/**
 * @page Errors
 * @package Real Statistics
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'errors', 'data-tables/TableTools.css', 'data-tables/ui.css', 'jquery.ui' );
javascript( 'jquery',  'jquery.tmp-val', 'jquery.common', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'jquery.ui', 'errors' );

$selected = 'errors';
$title = _('Errors | Admin') . ' | ' . TITLE;
get_header();
?>

<div class="narrowcolumn">
	<h1><?php echo _('Errors'); ?></h1>
	<table cellpadding="0" cellspacing="0" border="0" id="tGraphValues" class="data-table">
		<thead>
			<tr>
				<th width="15%"><?php echo _('Date'); ?></th>
				<th width="25%"><?php echo _('Value'); ?></th>
				<th width="25%">% <?php echo _('Change'); ?></th>
				<th width="25%"><?php echo _('Sum'); ?></th>
				<th width="10%"><?php echo _('Remove'); ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<?php get_footer(); ?>