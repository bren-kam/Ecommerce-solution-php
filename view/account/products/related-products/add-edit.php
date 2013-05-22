<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Related Products | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteProductGroup $group
 * @var Product[] $products
 * @var string $errs
 * @var string $js_validation
 */

$title = ( $group->id ) ? _('Edit') : _('Add');
$title .= ' ' . _('Group');

require VIEW_PATH . $this->variables['view_base'] . '../sidebar.php';
?>

<form action="<?php if ( $group->id ) echo '?wpgid=' . $group->id; ?>" name="fAddEditGroup" method="post">
    <div id="content">
        <div id="narrow-your-search-wrapper">
        <div id="narrow-your-search">
            <?php nonce::field( 'autocomplete_owned', '_autocomplete_owned' ); ?>
            <input type="text" class="tb" name="tName" id="tName" value="<?php echo ( isset( $_POST['tName'] ) ) ? $_POST['tName'] : $group->name; ?>" tmpval="<?php echo _('Related Product Group Name...'); ?>" />
            <br /><br />
            <h2><?php echo _('Narrow Your Search'); ?></h2>
            <br />
            <form action="" id="fSearch">
            <table class="formatted">
                <tr>
                    <td>
                        <select id="sAutoComplete">
                            <option value="sku"><?php echo _('SKU'); ?></option>
                            <option value="product"><?php echo _('Product Name'); ?></option>
                            <option value="brand"><?php echo _('Brand'); ?></option>
                        </select>
                    </td>
                    <td><input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" style="position: relative; top: 1px;" /></td>
                    <td align="right"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
                </tr>
            </table>
            </form>
        </div>
    </div>

    <div id="subcontent-wrapper" class="narrow-your-search">
        <div id="subcontent">
            <table id="tAddProducts">
                <thead>
                    <tr>
                        <th width="45%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                        <th width="25%"><?php echo _('Brand'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                        <th width="15%"><?php echo _('SKU'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                        <th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <h2><?php echo _('Selected Products'); ?></h2>
            <div id="dSelectedProducts">
                <?php
                foreach ( $products as $product ) {
                    $image = current( $product->get_images() );
                ?>
                <div id="dProduct_<?php echo $product->id; ?>" class="product">
                    <h4><?php echo format::limit_chars( $product->name, 37 ); ?></h4>
                    <p align="center"><img src="http://<?php echo $product->industry; ?>.retailcatalog.us/products/<?php echo $product->id; ?>/small/<?php echo $image; ?>" alt="<?php echo $product->name; ?>" height="110" /></p>
                    <p><?php echo _('Brand'); ?>: <?php echo $product->brand_id; ?></p>
                    <p class="product-actions" id="pProductAction<?php echo $product->id; ?>"><a href="javascript:;" class="remove-product" title="<?php echo _('Remove'); ?>"><?php echo _('Remove'); ?></a></p>
                    <input type="hidden" name="products[]" class="hidden-product" id="hProduct<?php echo $product->id; ?>" value="<?php echo $product->id; ?>" />
                </div>
                <?php } ?>
            </div>
            <br clear="left" /><br />
            <input type="submit" class="button" value="<?php echo ( $group->id ) ? _('Save') : _('Add'); ?>" />
            <br /><br />
        </div>
    </div>
    <?php nonce::field('add_edit'); ?>
</form>
<?php
echo $js_validation;
echo $template->end();
?>