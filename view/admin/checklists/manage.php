<?php
/**
 * @package Grey Suit Retail
 * @page Manage Checklists
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $sections
 * @var array $items
 */

$add_section = nonce::create( 'add_section' );
$add_item = nonce::create( 'add_item' );

$item_confirm = _('Are you sure you want to remove this item? This cannot be undone.');
$err_confirm = _('Please remove all items before deleting a section');
$section_confirm = _('Are you sure you want to remove this section? This cannot be undone.');

echo $template->start( _('Manage Checklists') );
?>

<form action="" method="post">
    <div id="checklist-sections">
        <?php
        /**
         * @var ChecklistSection $section
         */
        if ( is_array( $sections ) )
            foreach ( $sections as $section ) {
            ?>
            <div id="section-<?php echo $section->checklist_section_id; ?>" class="section">
                <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>

                <input type="text" name="sections[<?php echo $section->checklist_section_id; ?>]" class="tb section-title" value="<?php echo $section->name; ?>" />

                <a href="#" class="remove-section" title="<?php echo _('Remove Section'); ?>" confirm="<?php echo $section_confirm; ?>" err="<?php echo $err_confirm; ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove Section'); ?>" /></a>
                <br />
                <div class="section-items">
                    <?php
                    /**
                     * @var ChecklistItem $item
                     */
                    if ( is_array( $items[$section->checklist_section_id] ) )
                    foreach ( $items[$section->checklist_section_id] as $item ) { ?>
                        <div class="item">
                            <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>
                            <input type="text" class="tb item-name" name="items[<?php echo $section->checklist_section_id; ?>][<?php echo $item->checklist_item_id; ?>][name]" value="<?php echo $item->name; ?>" />
                            <input type="text" class="tb item-assigned-to" name="items[<?php echo $section->checklist_section_id; ?>][<?php echo $item->checklist_item_id; ?>][assigned_to]" value="<?php echo $item->assigned_to; ?>" />
                            <a href="#" class="remove-item" title="<?php echo _('Remove Item'); ?>" confirm="<?php echo $item_confirm; ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove Item'); ?>" /></a>
                        </div>
                    <?php } ?>
                </div>
                <a href="<?php echo url::add_query_arg( array( '_nonce' => $add_item, 'csid' => $item->checklist_section_id ), '/checklists/add-item/' ); ?>" class="add-section-item" title="<?php echo _('Add Item'); ?>" ajax="1"><?php echo _('Add Item'); ?></a>
                <br /><br />
            </div>
        <?php } ?>
    </div>
    <br />
    <a href="<?php echo url::add_query_arg( array( '_nonce' => $add_section ), '/checklists/add-section/' ); ?>" id="add-section" title="<?php echo _('Add Section'); ?>" ajax="1"><?php echo _('Add Section'); ?></a>
    <br /><br />
    <br /><br />
    <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
    <?php nonce::field('manage'); ?>
</form>
    
<div class="hidden">
    <div class="section" id="section-template">
        <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>

        <input type="text" name="sections[checklist_section_id]" class="tb section-title" placeholder="<?php echo _('Section title...'); ?>" />

        <a href="#" class="remove-section" title="<?php echo _('Remove Section'); ?>" confirm="<?php echo $section_confirm; ?>" err="<?php echo $err_confirm; ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove Section'); ?>" /></a>
        <br />
        <div class="section-items">
        </div>
        <a href="#" class="add-section-item" title="<?php echo _('Add Item'); ?>" ajax="1"><?php echo _('Add Item'); ?></a>
        <br /><br />
    </div>
    <div class="item" id="item-template">
        <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>
        <input type="text" name="items[checklist_section_id][checklist_item_id][name]" class="tb item-name" placeholder="<?php echo _('Description...'); ?>" />
        <input type="text" name="items[checklist_section_id][checklist_item_id][assigned_to]" class="tb item-assigned-to" placeholder="<?php echo _('Assigned to...'); ?>" />
        <a href="#" class="remove-item" title="<?php echo _('Remove Item'); ?>" confirm="<?php echo $item_confirm; ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove Item'); ?>" /></a>
    </div>
</div>

<?php echo $template->end(); ?>