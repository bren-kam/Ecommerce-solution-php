<?php
/**
 * @page View Ticket
 * @package Real Statistics
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Need to have something here
if ( empty( $_GET['tid'] ) )
	url::redirect('/tickets/');

$tickets = new Tickets();
$tc = new Ticket_Comments();

$ticket = $tickets->get( $_GET['tid'] );

// Don't want them to see this if they don't have the right role
if ( $user['role'] < $ticket['role'] && $user['user_id'] != $ticket['user_id'] )
	url::redirect( '/tickets/' );

$tu = $u->get_user( $ticket['user_id'] );
$comments = $tc->get( $_GET['tid'] );

// Auto assign feedback
if ( 0 == $ticket['assigned_to_user_id'] )
	$ticket['assigned_to_user_id'] = $tickets->update_assigned_to( $_GET['tid'], $user['user_id'] );

css( 'form', 'jquery.uploadify', 'tickets/ticket' );
javascript( 'swfobject', 'validator', 'jquery', 'jquery.uploadify', 'jquery.autoresize', 'jquery.tmp-val', 'tickets/ticket' );

$admin_users = $u->get_users( "AND `status` <> 0 AND `role` > 5 AND `status` = 1 AND '' <> `contact_name`" );

$selected = 'tickets';
$title = _('View Ticket | Admin') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $ticket['summary']; ?></h1>
	<br clear="all" /><br />
	<input type="hidden" id="hTicketID" value="<?php echo $_GET['tid']; ?>" />
	<input type="hidden" id="hWebsiteID" value="<?php echo $ticket['website_id']; ?>" />
	<input type="hidden" id="hUserID" value="<?php echo $user['user_id']; ?>" />
	<?php 
		nonce::field( 'update-ticket-status' );
		nonce::field( 'add-comment', '_ajax_add_comment' );
		nonce::field( 'delete-comment', '_ajax_delete_comment' );
		nonce::field( 'update-assigned-to', '_ajax_update_assigned_to' );
		nonce::field( 'update-priority', '_ajax_update_priority' );
		nonce::field( 'update-date-due', '_ajax_update_date_due' );
		nonce::field( 'upload-attachment', '_ajax_upload_attachment' );
		nonce::field( 'remove-attachment', '_ajax_remove_attachment' );
	?>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><strong><?php echo _('Name'); ?>:</strong> <?php echo $ticket['name']; ?></td>
			<td><strong><?php echo _('Browser'); ?>:</strong> <?php echo $ticket['browser_name'], ' ', $ticket['browser_version']; ?></td>
			<td class="move">
				<label for="sAssignedTo"><?php echo _('Assigned To'); ?>:</label>
				<select id="sAssignedTo" class="dd" style="width: 150px">
				<?php
				foreach ( $admin_users as $au ) {
					$selected = ( $ticket['assigned_to_user_id'] == $au['user_id'] ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $au['user_id'] . '"' . $selected . '>' . $au['contact_name'] . "</option>\n";
				}
				?>
				</select>
			</td>
			<td class="move">
				<label for="sStatus"><?php echo _('Status'); ?>:</label>
				<select id="sStatus" class="dd" style="width: 150px">
				<?php
				$statuses = array( 
					0 => _('Open'),
					1 => _('Closed')
				);
				
				foreach ( $statuses as $sn => $s ) {
					$selected = ( $ticket['status'] == $sn ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $sn . '"' . $selected . '>' . $s . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo _('Website'); ?>:</strong> <a href="http://<?php if ( !empty( $ticket['subdomain'] ) ) echo $ticket['subdomain'], '.'; echo $ticket['domain']; ?>/" title="<?php echo $ticket['website']; ?>" target="_blank"><?php echo $ticket['website']; ?></a><?php if ( !empty( $ticket['website_id'] ) ) { ?> (<a href="/accounts/control/?wid=<?php echo $ticket['website_id']; ?>" target="_blank" title="<?php echo _('Control'); ?>"><?php echo _('Control'); ?></a><?php if ( 10 == $user['role'] ) { ?> | <a href="/accounts/edit/?wid=<?php echo $ticket['website_id']; ?>" target="_blank" title="<?php echo _('Edit'); ?>"><?php echo _('Edit'); ?></a><?php } ?>)<?php } ?></td>
			<td><strong><?php echo _('OS'); ?>:</strong> <?php echo $ticket['browser_platform']; ?></td>
			<td class="move">
				<label for="sPriority"><?php echo _('Priority'); ?>:</label>
				<select id="sPriority" class="dd" style="width: 150px">
				<?php
				$priorities = array( 
					0 => _('Normal'),
					1 => _('High'),
					2 => _('Urgent')
				);
				
				foreach ( $priorities as $pn => $p ) {
					$selected = ( $ticket['priority'] == $pn ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $pn . '"' . $selected . '>' . $p . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo _('Date'); ?>:</strong><?php echo dt::date( 'm-d-Y', $ticket['date_created'] ); ?></td>
			<td>&nbsp;</td>
			<td class="move">
				<label for="tDateDue"><?php echo _('Due'); ?>:</label>
				<input type="text" id="tDateDue" class="tb" value="<?php echo ( empty( $ticket['date_due'] ) || 0 == $ticket['date_due'] ) ? '' : dt::date('m-d-Y', $ticket['date_due']); ?>" />
			</td>
		</tr>
	</table>
	<br /><br />
	<h2><?php echo _('Message'); ?></h2>
	<blockquote>
		<?php echo $ticket['message']; ?>
	</blockquote>
	<div class="attachments">
	<?php
	if ( isset( $ticket['attachments'] ) && is_array( $ticket['attachments'] ) )
	foreach ( $ticket['attachments'] as $ta ) {
	?>
	<a href="<?php echo $ta['link']; ?>" target="_blank" title="<?php echo _('Download'), ' ', $ta['name']; ?>"><?php echo $ta['name']; ?></a>
	<?php } ?>
	</div>
			
	<br /><hr />
	<div id="dTicketComments">
		<div class="shading"></div>
		<div id="dTATicketCommentsWrapper"><textarea id="taTicketComments" cols="5" rows="3"><?php echo _('Write a comment...'); ?></textarea></div>
		<a href="javascript:;" id="aAddComment" class="button" title="<?php echo _('Add Comment'); ?>"><?php echo _('Add Comment'); ?></a>
		<div id="dPrivate">
			<input type="checkbox" id="cbPrivate" value="1" /> <label for="cbPrivate"><?php echo _('Private'); ?></label>
		</div>
		<input type="file" name="fUploadAttachment" id="fUploadAttachment" />
		<br clear="all" />
		<div id="attachments"></div>
		<div class="divider" id="dTicketCommentsDivider"></div>
		<div id="dComments">
		<?php
		if ( is_array( $comments ) )
		foreach ( $comments as $c ) {
			if ( $user['user_id'] == $ticket['user_id'] && '1' == $c['private'] )
				continue;
		?>
		<div class="comment" id="dComment<?php echo $c['ticket_comment_id']; ?>">
			<p class="name">
				<?php if ( '1' == $c['private'] ) { ?>
				<img src="/images/icons/tickets/lock.gif" width="11" height="15"0 alt="<?php echo _('Private'); ?>" class="private" />
				<?php
				}
				
				echo $c['name'];
				?>
				<span class="date"><?php echo dt::date( 'm/d/Y g:ia', $c['date'] ); ?></span>
				
				<a href="javascript:;" class="delete-comment" title="<?php echo _('Delete Feedback Comment'); ?>"><img src="/images/icons/x.png" alt="X" width="16" height="16" /></a>
			</p>
			<p class="message"><?php echo $c['comment']; ?></p>
			<div class="attachments">
			<?php
			if ( is_array( $c['attachments'] ) )
			foreach ( $c['attachments'] as $ca ) {
			?>
			<a href="<?php echo $ca['link']; ?>" target="_blank" title="<?php echo _('Download'), ' ', $ca['name']; ?>"><?php echo $ca['name']; ?></a>
			<?php } ?>
			</div>
			<br clear="left" />
		</div>
		<?php } ?>
		</div>
	</div>
	<br clear="all" />
</div>

<?php get_footer(); ?>