<?php
/**
 * @package Grey Suit Retail
 * @page Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category[] $categories
 * @var int $product_count
 * @var WebsiteCoupon[] $coupons
 * @var array $pricing_points
 */

require VIEW_PATH . $this->variables['view_base'] . 'sidebar.php';
?>

<div id="content">
    <div id="narrow-your-search-wrapper">
    <div id="narrow-your-search">
        <?php
        nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
        nonce::field( 'update_sequence', '_update_sequence' );
        nonce::field( 'get_product_dialog_info', '_get_product_dialog_info' );
        ?>
        <h2 class="col-2 float-left"><?php echo _('Narrow Your Search'); ?></h2>
        <h3 class="col-2 float-left text-right"><?php echo _('Product Usage'); ?>: <?php echo number_format( $product_count ), ' / ', number_format( $user->account->products ); ?></h3>
        <br clear="left" />
        <form action="" id="fSearch">
        <table class="formatted">
            <tr>
                <td width="300">
                    <select id="sCategory">
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
                        <option value="product"><?php echo _('Product Name'); ?></option>
                        <option value="brand"><?php echo _('Brand'); ?></option>
                    </select>
                </td>
                <td><input type="text" class="tb" id="tAutoComplete" placeholder="<?php echo _('Enter SKU...'); ?>" style="position: relative; top: 1px;" /></td>
                <td align="right"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
            </tr>
            <tr>
                <td>
                    <?php
                    if ( !empty( $pricing_points ) )
                    ?>
                    <select id="sPricing">
                        <option value="">-- <?php echo _('Select Price Range'); ?> --</option>
                        <option value="0|0"><?php echo _('Unpriced'); ?></option>
                        <option value="0|<?php echo $pricing_points[0]; ?>"><?php echo '$' . number_format( $pricing_points[0] ) . ' ' . _('or less'); ?></option>
                        <option value="<?php echo $pricing_points[0] . '|' . $pricing_points[1]; ?>"><?php echo '$' . number_format( $pricing_points[0] ) . ' - $' . number_format( $pricing_points[1] ); ?></option>
                        <option value="<?php echo $pricing_points[1] . '|' . $pricing_points[2]; ?>"><?php echo '$' . number_format( $pricing_points[1] ) . ' - $' . number_format( $pricing_points[2] ); ?></option>
                        <option value="<?php echo $pricing_points[2]; ?>|"><?php echo '$' . number_format( $pricing_points[2] ) . ' ' . _('or more'); ?></option>
                    </select>
                </td>
                <td>
                    <input type="checkbox" class="cb" id="cbOnlyDiscontinued" value="1" /> <label for="cbOnlyDiscontinued"><?php echo _('Search Only Discontinued Products'); ?></label>
                    <br />
                    &nbsp; &nbsp; &nbsp; (<a href="<?php echo url::add_query_arg( '_nonce', nonce::create('remove_all_discontinued_products'), '/products/remove-all-discontinued-products/' ); ?>" ajax="1" confirm="<?php echo _('Are you sure you want to remove all discontinued products? This cannot be undone.'); ?>" title="<?php echo _('Remove All Discontinued Products'); ?>"><?php echo _('Remove All Discontinued Products'); ?></a>)
                </td>
            </tr>
        </table>
        </form>
    </div>
</div>

<div id="subcontent-wrapper" class="narrow-your-search">
    <div id="subcontent">
        <br /><br />
        <br />
        <div id="dProductList" align="center">
        </div>
        <br clear="left" /><br />
        <br /><br />
        </div>
</div>

<div id="dEditProduct" class="hidden">
	<form name="fEditProduct" id="fEditProduct" action="/products/update-product/" method="post" ajax="1">
	<div id="dPopupTabs">
        <p>
            <a href="#" class="screen-selector selected" id="aPricingProductInformation" title="<?php echo _('Pricing/Product Information'); ?>"><?php echo _('Pricing/Product Information'); ?></a>
            <a href="#" class="screen-selector" id="aProductOptions" title="<?php echo _('Product Options'); ?>"><?php echo _('Product Options'); ?></a>
            <?php if ( $user->account->shopping_cart) { ?>
                <a href="#" class="screen-selector" id="aShoppingCart" title="<?php echo _('Shopping Cart'); ?>"><?php echo _('Shopping Cart'); ?></a>
                <?php if ( $user->has_permission( User::ROLE_ADMIN ) ) { ?>
                    <a href="#" rel="http://<?php echo str_replace( 'account', 'admin', SUBDOMAIN ), '.', DOMAIN; ?>/products/add-edit/?pid=" class="screen-selector" id="aMasterCatalog" title="<?php echo _('Master Catalog'); ?>" target="_blank"><?php echo _('Master Catalog'); ?></a>
                <?php
                }
            }
            ?>
        </p>
    </div>
    <br />
    <br />
    <div class="screen selected" id="dPricingProductInformation">
        <br />
		<table cellpadding="0" cellspacing="0" class="col-2 float-left">
			<tr><td colspan="2"><h4><?php echo _('Pricing Information'); ?></h4></td></tr>
			<tr>
				<td><label for="tPrice"><?php echo _('Price'); ?>:</label></td>
                <td><input type="text" class="tb" name="tPrice" id="tPrice" /></td>
			</tr>
			<tr>
				<td><label for="tPriceNote"><?php echo _('Price Note'); ?>:</label></td>
				<td><input type="text" class="tb" name="tPriceNote" id="tPriceNote" maxlength="100" /></td>
			</tr>
			<tr>
				<td><label for="tAlternatePrice"><?php echo _('MSRP'); ?>:</label></td>
				<td><input type="text" class="tb" name="tAlternatePrice" id="tAlternatePrice" /></td>
			</tr>
			<tr>
				<td><label for="tAlternatePriceName"><?php echo _('MSRP Name'); ?>:</label></td>
				<td><input type="text" class="tb" name="tAlternatePriceName" id="tAlternatePriceName" maxlength="20" /></td>
			</tr>
			<tr>
				<td><label for="tSalePrice"><?php echo _('Sale Price'); ?>:</label></td>
				<td><input type="text" class="tb" name="tSalePrice" id="tSalePrice" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="checkbox" name="cbOnSale" id="cbOnSale" value="true" /> <label for="cbOnSale"><?php echo _('On Sale?'); ?></label></td>
			</tr>
            <tr>
                <td><label for="tSetupFee"><?php echo _('Setup Fee'); ?>:</label></td>
                <td><input type="text" class="tb" name="tSetupFee" id="tSetupFee" /></td>
            </tr>
		</table>
		<table class="col-2 float-left">
			<tr><td colspan="2"><h4><?php echo _('Product Information'); ?></h4></td></tr>
			<tr>
				<td class="top"><label for="taProductNote"><?php echo _('Product Note'); ?>:</label></td>
				<td><textarea name="taProductNote" id="taProductNote" cols="30" rows="2" style="width:205px"></textarea></td>
			</tr>
			<tr>
				<td><label for="tWarrantyLength"><?php echo _('Warranty Length'); ?>:</label></td>
				<td><input type="text" class="tb" name="tWarrantyLength" id="tWarrantyLength" maxlength="50" /></td>
			</tr>
			<tr>
				<td><label for="tInventory"><?php echo _('Inventory'); ?>:</label></td>
				<td><input type="text" class="tb" name="tInventory" id="tInventory" maxlength="10" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="checkbox" class="cb" name="cbDisplayInventory" id="cbDisplayInventory" value="1" /> <label for="cbDisplayInventory"><?php echo _('Display Inventory?'); ?></label></td>
			</tr>
			<tr>
				<td><label for="sStatus"><?php echo _('Status'); ?>:</label></td>
				<td>
					<select name="sStatus" id="sStatus">
						<option value="1"><?php echo _('In Stock'); ?></option>
						<option value="0"><?php echo _('Out of Stock'); ?></option>
						<option value="2"><?php echo _('On Display'); ?></option>
						<option value="3"><?php echo _('Special Order'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="tMetaTitle"><?php echo _('Meta Title'); ?>:</label></td>
				<td><input type="text" class="tb" name="tMetaTitle" id="tMetaTitle" maxlength="200" style="width:205px" /></td>
			</tr>
			<tr>
				<td><label for="tMetaDescription"><?php echo _('Meta Description'); ?>:</label></td>
				<td><input type="text" class="tb" name="tMetaDescription" id="tMetaDescription" maxlength="250" style="width:205px" /></td>
			</tr>
			<tr>
				<td><label for="tMetaKeywords"><?php echo _('Meta Keywords'); ?>:</label></td>
				<td><input type="text" class="tb" name="tMetaKeywords" id="tMetaKeywords" maxlength="200" style="width:205px" /></td>
			</tr>
		</table>
	</div>
	<div class="screen hidden" id="dProductOptions">
		<br />
		<select id="sProductOptions">
			<option value="">-- <?php echo _('Select a Product Option'); ?> --</option>
		</select>
		<a href="#" id="aAddProductOption" title="<?php echo _('Add Product Option'); ?>"><?php echo _('Add Product Option'); ?>...</a>
        <br /><br />
		<div id="dProductOptionsList"></div>
        <br />
	</div>
	<div class="screen hidden" id="dShoppingCart">
        <br />
		<table>
			<tr>
				<td><label for="tStoreSKU"><?php echo _('Store SKU'); ?></label></td>
				<td><input type="text" class="tb" name="tStoreSKU" id="tStoreSKU" maxlength="25" /></td>
			</tr>
			<tr>
				<td><label for="tShipsIn"><?php echo _('Ships In'); ?>:</label></td>
				<td><input type="text" class="tb" name="tShipsIn" id="tShipsIn" maxlength="50" /></td>
			</tr>
			<tr>
				<td valign="top"><?php echo _('Additional Shipping'); ?>:</td>
				<td>
					<p><input type="radio" class="rb-shipping" name="rShippingMethod" id="rShippingMethodFlatRate" value="Flat Rate" checked="checked" /> <label for="tShippingFlatRate"><?php echo _('Flate Rate'); ?></label> &nbsp; &nbsp;<span class="additional-shipping selected">$ <input type="text" class="tb price" name="tShippingFlatRate" id="tShippingFlatRate" maxlength="10" /></span></p>
					<p><input type="radio" class="rb-shipping" name="rShippingMethod" id="rShippingMethodPercentage" value="Percentage" /> <label for="tShippingPercentage"><?php echo _('Percentage'); ?></label> &nbsp;% <input type="text" class="tb price" name="tShippingPercentage" id="tShippingPercentage" maxlength="10" /></p>
				</td>
			</tr>
			<tr>
				<td><label for="tWholesalePrice"><?php echo _('Wholesale Price'); ?>:</label></td>
				<td><input type="text" class="tb" name="tWholesalePrice" id="tWholesalePrice" /></td>
			</tr>
			<tr>
				<td><label for="tWeight"><?php echo _('Weight'); ?>:</label></td>
				<td><input type="text" class="tb" name="tWeight" id="tWeight" /></td>
			</tr>
			<tr>
				<td valign="top"><?php echo _('Coupons'); ?>:</td>
				<td>
				<div id="dCouponList"></div>
				<select id="sCoupons">
					<option value="">-- <?php echo _('Select a Coupon'); ?> --</option>
					<?php foreach ( $coupons as $coupon ) { ?>
                        <option value="<?php echo $coupon->id; ?>"><?php echo $coupon->name; ?></option>
					<?php } ?>
				</select>
				<a href="#" id="aAddCoupon" title="<?php echo _('Add Coupon'); ?>"><?php echo _('Add Coupon'); ?>...</a>
				<input type="hidden" name="hCoupons" id="hCoupons" />
			</tr>
		</table>
	</div>

	<input type="hidden" id="hProductID" name="hProductID" />
	<?php nonce::field( 'update_product' , '_nonce' ); ?>
	</form>
	<input type="hidden" id="dDialogHeight" value="500" />
    <div class="boxy-footer hidden">
        <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
        <p class="text-right col-2 float-right"><input type="submit" id="bSaveProduct" class="button" value="<?php echo _('Save'); ?>" /></div>
    </div>
</div>

<?php echo $template->end(); ?>