<?php
/**
 * @page View Feedback
 * @package Real Statistics
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$f = new Feedback();
$fc = new Feedback_Comments();

$fb = $f->get( $_GET['fid'] );
$comments = $fc->get( $_GET['fid'] );

// Auto assign feedback
if ( 0 == $fb['assigned_to_user_id'] )
	$fb['assigned_to_user_id'] = $f->update_assigned_to( $_GET['fid'], $user['user_id'] );

css( 'form', 'view-feedback' );
javascript( 'validator', 'jquery', 'jquery.autoresize', 'jquery.tmp-val', 'view-feedback' );

$selected = 'feedback';
$title = _('View Feedback | Admin') . ' | ' . TITLE;
get_header();
?>

<div class="narrowcolumn">
	<h1>Feedback</h1>
	<input type="hidden" id="hFeedbackID" value="<?php echo $_GET['fid']; ?>" />
	<?php 
		nonce::field( 'update-feedback-status' );
		nonce::field( 'add-comment', '_ajax_add_comment' );
		nonce::field( 'delete-comment', '_ajax_delete_comment' );
		nonce::field( 'update-assigned-to', '_ajax_update_assigned_to_nonce' );
		nonce::field( 'update-priority', '_ajax_update_priority_nonce' );
	?>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><strong><?php echo _('Name'); ?>:</strong></td>
			<td><?php echo $fb['name']; ?></td>
			<td><strong><?php echo _('Browser'); ?>:</strong></td>
			<td><?php echo $fb['browser_name'], ' ', $fb['browser_version']; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Date'); ?>:</strong></td>
			<td><?php echo $fb['date_created']; ?></td>
			<td><strong><?php echo _('Operating System'); ?>:</strong></td>
			<td><?php echo $fb['browser_platform']; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Email'); ?>:</strong></td>
			<td><a href="mailto:<?php echo $fb['email']; ?>" title="Email <?php echo $fb['name']; ?>"><?php echo $fb['email']; ?></a></td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td><label for="sPriority"><?php echo _('Priority'); ?>:</label></td>
			<td>
				<select id="sPriority" class="dd" style="width: 150px">
				<?php
				$priorities = array( 
					0 => _('Low'),
					1 => _('Medium'),
					2 => _('High')
				);
				
				foreach ( $priorities as $pn => $p ) {
					$selected = ( $fb['priority'] == $pn ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $pn . '"' . $selected . '>' . $p . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>		<tr>
			<td><label for="sStatus"><?php echo _('Status'); ?>:</label></td>
			<td>
				<select id="sStatus" class="dd" style="width: 150px">
				<?php
				$statuses = array( 
					0 => _('Open'),
					1 => _('Closed')
				);
				
				foreach ( $statuses as $sn => $s ) {
					$selected = ( $fb['status'] == $sn ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $sn . '"' . $selected . '>' . $s . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="sAssignedTo"><?php echo _('Assigned To'); ?>:</label></td>
			<td>
				<select id="sAssignedTo" class="dd" style="width: 150px">
				<?php
				switch ( $u->admin_users() as $au ) {
					$selected = ( $fb['assigned_to_user_id'] == $au['user_id'] ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $au['user_id'] . '"' . $selected . '>' . $au['first_name'] . ' ' . $au['last_name'] . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
	</table>
	<br />
	<h2><?php echo _('Message'); ?></h2>
	<blockquote>
		<?php echo $fb['message']; ?>
	</blockquote>
	<br />
	
	<h2><?php echo _('Comments'); ?></h2>
	<div id="dFeedbackComments">
		<div class="shading"></div>
		<img src="http://manage.realstatistics.com/images/<?php echo ( empty( $user['picture'] ) ) ? 'icons/person.png' : 'users/' . $user['user_id'] . '/icon/' . $user['picture']; ?>" id="iFeedbackCommentsImage" width="60" height="60" alt="<?php echo $user['first_name'], ' ', $user['last_name']; ?>" />
		<div id="dTAFeedbackCommentsWrapper"><textarea id="taFeedbackComments" cols="5" rows="3"><?php echo _('Write a comment...'); ?></textarea></div>
		<a href="javascript:;" id="aAddComment" class="button" title="<?php echo _('Add Comment'); ?>"><?php echo _('Add Comment'); ?></a>
		<div id="dPrivate">
			<input type="checkbox" id="cbPrivate" value="1" /> Private
		</div>
		<br clear="all" />
		<div class="divider hidden" id="dFeedbackCommentsDivider"></div>
		<div id="dComments">
		<?php
		if ( is_array( $comments ) )
		foreach ( $comments as $c ) {
		?>
		<div class="comment" id="dComment<?php echo $c['feedback_comment_id']; ?>">
			<img src="http://manage.realstatistics.com/images/<?php echo ( empty( $c['picture'] ) ) ? 'icons/person.png' : 'users/' . $c['user_id'] . '/icon/' . $c['picture']; ?>" class="avatar" width="60" height="60" alt="<?php echo $c['name']; ?>" />
			<div class="comment-content">
				<p class="name">
					<?php echo $c['name']; ?>
					<a href="javascript:;" class="delete-comment" title="Delete Feedback Comment"><img src="/images/icons/x.png" alt="X" width="16" height="16" /></a>
				</p>
				<p><?php echo $c['comment']; ?></p>
				<p class="date"><?php echo dt::date( 'm/d/Y g:ia', $c['date'] ); ?></p>
			</div>
			<br clear="left" />
		</div>
		<?php } ?>
		</div>
	</div>
	<br clear="all" />
</div>

<?php get_footer(); ?>