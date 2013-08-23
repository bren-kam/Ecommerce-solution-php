<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Category -- Dialog
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category $category
 * @var array $categories
 * @var array $attributes
 * @var array $category_attribute_ids
 * @var string $google_taxonomy
 */

$add_edit_url = '/products/categories/add-edit/';

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
    <tr>
        <td><label for="tSlug"><?php echo _('Slug'); ?>:</label></td>
        <td><input type="text" class="tb" name="tSlug" id="tSlug" value="<?php echo $template->v('slug'); ?>" /></td>
    </tr>
    <tr>
        <td><label for="tGoogleTaxonomy"><a href="http://www.google.com/basepages/producttype/taxonomy.en-US.txt" target="_blank" title="<?php echo _('Google Taxonomy'); ?>"><?php echo _('Google Taxonomy'); ?>:</a></label></td>
        <td><input type="text" class="tb" name="tGoogleTaxonomy" id="tGoogleTaxonomy" value="<?php echo $google_taxonomy; ?>" /></td>
    </tr>
    <tr>
        <td class="top"><label for="sAttributes"><?php echo _('Attributes'); ?>:</label></td>
        <td>
            <select name="sAttributes" id="sAttributes" multiple="multiple">
                <?php
                foreach ( $attributes as $a ) {
                    $disabled = ( in_array( $a->id, $category_attribute_ids ) ) ? ' disabled="disabled"' : '';
                ?>
                    <option value="<?php echo $a->id; ?>"<?php echo $disabled; ?>><?php echo $a->title; ?></option>
                <?php } ?>
            </select>
            <div id="attributes-list">
            <?php foreach ( $category_attribute_ids as $caid ) { ?>
            <div extra="<?php echo format::slug( $attributes[$caid]->title ); ?>" id="dAttribute<?php echo $caid; ?>" class="attribute-container">
                <div class="attribute">
                    <span class="attribute-name"><?php echo $attributes[$caid]->title; ?></span>
                    <a href="#" class="delete-attribute" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" /></a>
                </div>
            </div>
            <?php } ?>
            </div>
            <input type="hidden" name="hAttributes" id="hAttributes" />
        </td>
    </tr>
</table>
<div class="boxy-footer hidden">
    <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
    <p class="text-right col-2 float-right"><input type="submit" class="button" value="<?php echo ( $category->id ) ? _('Save') : _('Add'); ?>" rel="fAddEditCategory" /></p>
</div>
<?php nonce::field('add_edit'); ?>
</form>
<script type="text/javascript">
    updateAttributes();
</script>
