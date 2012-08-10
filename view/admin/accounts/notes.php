<?php
/**
 * @package Grey Suit Retail
 * @page Notes for an account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var array $notes
 * @var Validator $v
 *
 */
echo $template->start( $account->title . ' ' . _('Notes') );
?>
<form name="fAddNote" method="post" action="/accounts/notes/?aid=<?php echo $_GET['aid']; ?>">
    <div style="padding-right: 18px;"><textarea name="taNote" id="taNote" cols="50" rows="3" class="col-1"></textarea></div>
    <br />
    <p class="float-right"><input type="submit" class="button" value="<?php echo _('Add Note'); ?>" /></p>
    <br clear="right" />
    <?php nonce::field( 'notes' ); ?>
</form>

<?php echo $v->js_validation(); ?>
<br /><br />
<div id="dNotes">
    <?php
    $i = 0;
    $delete_note_url = url::add_query_arg( '_nonce', nonce::create('delete_note'), '/accounts/delete-note/' );
    $confirm_delete_note = _('Are you sure you want to delete this note? It cannot be undone.');

    if ( is_array( $notes ) )
    foreach ( $notes as $note ) {
        $date = new DateTime( $note->date_created );
        $delete_note_url = url::add_query_arg( 'anid', $note->id, $delete_note_url );
        $i++;
        ?>
    <div id="dNote<?php echo $note->id; ?>" class="note<?php if ( 1 == $i ) echo ' first'; ?>">
        <div class="title">
            <strong><?php echo $note->contact_name; ?></strong>
            <br /><?php echo $date->format( 'F j, Y' ); ?>
            <?php
            if ( $note->user_id == $user->user_id )
                echo '<br /><a href="', $delete_note_url, '" title="' . _('Delete') . '" ajax="1" confirm="', $confirm_delete_note, '">' . _('Delete') . '</a>';
            ?>
        </div>
        <div class="message"><?php echo $note->message; ?></div>
    </div>
    <?php } ?>
    <br clear="left" />
</div>

<?php echo $template->end(); ?>