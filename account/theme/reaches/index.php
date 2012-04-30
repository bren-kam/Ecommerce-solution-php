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

$selected = 'reaches';
$title = _('Reaches') . ' | ' . TITLE;
get_header();

?>
<div id="content">
	<h1><?php echo _('Reaches'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/' ); ?>
	
	<div id="subcontent">
		<?php 
		nonce::field( 'change-status', '_ajax_change_status' );
		nonce::field( 'change-assigned-to', '_ajax_change_assigned_to' );
		?>
		<div id="dListTickets">
	
			<table ajax="/ajax/reaches/list/" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%" id="tListTickets">
				<thead>
					<tr>
						<th width="15%"><?php echo _('Name'); ?></th>
						<th width="18%"><?php echo _('Assigned To'); ?></th>
						<th width="15%"><?php echo _('Status'); ?></th>
						<th width="15%"><?php echo _('Priority'); ?></th>
						<th width="8%"><?php echo _('Created'); ?></th>
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
</div>

<?php get_footer(); ?>