<?php
/**
 * @page View Reach
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

// Get reach, with meta
$reach = $reaches->get( $_GET['rid'], true );
$reach_info = $reaches->_get_friendly_info( $reach['meta'] );
$reach_date = new DateTime( $reach['assigned_to_date'] );

// TODO integrate ACL stuff
// Don't want them to see this if they don't have the right role
//if ( $user['role'] < $reach['role'] && $user['user_id'] != $reach['user_id'] )
//	url::redirect( '/reachs/' );

$ru = $u->get_user( $reach['user_id'] );
$comments = $rc->get( $_GET['rid'] );

$assignable_users = $u->get_website_users( "AND b.`website_id` = {$user[website][website_id]} AND role >= 1 AND a.`status` <> 0 AND a.`status` = 1 AND '' <> a.`contact_name`" );
$assignable_users[] = $u->get_user( $user['website']['user_id'] );

css( 'reaches/reach' );
javascript( 'mammoth', 'reaches/reach' );


$selected = 'reaches';
$title = _('View Reach | Account') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $reaches->_get_friendly_type( $reach['meta']['type'] ) . _(' #') . $reach['website_reach_id']; ?></h1>
	<br clear="all" /><br />
	
	<?php get_sidebar( 'products/' ); ?>
	
	<div id="subcontent">
		<input type="hidden" id="hReachID" value="<?php echo $_GET['rid']; ?>" />
		<input type="hidden" id="hWebsiteID" value="<?php echo $reach['website_id']; ?>" />
		<input type="hidden" id="hUserID" value="<?php echo $user['user_id']; ?>" />
		<?php 
			nonce::field( 'update-status', '_ajax-update-status');
			nonce::field( 'update-assigned-to', '_ajax-update-assigned-to' );
			nonce::field( 'update-priority', '_ajax-update-priority' );
		?>
		<div class="reach-col float-left">
			<table>
				<tr>
					<td><strong><?php echo _('Name'); ?>:</strong></td>
					<td><?php echo $reach['name']; ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<p>
						<?php if( $reach_info ) foreach ( $reach_info as $key => $value ): ?>
							<ul>
								<li><?php echo $key; ?>: <?php echo $value; ?></li>
							</ul>
						<?php endforeach; ?>
						</p>
					</td>
				</tr>		
			</table>
		</div>
		<div class="reach-col float-left">
			<table>
				<tr>
					<td><label for="sAssignedTo"><?php echo _('Assigned To'); ?>:</label></td>
					<td>
						<select id="sAssignedTo" class="dd" style="width: 150px">
                            <option value="">-- <?php echo _('Assign a User'); ?>--</option>
                            <?php
                                foreach ( $assignable_users as $au ) {
                                    $selected = ( $reach['assigned_to_user_id'] == $au['user_id'] ) ? ' selected="selected"' : '';

                                    echo '<option value="' . $au['user_id'] . '"' . $selected . '>' . $au['contact_name'] . "</option>\n";
                                }
                            ?>
						</select>
                        <?php if ( '0000-00-00 00:00:00' != $reach['assigned_to_date'] ) echo $reach_date->format('F jS, Y'); ?>
					</td>
				</tr>
				<tr>
					<td><label for="sPriority"><?php echo _('Priority'); ?>:</label></td>
					<td>
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
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="sStatus"><?php echo _('Status'); ?>:</label></td>
					<td>
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
					</td>
				</tr>
			</table>
		</div>
		
		<div class="clr"></div>
		
		<h2><?php echo _('Message'); ?></h2>
		<blockquote>
			<?php echo $reach['message']; ?>
		</blockquote>
				
		<br /><hr />
		<div id="dReachComments">
			<div class="shading"></div>
			<form action="/ajax/reaches/add-comment/" method="POST" ajax="1">
				<?php nonce::field( 'add-comment', '_nonce' ); ?>
				<input type="hidden" name="rid" value="<?php echo $reach['website_reach_id']; ?>" />
				<div id="dTAReachCommentsWrapper"><textarea id="taReachComment" name="taReachComment" cols="5" rows="3" tmpVal="<?php echo _('Write a comment...'); ?>"></textarea></div>
				<input type="submit" id="aAddComment" class="button" title="<?php echo _('Add Comment'); ?>" value="<?php echo _('Add Comment'); ?>" />
				<div id="dPrivate">
					<input type="checkbox" id="cbPrivate" name="cbPrivate" value="1" /> <label for="cbPrivate"><?php echo _('Private'); ?></label>
				</div>
			</form>
			
			<div class="divider" id="dReachCommentsDivider"></div>
			<div id="dComments">
			<?php
			if ( is_array( $comments ) )
			foreach ( $comments as $c ) {
				if ( $user['user_id'] == $reach['user_id'] && '1' == $c['private'] )
					continue;
			?>
			<div class="comment" id="dComment<?php echo $c['website_reach_comment_id']; ?>">
				<p class="name">
					<?php if ( '1' == $c['private'] ) { ?>
					<img src="/images/icons/reaches/lock.gif" width="11" height="15"0 alt="<?php echo _('Private'); ?>" class="private" />
					<?php
					}
					
					echo $c['name'];
					?>
					<span class="date"><?php echo dt::date( 'm/d/Y g:ia', $c['date'] ); ?></span>
					<?php if ( $user['user_id'] == $c['user_id'] ): ?>
						<a ajax="1" href="/ajax/reaches/delete-comment/?_nonce=<?php echo nonce::create( 'delete-comment' ); ?>&rcid=<?php echo $c['website_reach_comment_id']; ?>" class="delete-comment" title="<?php echo _('Delete Feedback Comment'); ?>">
							<img src="/images/icons/x.png" alt="X" width="16" height="16" />
						</a>
					<?php endif; ?>
				</p>
				<p class="message"><?php echo $c['comment']; ?></p>
				<br clear="left" />
			</div>
			<?php } ?>
			</div>
		</div>
		<br clear="all" />
	</div>
	<br/><br/>
</div>

<?php get_footer(); ?>