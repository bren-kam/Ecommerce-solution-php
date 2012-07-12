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

$comment_user_ids = array();

// @Fix should probably only be done once
foreach ( $comments as $c ) {
    $comment_user_ids[] = $c['user_id'];
}

$comment_user_ids = array_unique( $comment_user_ids );

// Auto assign feedback
if ( 0 == $ticket['assigned_to_user_id'] )
	$ticket['assigned_to_user_id'] = $tickets->update_assigned_to( $_GET['tid'], $user['user_id'] );

css( 'form', 'jquery.uploadify', 'tickets/ticket' );
javascript( 'swfobject', 'validator', 'jquery', 'jquery.uploadify', 'jquery.autoresize', 'jquery.tmp-val', 'tickets/ticket' );

$admin_users = $u->admin_users( $comment_user_ids );

// The Admin User Options
$admin_user_options = '';
$admin_user_ids = array();

foreach ( $admin_users as $au ) {
    $selected = ( $ticket['assigned_to_user_id'] == $au['user_id'] ) ? ' selected="selected"' : '';

    $admin_user_options .= '<option value="' . $au['user_id'] . '"' . $selected . '>' . $au['contact_name'] . "</option>\n";

    $admin_user_ids[] = $au['user_id'];
}

// Find out if the user is an admin user
$user_is_admin = in_array( $ticket['user_id'], $admin_user_ids );

$selected = 'tickets';
$title = _('View Ticket') . ' | ' . _('Tickets') . ' | ' . TITLE;
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
		nonce::field( 'upload-attachment', '_ajax_upload_attachment' );
		nonce::field( 'remove-attachment', '_ajax_remove_attachment' );
	?>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><strong><?php echo _('Name'); ?>:</strong> <?php if ( $user_is_admin ) { ?><a href="javascript:;" class="assign-to" rel="<?php echo $ticket['user_id']; ?>"><?php } echo $ticket['name']; if ( $user_is_admin ) { ?></a><?php } ?></td>
			<td><strong><?php echo _('Browser'); ?>:</strong> <?php echo $ticket['browser_name'], ' ', $ticket['browser_version']; ?></td>
			<td class="move">
				<label for="sAssignedTo"><?php echo _('Assigned To'); ?>:</label>
				<select id="sAssignedTo" class="dd" style="width: 150px">
				<?php echo $admin_user_options; ?>
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

                if ( in_array( $c['user_id'], $admin_user_ids ) )
                    echo '<a href="javascript:;" class="assign-to" rel="', $c['user_id'], '">';

				echo $c['name'];

                if ( in_array( $c['user_id'], $admin_user_ids ) )
                    echo '</a>';
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

<?php
if ( !empty( $ticket['website_id'] ) && $user['role'] >= 7 ) {
    $w = new Websites;

    $website = $w->get_website( $ticket['website_id'] );
    if ( 1 != $website['live'] ) {
        $c = new Checklists;
        $checklist_items = $c->get_checklist_items_by_website( $ticket['website_id'] );
        ?>
        <div id="checklist"><a href="javascript:;" id="aChecklist" title="<?php echo _('Checklist'); ?>"><img src="/images/trans.gif" width="26" height="100" alt="<?php echo _('Checklist'); ?>" /></a></div>
        <div id="dChecklistPopup" class="hidden" title="<?php echo _('Update Checklist'); ?>">
            <form action="/ajax/checklists/update/" id="fUpdateChecklist" method="post">
                <p><?php echo _('Select the checklist items you want to mark as complete.'); ?></p>
                <br />
                <select name="sChecklistItems[]" id="sChecklistItems" multiple="multiple" title="<?php echo _('Hint: Hit Ctrl + Click to select multiple items'); ?>">
                    <?php
                    if( is_array( $checklist_items ) )
                    foreach ( $checklist_items as $section => $item_array ) {
                        $options = '';

                        if ( is_array( $item_array ) )
                        foreach( $item_array as $item ) {
                            // We don't want to see checked items
                            if ( 1 == $item['checked'] )
                                continue;

                            $options .= '<option value="' . $item['checklist_website_item_id'] . '">' . $item['name'] . '</option>';
                        }

                        if ( !empty( $options ) )
                            echo '<optgroup label="', $section, '">', $options, '</optgroup>';
                    }
                    ?>
                </select>
                <br /><br />
                <?php nonce::field( 'update-checklist', '_ajax_update_checklist' ); ?>
                <input type="hidden" name="hTicketID" value="<?php echo $_GET['tid']; ?>" />
                <input type="hidden" name="hWebsiteID" value="<?php echo $ticket['website_id']; ?>" />
                <input type="submit" class="button" value="<?php echo _('Update Checklist'); ?>" />
            </form>
        </div>
        <?php
    }
}
?>

<?php get_footer(); ?>