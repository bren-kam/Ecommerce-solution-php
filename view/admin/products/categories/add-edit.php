<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Category -- Dialog
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

?>

<form name="fAddEditCategory" action="" method="post">
<table>
    <tr>
        <td><label for="tName"><?php echo _('Name'); ?>:</label></td>
        <td><input type="text" class="tb" name="tName" id="tName" /></td>
    </tr>
    <tr>
        <td><label for="sParentCategoryID"><?php echo _('Parent Category'); ?>:</label></td>
        <td>
            <select name="sParentCategoryID" id="sParentCategoryID">
                <option value="">-- <?php echo _('Select Category'); ?> --</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="tCategoryURL"><?php echo _('Category URL'); ?>:</label></td>
        <td><input type="text" class="tb" name="tCategoryURL" id="tCategoryURL" /></td>
    </tr>
    <tr>
        <td><label for="sAttributes"><?php echo _('Attributes'); ?>:</label></td>
        <td>
            <select name="sAttributes" id="sAttributes">
                <option value="">-- <?php echo _('Select Attribute'); ?> --</option>
            </select>
        </td>
    </tr>
</table>
<div class="boxy-footer hidden">
    <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
    <p class="text-right col-2 float-right"><input type="submit" class="button" value="<?php echo ( $category_id ) ? _('Save') : _('Add'); ?>" /></p>
</div>
<?php nonce::field('add-edit'); ?>
</form>