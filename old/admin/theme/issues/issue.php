<?php
/**
 * @page View Issue
 * @package Real Statistics
 */

error_reporting( E_ALL );
// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Don't want them to see this if they don't have the right role
if ( $user['role'] < 10 )
	url::redirect( '/' );

// Need to have something here
if ( empty( $_GET['ik'] ) )
	url::redirect('/issues/');

$i = new Issues();

$issue = $i->get( $_GET['ik'] );
$errors = $i->get_errors( $_GET['ik'] );
$comments = $i->get_comments( $_GET['ik'] );

css( 'form', 'jquery.uploadify', 'issues/issue' );
javascript( 'jquery', 'jquery.autoresize', 'jquery.tmp-val', 'issues/issue' );

// Recusrive function to show args
function show_args( $args, $html = '', $depth = 0 ) {
	if ( !is_array( $args ) )
		return $html;
	
	// Make sure we know the count
	$i = 0;
	
	foreach ( $args as $k => $v ) {
		if ( 0 != $i )
			$html .= ', ';
							
		if ( is_object( $v ) )
			$v = get_class( $v );
		
		if ( is_array( $v ) ) {
			$html .= 'array(<br /><blockquote>';
																			
			$html .= show_args( $v, $html, $depth + 4 );
			
			$html .= '</blockquote><br />)';
		} else {
			$html .= ( is_string( $v ) ) ? "'$v'" : $v;
		}
		
		$i++;
	}
	
	return $html;
}

$selected = 'issues';
$title = _('View Issue | Admin') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h2><?php echo _('Message'); ?></h2>
	<p><?php echo $issue['message']; ?></p>
	<br clear="all" /><br />
	<input type="hidden" id="hIssueKey" value="<?php echo $_GET['ik']; ?>" />
	<?php 
		nonce::field( 'update-issue-status' );
		nonce::field( 'add-comment', '_ajax_add_comment' );
		nonce::field( 'delete-comment', '_ajax_delete_comment' );
	?>
	<table width="100%">
		<tr>
			<td width="120"><strong><?php echo _('File'); ?>:</strong></td>
			<td><?php echo $issue['file']; ?></td>
			<td align="right">
				<label for="sStatus"><?php echo _('Status'); ?>:</label>
				<select id="sStatus" class="dd" style="width: 150px">
				<?php
				$statuses = array( 
					0 => _('Open')
					, 1 => _('Closed')
				);
				
				foreach ( $statuses as $sn => $s ) {
					$selected = ( $issue['status'] == $sn ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $sn . '"' . $selected . '>' . $s . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo _('Line'); ?>:</strong></td>
			<td><?php echo $issue['line']; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Date'); ?>:</strong></td>
			<td><?php echo dt::date( 'm-d-Y', $issue['date_created'] ); ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Priority'); ?>:</strong></td>
			<td>
				<?php 
				switch ( $issue['priority'] ) {
					case 1:
						echo '<span class="low">Low</span>';
					break;
					
					case 2:
						echo '<span class="normal">Normal</span>';
					break;
					
					case 3:
						echo '<span class="high">High</span>';
					break;
				}
				?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo _('Warning Type'); ?>:</strong></td>
			<td>
				<?php 
				switch ( $issue['number'] ) {
					case E_ERROR:
						echo 'E_ERROR';
					break;
					
					case E_WARNING:
						echo 'E_WARNING';
					break;
					
					case E_PARSE:
						echo 'E_PARSE';
					break;
					
					case E_NOTICE:
						echo 'E_NOTICE';
					break;
					
					case E_CORE_ERROR:
						echo 'E_CORE_ERROR';
					break;
					
					case E_CORE_WARNING:
						echo 'E_CORE_WARNING';
					break;
					
					case E_COMPILE_ERROR:
						echo 'E_COMPILE_ERROR';
					break;
					
					case E_COMPILE_WARNING:
						echo 'E_COMPILE_WARNING';
					break;
					
					case E_USER_ERROR:
						echo 'E_USER_ERROR';
					break;
					
					case E_USER_WARNING:
						echo 'E_USER_WARNING';
					break;
					
					case E_USER_NOTICE:
						echo 'E_USER_NOTICE';
					break;
					
					case E_STRICT:
						echo 'E_STRICT';
					break;
					
					case E_RECOVERABLE_ERROR:
						echo 'E_RECOVERABLE_ERROR';
					break;
					
					case E_DEPRECATED:
						echo 'E_DEPRECATED';
					break;
					
					case E_USER_DEPRECATED:
						echo 'E_USER_DEPRECATED';
					break;
					
					default:
						echo $issue['number'];
					break;
				}
				?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo _('Occurrences'); ?>:</strong></td>
			<td><?php echo $issue['occurrences']; ?></td>
		</tr>
	</table>
	<br /><br />
	<?php /*
	<h2>Context (Variables)</h2>
	
	
	echo $issue['context'];
	return;
	$context = unserialize( $issue['context'] );
	
	if ( $context ) {
		?>
		<table width="100%">
			<?php foreach ( $context as $k => $v ) { ?>
				<tr>
					<td width="50"><strong><?php echo $k; ?>:</strong></td>
					<td><?php echo ( is_object( $v ) ) ? get_class( $v ) : $v; ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
	<br /><br />
	*/
	?>
	
	<h2>Backtrace</h2>
	<?php 
	error_reporting( E_ALL );
	$backtrace = unserialize( base64_decode( $issue['backtrace'] ) );
	
	if ( $backtrace ) {
		?>
		<ol>
			<?php foreach ( $backtrace as $b ) { ?>
				<li>
					<p><?php echo $b['file']; ?> <strong>#<?php echo $b['line']; ?></strong></p>
					<?php if ( isset( $b['class'] ) ) { ?>
					<p>
						<?php echo $b['class'], $b['type'], $b['function'], '( ', show_args( $b['args'] ), ' )';
							
							?>
					</p>
					<?php } ?>
				</li>
			<?php } ?>
		</ol>
	<?php } ?>
	<br /><br />
	
	<h2>Errors</h2>
	<table width="100%">
		<thead>
			<tr>
				<th>User</th>
				<th>Website</th>
				<th>SQL</th>
				<th>SQL Error</th>
				<th>Page</th>
				<th>Referer</th>
				<th>Browser</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			if ( is_array( $errors ) )
			foreach ( $errors as $e ) {
				$date = new DateTime();
				$date->setTimestamp( $e['date_created'] );
			?>
				<tr>
					<td><?php echo $e['user']; ?></td>
					<td><?php echo $e['website']; ?></td>
					<td><?php echo $e['sql']; ?></td>
					<td><?php echo $e['sql_error']; ?></td>
					<td><?php echo $e['page']; ?></td>
					<td><?php echo $e['referer']; ?></td>
					<td><?php echo $e['browser']; ?></td>
					<td><?php echo $date->format( 'Y-m-d H:i:s' ); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<br />
	
	<br /><hr />
	<div id="dIssueComments">
		<div class="shading"></div>
		<div id="dTAIssueCommentsWrapper"><textarea id="taIssueComments" cols="5" rows="3"><?php echo _('Write a comment...'); ?></textarea></div>
		<a href="javascript:;" id="aAddComment" class="button" title="<?php echo _('Add Comment'); ?>"><?php echo _('Add Comment'); ?></a>
		<br clear="all" />
		<div class="divider" id="dIssueCommentsDivider"></div>
		<div id="dComments">
		<?php
		if ( is_array( $comments ) )
		foreach ( $comments as $c ) {
		?>
		<div class="comment" id="dComment<?php echo $c['issue_comment_id']; ?>">
			<p class="name">
				<?php echo $c['name']; ?>
				<span class="date"><?php echo dt::date( 'm/d/Y g:ia', $c['date'] ); ?></span>
				
				<a href="javascript:;" class="delete-comment" title="<?php echo _('Delete Issue Comment'); ?>"><img src="/images/icons/x.png" alt="X" width="16" height="16" /></a>
			</p>
			<p class="message"><?php echo $c['comment']; ?></p>
			<br clear="left" />
		</div>
		<?php } ?>
		</div>
	</div>
	<br clear="all" />
</div>

<?php get_footer(); ?>