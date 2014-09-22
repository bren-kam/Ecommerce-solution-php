<?php
/**
 * @package Grey Suit Retail
 * @page Checklist
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Checklist $checklist
 * @var array $items
 */

echo $template->start( $checklist->title . ' ' . _('Checklist') );

$date_start = new DateTime( $checklist->date_created );
$date_end = new DateTime( $checklist->date_created );
$date_end->add( new DateInterval( 'P30D' ) );

nonce::field( 'update_item', '_update_item' );
nonce::field( 'delete-note', '_delete_note' );
$_notes = nonce::create('notes');
?>

<table style="width: auto;" class="formatted">
    <tr>
        <td><strong><?php echo _('Start Date'); ?>:</strong></td>
        <td><?php echo $date_start->format( 'F j, Y' ); ?></td>
    </tr>
    <tr>
        <td><strong><?php echo _('End Date'); ?>:</strong></td>
        <td><?php echo $date_end->format( 'F j, Y'); ?></td>
    </tr>
    <tr>
        <td><strong><?php echo _('Days Left'); ?>:</strong></td>
        <td><?php echo $checklist->days_left; ?></td>
</table>

<br />
<div id="list">
<?php
if ( is_array( $items ) ) {
foreach ( $items as $section_title => $section_items ) {
?>
    <h3><?php echo $section_title; ?></h3>
    <?php
    /**
     * @var ChecklistItem $item
     */
    foreach ( $section_items as $item ) {
        if ( $item->checked ) {
            $class = ' done';
            $checked = ' checked="checked"';
        } else {
            $class = $checked = '';
        }
    ?>
    <div id="item-<?php echo $item->checklist_website_item_id; ?>" class="list-item<?php echo $class; ?>">
        <span class="sequence"><?php echo $item->sequence; ?> . </span>
        <span class="check"><input type="checkbox" class="cb" value="<?php echo $item->checklist_website_item_id; ?>" <?php echo $checked; ?> /></span>
        <span><strong><?php echo $item->name; ?></strong> (<?php echo $item->assigned_to; ?>) - <a href="<?php echo url::add_query_arg( array( '_nonce' => $_notes, 'cwiid' => $item->checklist_website_item_id ), '/checklists/notes/' ); ?>" title="<?php echo _('Notes'); ?>" class="notes" rel="dialog" cache="0"><?php echo _('Notes'); ?></a> [ <span><?php echo (int) $item->notes_count; ?></span> ]</span>
    </div>
<?php
    }
}
?>
</div>
<?php } else { ?>
    <h4><?php echo _('There are no items in the checklist.'); ?></h4>
<?php } ?>

<?php echo $template->end(); ?>