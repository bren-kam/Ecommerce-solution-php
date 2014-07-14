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

<!-- Modal -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="modalLabel">Notes</h4>
        </div>
        <div class="modal-body">

            <div class="timeline">
                <?php
                    if ( is_array( $notes ) )
                        foreach( $notes as $note ):
                            $date = new DateTime( $note->date_created );
                ?>

                    <article class="timeline-item alt" data-note-id="<?php echo $note->id ?>">
                        <div class="timeline-desk">
                            <div class="panel">
                                <div class="panel-body">
                                    <span class="arrow"></span>
                                    <span class="timeline-icon green"></span>
                                    <span class="timeline-date"><?php echo $date->format( 'F j, Y g:ia' ) ?></span>
                                    <h1 class="green"><?php echo $note->user ?> | <a href="<?php echo url::add_query_arg( array( '_nonce' => $delete_nonce, 'cwinid' => $note->id ), '/checklists/delete-note/' ); ?>" class="delete-note" title="Delete this note"><i class="fa fa-trash-o"></i></a></h1>
                                    <p><?php echo $note->note ?></p>
                                </div>
                            </div>
                        </div>
                    </article>

                <?php endforeach; ?>
            </div>

            <form id="fNewNote" method="post" action="/checklists/add-note/" role="form">
                <div class="form-group">
                    <label for="note">Note:</label>
                    <textarea class="form-control" name="note" id="note" rows="3" cols="40" placeholder="Add a new Note"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="bSendNote">Add Note</button>
                <input type="hidden" name="hChecklistWebsiteItemId" id="hChecklistWebsiteItemId" value="<?php echo $checklist_website_item_id; ?>" />
                <?php nonce::field( 'add_note' ); ?>
            </form>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

