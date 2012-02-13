<?php
/**
 * @page View Ticket
 * @package Real Statistics
 */

// Get current user
global $user, $w;

// If user is not logged in
if ( !$user )
	login();

// Need to have something here
if ( empty( $_GET['rid'] ) )
	url::redirect('/reaches/');

$reaches = new Reaches();
$rc = new Reach_Comments();

$reach = $reaches->get( $_GET['rid'] );

// TODO integrate ACL stuff
// Don't want them to see this if they don't have the right role
//if ( $user['role'] < $ticket['role'] && $user['user_id'] != $ticket['user_id'] )
//	url::redirect( '/tickets/' );

$ru = $u->get_user( $reach['user_id'] );
$comments = $rc->get( $_GET['rid'] );

// Auto assign feedback
/*if ( 0 == $reach['assigned_to_user_id'] )
	$reach['assigned_to_user_id'] = $reach->update_assigned_to( $_GET['rid'], $user['user_id'] );
 */

//css( 'form', 'jquery.uploadify', 'tickets/ticket' );
javascript( 'mammoth' );

$assignable_users = $u->get_website_users( "AND b.`website_id` = {$user[website][website_id]} AND a.`status` <> 0 AND a.`status` = 1 AND '' <> a.`contact_name`" );

$selected = 'reaches';
$title = _('View Reach | Account') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Reach Detail'); ?></h1>
	<br clear="all" /><br />
	<input type="hidden" id="hTicketID" value="<?php echo $_GET['rid']; ?>" />
	<input type="hidden" id="hWebsiteID" value="<?php echo $reach['website_id']; ?>" />
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
	<div class="float-left">
		<p>
			<strong><?php echo _('Name'); ?>:</strong> <?php echo $reach['name']; ?>
		</p>
		<p>
			<strong><?php echo _('Website'); ?>:</strong> 
			<a href="http://<?php if ( !empty( $reach['subdomain'] ) ) echo $reach['subdomain'], '.'; echo $reach['domain']; ?>/" title="<?php echo $reach['website']; ?>" target="_blank"><?php echo $reach['website']; ?></a><?php if ( !empty( $reach['website_id'] ) ) { ?> <?php } ?>
		</p>
	</div>
	<div class="float-left">
		<p>
			<label for="sAssignedTo"><?php echo _('Assigned To'); ?>:</label>
			<select id="sAssignedTo" class="dd" style="width: 150px">
			<?php
				foreach ( $assignable_users as $au ) {
					$selected = ( $reach['assigned_to_user_id'] == $au['user_id'] ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $au['user_id'] . '"' . $selected . '>' . $au['contact_name'] . "</option>\n";
				}
			?>
			</select>
		</p>
		
		<p>
			<label for="sPriority"><?php echo _('Priority'); ?>:</label>
			<select id="sPriority" class="dd" style="width: 150px">
			<?php
				$priorities = array( 
					0 => _('Normal'),
					1 => _('High'),
					2 => _('Urgent')
				);
				
				foreach ( $priorities as $pn => $p ) {
					$selected = ( $reach['priority'] == $pn ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $pn . '"' . $selected . '>' . $p . "</option>\n";
				}
			?>
		</p>
		</select>
	</div>
	<div class="float-left">
	
		<label for="sStatus"><?php echo _('Status'); ?>:</label>
		<select id="sStatus" class="dd" style="width: 150px">
		<?php
			$statuses = array( 
				0 => _('Open'),
				1 => _('Closed')
			);
			
			foreach ( $statuses as $sn => $s ) {
				$selected = ( $reach['status'] == $sn ) ? ' selected="selected"' : '';
				
				echo '<option value="' . $sn . '"' . $selected . '>' . $s . "</option>\n";
			}
		?>
		</select>
	</div>
	<div class="clr"></div>
	
	
	<h2><?php echo _('Message'); ?></h2>
	<blockquote>
		<?php echo $reach['message']; ?>
	</blockquote>
	<div class="attachments">
	<?php
	if ( isset( $reach['attachments'] ) && is_array( $reach['attachments'] ) )
	foreach ( $reach['attachments'] as $ta ) {
	?>
	<a href="<?php echo $ta['link']; ?>" target="_blank" title="<?php echo _('Download'), ' ', $ta['name']; ?>"><?php echo $ta['name']; ?></a>
	<?php } ?>
	</div>
			
	<br /><hr />
	<div id="dTicketComments">
		<div class="shading"></div>
		<form action="/ajax/reaches/add-comment/" method="POST" ajax="1">
			<?php nonce::field( 'add-comment', '_nonce' ); ?>
			<input type="hidden" name="rid" value="<?php echo $reach['website_reach_id']; ?>" />
			<input type="h"
			<div id="dTATicketCommentsWrapper"><textarea id="taReachComment" name="taReachComment" cols="5" rows="3" tmpVal="<?php echo _('Write a comment...'); ?>"></textarea></div>
			<input type="submit" id="aAddComment" class="button" title="<?php echo _('Add Comment'); ?>" value="<?php echo _('Add Comment'); ?>" />
			<div id="dPrivate">
				<input type="checkbox" id="cbPrivate" name="cbPrivate" value="1" /> <label for="cbPrivate"><?php echo _('Private'); ?></label>
			</div>
		</form>
		
		<div class="divider" id="dTicketCommentsDivider"></div>
		<div id="dComments">
		<?php
		if ( is_array( $comments ) )
		foreach ( $comments as $c ) {
			if ( $user['user_id'] == $reach['user_id'] && '1' == $c['private'] )
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