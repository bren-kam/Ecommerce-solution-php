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


$date_start = new DateTime( $checklist->date_created );
$date_end = new DateTime( $checklist->date_created );
$date_end->add( new DateInterval( 'P30D' ) );

nonce::field( 'update_item', '_update_item' );
nonce::field( 'delete-note', '_delete_note' );
$_notes = nonce::create('notes');
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $checklist->title ?>
            </header>

            <div class="panel-body">
                <ul>
                    <li><strong>Start Date:</strong> <?php echo $date_start->format( 'F j, Y' ); ?></li>
                    <li><strong>End Date:</strong> <?php echo $date_end->format( 'F j, Y' ); ?></li>
                    <li><strong>Days Left:</strong> <?php echo $checklist->days_left; ?></li>
                </ul>
            </div>
        </section>
    </div>
</div>

<?php
    if ( is_array( $items ) ):
        foreach ( $items as $section_title => $section_items ):
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $section_title ?>
            </header>

            <div class="panel-body">
                <ul class="checklist">
                    <?php foreach ( $section_items as $item ): ?>
                    <li data-checklist-item-id="<?php echo $item->checklist_website_item_id ?>">
                        <span class="sequence"><?php echo $item->sequence; ?>.</span>

                        <span class="label <?php echo $item->checked ? 'label-success' : 'label-default' ?>">
                            <input type="checkbox" value="<?php echo $item->checklist_website_item_id; ?>" <?php if ( $item->checked ) echo 'checked'; ?> />
                            <?php echo $item->checked ? 'Done' : 'Pending' ?>
                        </span>

                        &nbsp;

                        <a class="notes label label-primary" href="<?php echo url::add_query_arg( array( '_nonce' => $_notes, 'cwiid' => $item->checklist_website_item_id ), '/checklists/notes/' ); ?>" title="View Notes" data-modal><?php echo (int) $item->notes_count; ?> notes</a>

                        <strong><?php echo $item->name; ?></strong> (<?php echo $item->assigned_to; ?>)

                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
</div>

<?php
        endforeach;
    else:
?>
<div class="alert alert-warning">There are no items in this checklist</div>

<?php endif; ?>


