<?php
/**
 * @page View Checklist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure we have a checklist selected
if ( empty( $_GET['cid'] ) )
	url::redirect( '/checklists/' );

css( 'form', 'checklists/view' );
javascript( 'validator', 'jquery', 'jquery.ui', 'jquery.common', 'jquery.form', 'checklists/view' );

$c = new Checklists;
$checklist = $c->get( (int) $_GET['cid'] );
$items = $c->get_checklist_items( (int) $_GET['cid'] );
		
$selected = 'checklists';
$title = _('View Checklist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1 style="float:left;width:70%"><strong><?php echo $checklist['type'], _(' for '), $checklist['title']; ?></strong></h1>
	<h1 style="float:left;width:30%;text-align:right"><?php echo _('Download'); ?>: <a href="/checklists/download-excel/?cid=<?php echo $checklist['checklist_id']; ?>" title="<?php echo _('Download Excel'); ?>"><?php echo _('Excel'); ?></a></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'checklists/' ); ?> 
	
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><?php echo _('Start Date'); ?>:</td>
				<td><?php echo date( 'F jS, Y', $checklist['date_created'] ); ?></td>
			</tr>
			<tr>
				<td><?php echo _('End Date'); ?>:</td>
				<td><?php echo date( 'F jS, Y', $checklist['date_created'] + 2592000 ) ; ?></td>
			</tr>
			<tr>
				<td><?php echo _('Days Left'); ?>:</td>
				<td><?php echo $checklist['days_left']; ?></td>
		</table>
		<br />
		<div id="dList">
			<?php
				nonce::field('update-item' , '_ajax_update_item_nonce');
				if ( is_array( $items ) ) {
				foreach ( $items as $section_title => $section_items ) {
			?>
				<div><br /><h3><?php echo $section_title; ?></h3></div>
				<?php foreach ( $section_items as $item ) { ?>
				<div id="dItem<?php echo $item['checklist_website_item_id']; ?>" class="list-item<?php echo ( $item['checked'] ) ? ' done' : ''; ?>">
					<span class="sequence"><?php echo $item['sequence']; ?> . </span>
					<span class="check"> <input type="checkbox" class="item-checkbox" value="<?php echo $item['checklist_website_item_id']; ?>" id="cItem<?php echo $item['checklist_website_item_id']; ?>"<?php echo ( $item['checked'] ) ? ' checked="checked"' : ''; ?> /></span>
					<span id="tdItem<?php echo $item['checklist_website_item_id']; ?>"><strong><?php echo $item['name']; ?></strong> (<?php echo $item['assigned_to']; ?>) - <a href="javascript:;" title="<?php echo _('Notes'); ?>" class="note-link"><?php echo _('Notes'); ?></a> [ <span id="sNoteCount<?php echo $item['checklist_website_item_id']; ?>"><?php echo ( empty( $item['notes_count'] ) ) ? '0' : $item['notes_count']; ?></span> ]
					</span>
				</div>
			<?php 
				}
			}
			?>
		</div>
		<?php } else { ?>
			<h4><?php echo _('There are no items in the checklist.'); ?></h4>
		<?php } ?>
	</div>
	<br /><br />
</div>

<div id="dNotes" style="display: none;">
<?php 
nonce::field( 'get-notes', '_ajax_get_notes' );
nonce::field( 'delete-note', '_ajax_delete_note' );
nonce::field( 'update-note', '_ajax_update_note' );
nonce::field( 'update-item', '_ajax_update_item' );
?>
<div id="dNotesList"></div>
<br />
<form id="fNewNote" method="post" action="/ajax/checklists/view/add-note/">
	<?php nonce::field( 'add-note', '_ajax_add_note' ); ?>
	<input type="hidden" name="hItemId" id="hItemId" value="" />
	<textarea id="taNote" name="taNote" rows="3" cols="40" ></textarea><br />
	<input type="submit" class="button" id="bSendNote" value="Add Note" />
</form>
</div>

<?php get_footer(); ?>