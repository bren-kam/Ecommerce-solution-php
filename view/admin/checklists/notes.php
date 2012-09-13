<?php
/**
 * @package Grey Suit Retail
 * @page Display Checklist Notes
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var int $checklist_website_item_id
 * @var array $notes
 */

$confirmation = _('Are you sure you want to delete this note? This cannot be undone.');
$delete_nonce = nonce::create('delete_note');
?>
<div id="notes">
    <?php
    /**
     * @var ChecklistWebsiteItemNote $note
     */
    if ( is_array( $notes ) )
    foreach( $notes as $note ) {
        $date = new DateTime( $note->date_created );
        ?>
        <div id="note-<?php echo $note->id; ?>" class="note">
            <div class="title">
                <strong><?php echo $note->user; ?></strong>
                <br /><?php echo $date->format( 'F j, Y g:ia' ); ?>
                <br />
                <a href="<?php echo url::add_query_arg( array( '_nonce' => $delete_nonce, 'cwinid' => $note->id ), '/checklists/delete-note/' ); ?>" class="delete-note" title="<?php echo _('Delete'); ?>" ajax="1" confirm="<?php echo $confirmation; ?>"><?php echo _('Delete'); ?></a>
            </div>
            <div class="note-note"><?php echo $note->note; ?></div>
        </div>
    <?php } ?>
</div>
<br />
<form id="fNewNote" method="post" action="/checklists/add-note/" ajax="1">
	<textarea name="note" id="note" rows="3" cols="40"></textarea>
    <br />
	<input type="submit" class="button" id="bSendNote" value="<?php echo _('Add Note'); ?>" />
    <input type="hidden" name="hChecklistWebsiteItemId" id="hChecklistWebsiteItemId" value="<?php echo $checklist_website_item_id; ?>" />
	<?php nonce::field( 'add_note' ); ?>
</form>
<div class="boxy-footer hidden">
    <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
</div>