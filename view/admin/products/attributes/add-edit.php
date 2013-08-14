<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Attribute
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Attribute $attribute
 * @var array $attribute_items
 * @var bool|string $errs
 * @var string $validation
 */

$confirm_delete = _('Are you sure you want to delete this attribute? This cannot be undone.');

if ( $attribute->id ) {
    $title = _('Edit Attribute');
    $button = _('Save');
} else {
    $title = _('Add Attribute');
    $button = _('Add');
}

echo $template->start( $title, '../sidebar' );

if ( $errs )
    echo '<p class="red">' . $errs . '</p><br />';
?>

<form name="fAddEditAttribute" action="" method="post">
    <table>
        <tr>
            <td><label for="tTitle"><?php echo _('Title'); ?></label> <span class="red">*</span>:</td>
            <td><input type="text" class="tb" name="tTitle" id="tTitle" value="<?php echo ( isset( $_POST['tTitle'] ) || !$attribute->id ) ? $template->v('tTitle') : $attribute->title; ?>" /></td>
        </tr>
        <tr>
            <td><label for="tName"><?php echo _('Name'); ?></label> <span class="red">*</span>:</td>
            <td><input type="text" class="tb" name="tName" id="tName" value="<?php echo ( isset( $_POST['tName'] ) || !$attribute->id ) ? $template->v('tName') : $attribute->name; ?>" /></td>
        </tr>
        <tr>
            <td><strong><?php echo _('Items'); ?></strong></td>
            <td>
                <input type="text" class="tb" id="list-item-value" placeholder="<?php echo _('Item Name'); ?>" />
                <a href="#" id="add-list-item" title="<?php echo _('Add Item'); ?>"><?php echo _('Add Item...'); ?></a>
                <br />
                <div id="items-list">
                    <?php
                    /**
                     * @var AttributeItem $attribute_item
                     */
                    if ( is_array( $attribute_items ) )
                    foreach ( $attribute_items as $attribute_item ) {
                    ?>
                        <div class="list-item">
                            <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>
                            <input type="text" class="tb" name="list-items[ai<?php echo $attribute_item->id; ?>]" value="<?php echo $attribute_item->name; ?>" />

                            <a href="#" class="delete-list-item" title="<?php echo _('Delete'); ?>" confirm="<?php echo $confirm_delete; ?>"><img src="/images/icons/x.png" alt="<?php echo _('Delete'); ?>" width="15" height="17" /></a>
                        </div>
                    <?php } ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" class="button" value="<?php echo $button; ?>" /></td>
        </tr>
    </table>
    <?php nonce::field( 'add_edit' ); ?>
</form>
<?php echo $validation; ?>
<div class="list-item hidden" id="list-item-template">
    <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>
    <input type="text" class="tb" name="list-items[]" />

    <a href="#" class="delete-list-item" title="<?php echo _('Delete'); ?>" confirm="<?php echo $confirm_delete; ?>"><img src="/images/icons/x.png" alt="<?php echo _('Delete'); ?>" width="15" height="17" /></a>
</div>

<?php echo $template->end(); ?>