<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Product Option
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var ProductOption $product_option
 * @var array $product_option_list_items
 * @var array $forms
 * @var string $validation
 * @var string $errs
 */

$confirm_delete = _('Are you sure you want to delete this item? This cannot be undone.');

if ( $product_option->id ) {
    $title = _('Edit Product Option');
} else {
    $title = _('Add Product Option');
}

echo $template->start( $title, '../sidebar' );
?>

<div class="screen<?php if ( $product_option->id ) echo ' hidden'; ?>" id="choose">
    <div class="box">
        <h2><?php echo _('Drop Down List'); ?></h2>
        <img src="/images/icons/product-options/drop-down-list.png" width="73" height="73" alt="" align="left" />
        <p><?php echo _('This is an option that can be added to a product where the user would be able to select one of multiple options. Examples are Colors and Sizes.'); ?></p>
        <p><a href="#" class="button screen" rel="drop-down-list" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
    </div>
    <div class="box">
        <h2><?php echo _('Checkbox'); ?></h2>
        <img src="/images/icons/product-options/checkbox.png" width="69" height="69" alt="" align="left" />
        <p><?php echo _('This is an option that can be added to a product for a yes/no type question. Example: Insurance.'); ?></p>
        <p><a href="#" class="button screen" rel="checkbox" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
    </div>
    <?php /*
    <div class="box last">
        <h2><?php echo _('Text'); ?></h2>
        <img src="/images/icons/product-options/text.png" width="68" height="68" alt="" align="left" />
        <p><?php echo _('This is an option that can be added if you want to give the user the ability to provide you with some information.'); ?></p>
        <p><a href="#" class="button screen" rel="text" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
    </div>
    */?>

    <br clear="left" />
</div>
<div class="screen<?php if ( 'select' != $product_option->type ) echo ' hidden'; ?>" id="drop-down-list">
    <p><a href="#" class="screen" title="<?php echo _('Back'); ?>" rel="choose">&laquo; <?php echo _('Back'); ?></a></p>
    <h3><?php echo _('Drop Down List'); ?></h3>
    <br />
    <?php
    if ( $errs )
        echo '<p class="red">' . $errs . '</p><br />';
    ?>
    <form name="fAddEditDropDownList" action="" method="post">
        <table>
            <tr>
                <td><label for="tDropDownListTitle"><?php echo _('Title'); ?></label> <span class="red">*</span>:</td>
                <td><input type="text" class="tb" name="tDropDownListTitle" id="tDropDownListTitle" value="<?php echo ( isset( $_POST['tDropDownListTitle'] ) || !$product_option->id ) ? $template->v('tDropDownListTitle') : $product_option->title; ?>" maxlength="50" /></td>
            </tr>
            <tr>
                <td><label for="tDropDownListName"><?php echo _('Name'); ?></label> <span class="red">*</span>:</td>
                <td><input type="text" class="tb" name="tDropDownListName" id="tDropDownListName" value="<?php echo ( isset( $_POST['tDropDownListName'] ) || !$product_option->id ) ? $template->v('tDropDownListName') : $product_option->name; ?>" maxlength="200" /></td>
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
                         * @var ProductOptionListItem $product_option_list_item
                         */
                        if ( is_array( $product_option_list_items ) )
                        foreach ( $product_option_list_items as $product_option_list_item ) {
                        ?>
                            <div class="list-item">
                                <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>
                                <input type="text" class="tb" name="list-items[poli<?php echo $product_option_list_item->id; ?>]" value="<?php echo $product_option_list_item->value; ?>" />

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
        <input type="hidden" name="hType" value="drop-down-list" />
        <?php nonce::field('add_edit'); ?>
    </form>
    <?php echo $validation; ?>
</div>
<div class="screen<?php if ( 'checkbox' != $product_option->type ) echo ' hidden'; ?>" id="checkbox">
    <p><a href="#" class="screen" title="<?php echo _('Back'); ?>" rel="choose">&laquo; <?php echo _('Back'); ?></a></p>
    <h3><?php echo _('Checkbox'); ?></h3>
    <br />
    <?php echo $forms['checkbox']; ?>
</div>
<div class="screen<?php if ( 'text' != $product_option->type && 'textarea' != $product_option->type ) echo ' hidden'; ?>" id="text">
    <p><a href="#" class="screen" title="<?php echo _('Back'); ?>" rel="choose">&laquo; <?php echo _('Back'); ?></a></p>
    <h3><?php echo _('Text'); ?></h3>
    <br />
    <?php echo $forms['text']; ?>
</div>

<div class="list-item hidden" id="list-item-template">
    <a href="#" class="handle"><img src="/images/icons/move.png" width="16" height="16" alt="<?php echo _('Move'); ?>" /></a>
    <input type="text" class="tb" name="list-items[]" />

    <a href="#" class="delete-list-item" title="<?php echo _('Delete'); ?>" confirm="<?php echo $confirm_delete; ?>"><img src="/images/icons/x.png" alt="<?php echo _('Delete'); ?>" width="15" height="17" /></a>
</div>

<?php echo $template->end(); ?>