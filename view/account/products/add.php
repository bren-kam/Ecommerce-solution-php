<?php
/**
 * @package Grey Suit Retail
 * @page Products > Add
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var int $product_count
 * @var Category[] $categories
 * @var Brand[] $brands
 */

require VIEW_PATH . $this->variables['view_base'] . 'sidebar.php';
?>

<div id="right-sidebar">
    <!-- Box Categories -->
    <div class="box">
        <h2><?php echo _('Products List'); ?></h2>
        <div class="box-content">
            <p><?php echo _('Product Usage'); ?>: <span id="sProductCount"><?php echo number_format( $product_count ); ?></span>/<span id="sAllowedProducts"><?php echo number_format( $user->account->products ); ?></span></p>
            <div id="dProductsList"></div>
            <p id="pNoProducts"><?php echo _('You have not selected any products to add.'); ?></p>
            <p id="pNewCount" class="hidden"><?php echo _('You are adding'), ' <span></span> ', _('new product(s).'); ?></p>
            <form id="fAddProducts" method="post" action="/products/add/">
                <input type="submit" class="button hidden" id="bAddProducts" value="<?php echo _('Add Products'); ?>" />
                <?php nonce::field('add'); ?>
            </form>
        </div>
    </div>

    <!-- Box Categories -->
    <div class="box">
        <h2><?php echo _('Request a Product'); ?></h2>
        <div class="box-content">
            <p>
                <?php echo _("Don't see a product you want?"); ?>
                <br />
                <a href="#dProductRequest" title="<?php echo _('Request for Product(s)'); ?>" rel="dialog"><?php echo _('Request a product'); ?></a> <?php echo _('and we will add it for you.'); ?>
            </p>
        </div>
    </div>
</div>
<div id="content">
    <div id="narrow-your-search-wrapper">
        <div id="narrow-your-search">
            <?php
            nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
            nonce::field( 'sku_exists', '_sku_exists' );
            ?>

            <h2><?php echo _('Narrow Your Search'); ?></h2>
            <form action="" id="fSearch">
                <table class="formatted">
                    <tr>
                        <td width="300">
                            <select name="sCategory" id="sCategory">
                                <option value="">-- <?php echo _('Select Category'); ?> --</option>
                                <?php foreach ( $categories as $category ) { ?>
                                    <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select id="sAutoComplete">
                                <option value="sku"><?php echo _('SKU'); ?></option>
                                <option value="products"><?php echo _('Product Name'); ?></option>
                                <option value="brands"><?php echo _('Brands'); ?></option>
                            </select>
                        </td>
                        <td>
                            <div style="margin-right: 18px">
                                <input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter Search...'); ?>" style="width: 100%" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" colspan="2"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
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
                    <th width="50%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    <th width="20%"><?php echo _('Brand'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    <th width="15%"><?php echo _('SKU'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    <th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="dProductRequest" class="hidden">
        <h3><?php echo _('New Request'); ?></h3>
        <br />
        <form name="fProductRequest" action="/products/request/" method="post" ajax="1">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><label for="sRequestBrand"><?php echo _('Brand'); ?>:</label></td>
                    <td>
                        <select name="sRequestBrand" id="sRequestBrand" error='<?php echo _('The "Brand" field is required'); ?>'>
                            <option value="">-- <?php echo _('Select a Brand'); ?> --</option>
                            <?php foreach ( $brands as $brand ) { ?>
                            <option value="<?php echo $brand->id; ?>"><?php echo $brand->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="tRequestSKU"><?php echo _('SKU'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tRequestSKU" id="tRequestSKU" error='<?php echo _('The "SKU" field is required'); ?>' /></td>
                </tr>
                <tr>
                    <td><label for="tCollection"><?php echo _('Collections/Product'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tCollection" id="tCollection" error='<?php echo _('The "Collections/Product" field is required'); ?>' /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><a href="#" id="aAddRequest" title="<?php echo _('Add Request'); ?>"><?php echo _('Add Request'); ?></a></td>
                </tr>
                <tr><td colspan="2">
                    <div id="dRequestList"></div>
                    <hr />
                </td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Send Request'); ?>" /></td>
                </tr>
            </table>
            <a href="#" id="aClose" class="close hidden">&nbsp;</a>
            <?php nonce::field( 'request' ); ?>
        </form>
    </div>

<?php echo $template->end(); ?>