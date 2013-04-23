<?php
/**
 * @package Grey Suit Retail
 * @page Knowledge Base Add/Edit Category -- Dialog
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category $category
 * @var array $categories
 * @var int $parent_category_id
 */

$add_edit_url = '/knowledge-base/categories/add-edit/';

if ( $category->id )
    $add_edit_url = url::add_query_arg( array( 'cid' => $category->id, 'pcid' => $_GET['pcid'] ) );
?>
<form name="fAddEditCategory" class="form-add-edit-category" id="fAddEditCategory" action="<?php echo $add_edit_url; ?>" method="post" ajax="1">
<table>
    <tr>
        <td><label for="tName"><?php echo _('Name'); ?>:</label></td>
        <td><input type="text" class="tb" name="tName" id="tName" value="<?php echo $template->v('name'); ?>" /></td>
    </tr>
    <tr>
        <td><label for="sParentCategoryID"><?php echo _('Parent Category'); ?>:</label></td>
        <td>
            <select name="sParentCategoryID" id="sParentCategoryID">
                <option value="">-- <?php echo _('Select Category'); ?> --</option>
                <?php
                foreach ( $categories as $c ) {
                    $selected = ( $parent_category_id == $c->id ) ? ' selected="selected"' : '';
                ?>
                    <option value="<?php echo $c->id; ?>"<?php echo $selected; ?>><?php echo str_repeat( '&nbsp;', $c->depth * 5 ), $c->name; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>
<div class="boxy-footer hidden">
    <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
    <p class="text-right col-2 float-right"><input type="submit" class="button" value="<?php echo ( $category->id ) ? _('Save') : _('Add'); ?>" rel="fAddEditCategory" /></p>
</div>
<?php nonce::field('add_edit'); ?>
</form>