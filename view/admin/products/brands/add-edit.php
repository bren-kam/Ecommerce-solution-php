<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Brand
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Brand $brand
 * @var array $product_options_array
 * @var array $product_option_ids
 * @var bool|string $errs
 */

if ( $brand->id ) {
    $title = _('Edit Brand');
    $button = _('Save');
} else {
    $title = _('Add Brand');
    $button = _('Add');
}

echo $template->start( $title, '../sidebar' );

if ( $errs )
    echo '<p class="red">' . $errs . '</p><br />';
?>

<form name="fAddEditBrand" action="" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td><label for="tName"><?php echo _('Name'); ?></label> <span class="red">*</span>:</td>
            <td><input type="text" class="tb" name="tName" id="tName" value="<?php echo ( isset( $_POST['tName'] ) || !$brand->id ) ? $template->v('tName') : $brand->name; ?>" /></td>
        </tr>
        <tr>
            <td><label for="tSlug"><?php echo _('Slug'); ?></label> <span class="red">*</span>:</td>
            <td><input type="text" class="tb" name="tSlug" id="tSlug" value="<?php echo ( isset( $_POST['tSlug'] ) || !$brand->id ) ? $template->v('tSlug') : $brand->slug; ?>" /></td>
        </tr>
        <tr>
            <td><label for="tWebsite"><?php echo _('Website'); ?></label>:</td>
            <td><input type="text" class="tb" name="tWebsite" id="tWebsite" value="<?php echo ( isset( $_POST['tWebsite'] ) || !$brand->id ) ? $template->v('tWebsite') : $brand->link; ?>" /></td>
        </tr>
        <tr>
            <td><label for="sProductOptions"><?php echo _('Product Options'); ?></label>:</td>
            <td>
                <select name="sProductOptions" id="sProductOptions">
                    <option value="">-- <?php echo _('Select Product Option'); ?> --</option>
                    <?php
                    $product_options = array();

                    foreach ( $product_options_array as $po ) {
                        $product_options[$po->id] = $po;
                        $disabled = ( in_array( $po->id, $product_option_ids ) ) ? ' disabled="disabled"' : '';
                    ?>
                        <option value="<?php echo $po->id; ?>"<?php echo $disabled; ?>><?php echo $po->name; ?></option>
                    <?php } ?>
                </select>
                <div id="product-options-list">
                <?php foreach ( $product_option_ids as $product_option_id ) { ?>
                    <div class="product-option">
                        <span class="product-option-name"><?php echo $product_options[$product_option_id]->name; ?></span>
                        <a href="#" class="delete-product-option" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" /></a>
                        <input type="hidden" name="product-options[]" value="<?php echo $product_option_id; ?>" />
                    </div>
                <?php } ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="tImage"><?php echo _('Image'); ?></label>:</td>
            <td>
                <input type="text" class="tb" id="tImage" />
                <a href="#" id="aUpload" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
                <input type="file" class="hidden" name="fImage" id="fImage" err="<?php echo _('Only jpeg, gif and png file types allowed.'); ?>" />
                <?php if ( $brand->id && !empty( $brand->image ) ) { ?>
                <br /><br />
                <img src="<?php echo $brand->image; ?>" alt="" />
                <?php } ?>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" class="button" value="<?php echo $button; ?>" /></td>
        </tr>
    </table>
    <?php nonce::field( 'add_edit' ); ?>
</form>
<?php echo $validation; ?>

<?php echo $template->end(); ?>