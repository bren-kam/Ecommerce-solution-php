<?php
/**
 * @page Product Catalog
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$c = new Coupons;
$p = new Products;
$v = new Validator;
$wc = new Website_Categories;

$v->form_name = 'fEditProduct';
$v->add_validation( 'hProductID', 'req', _('An error occurred while trying to save your product. Please refresh the page and try again') );

$v->add_validation( 'tAlternatePrice', 'float', _('The "Alternate Price" field may only contain a number and a period') );
$v->add_validation( 'tPrice', 'float', _('The "Price" field may only contain a number and a period') );
$v->add_validation( 'tSalePrice', 'float', _('The "Sale Price" field may only contain a number and a period') );
$v->add_validation( 'tInventory', 'int', _('The "Inventory" field may only contain a number') );
$v->add_validation( 'tWholesalePrice', 'float', _('The "Wholesale Price" field may only contain a number and a period') );
$v->add_validation( 'tWeight', 'float', _('The "Weight" field may only contain a number and a period') );

add_footer( $v->js_validation() );

$where = ' AND ( a.`website_id` = 0 || a.`website_id` = ' . (int) $user['website']['website_id'] . ' )';
$where .= ' AND c.`website_id` = ' . (int) $user['website']['website_id'];
$where .= " AND a.`publish_visibility` = 'public' AND a.`publish_date` <> '0000-00-00 00:00:00'";

$product_count = $p->count_website_products( $where ) . "/" . $user['website']['products'];
$coupons = $c->get_all();
$categories = $wc->get_list();

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
css('products/list');
javascript( 'mammoth', 'products/list');

$title = _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Product Catalog'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
		<?php 
		nonce::field( 'get-products', '_ajax_get_products' );
		nonce::field( 'products-autocomplete', '_ajax_autocomplete' );
		nonce::field( 'update-website-product-sequence', '_ajax_update_website_product_sequence' );
		nonce::field( 'get-product-dialog-info', '_ajax_get_product_dialog_info' );
		
		if ( isset( $_GET['m'] ) && '1' == $_GET['m'] ) {
		?>
		<p class="success"><?php echo _('Your product(s) have been successfully added!'); ?></p>
		<?php } ?>
		<div id="dNarrowSearchContainer">
			<div id="dNarrowSearch">
				<h2 class="col-2 float-left"><?php echo _('Narrow Your Search'); ?></h2>
				<h3 class="col-2 float-left text-right"><?php echo _('Product Usage'); ?>: <?php echo $product_count; ?></h3>
				<br />
				<table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%" class="form">
					<tr>
						<td><label for="sCategory"><?php echo _('Category'); ?></label></td>
						<td>
							<select name="sCategory" id="sCategory">
								<option value="">-- <?php echo _('Select a Category'); ?> --</option>
								<?php echo $categories; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="264">
							<select id="sAutoComplete">
								<option value="sku"><?php echo _('SKU'); ?></option>
								<option value="product"><?php echo _('Product Name'); ?></option>
								<option value="brand"><?php echo _('Brand'); ?></option>
							</select>
						</td>
						<td valign="top"><input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" style="width: 100% !important;" /></td>
						<td class="text-right" width="125"><a href="javascript:;" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
					</tr>
				</table>
				<img id="iNYSArrow" src="/images/narrow-your-search.png" alt="" width="76" height="27" />
			</div>
		</div>
		<br /><br />
		<br />
		<div id="dProductList" align="center">
		</div>
		<br clear="left" /><br />
		<br /><br />
	</div>
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<div id="dEditProduct" class="hidden">
	<form name="fEditProduct" id="fEditProduct" action="/ajax/products/update-product/" method="post" ajax="1">
	<br />
	<?php if ( $user['website']['shopping_cart'] ) { ?>
	<p>
		<a href="javascript:;" class="button screen-selector selected" id="aPricingProductInformation" title="<?php echo _('Pricing/Product Information'); ?>"><?php echo _('Pricing/Product Information'); ?></a> 
		<a href="javascript:;" class="button screen-selector" id="aProductOptions" title="<?php echo _('Product Options'); ?>"><?php echo _('Product Options'); ?></a> 
		<a href="javascript:;" class="button screen-selector" id="aShoppingCart" title="<?php echo _('Shopping Cart'); ?>"><?php echo _('Shopping Cart'); ?></a>
	</p>
	<br />
	<?php } ?>
	<div class="screen selected" id="dPricingProductInformation">
		<h2><?php echo _('Pricing/Product Information'); ?></h2>
		<br />
		<table cellpadding="0" cellspacing="0" class="col-2 float-left">
			<tr><td colspan="2"><strong><?php echo _('Pricing Information'); ?></strong></td></tr>
			<tr>
				<td><label for="tPrice"><?php echo _('Price'); ?>:</label></td>
				<td><input type="text" class="tb" name="tPrice" id="tPrice" /></td>
			</tr>
			<tr>
				<td><label for="tPriceNote"><?php echo _('Price Note'); ?>:</label></td>
				<td><input type="text" class="tb" name="tPriceNote" id="tPriceNote" maxlength="20" /></td>
			</tr>
			<tr>
				<td><label for="tAlternatePrice"><?php echo _('Alternate Price'); ?>:</label></td>
				<td><input type="text" class="tb" name="tAlternatePrice" id="tAlternatePrice" /></td>
			</tr>
			<tr>
				<td><label for="tAlternatePriceName"><?php echo _('Alternate Price Name'); ?>:</label></td>
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
		</table>
		<table class="col-2 float-left">
			<tr><td colspan="2"><strong><?php echo _('Product Information'); ?></strong></td></tr>
			<tr>
				<td valign="top"><label for="taProductNote"><?php echo _('Product Note'); ?>:</label></td>
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
		<h2><?php echo _('Product Options'); ?></h2>
		<br />
		<div id="dProductOptionsList"></div>
		<br />
		<select id="sProductOptions">
			<option value="">-- <?php echo _('Select a Product Option'); ?> --</option>
		</select>
		<a href="javascript:;" id="aAddProductOption" title="<?php echo _('Add Product Option'); ?>"><?php echo _('Add Product Option'); ?>...</a>
	</div>
	<div class="screen hidden" id="dShoppingCart">
		<h2><?php echo _('Shopping Cart'); ?></h2>
		<br />
		<table cellpadding="0" cellspacing="0">
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
					<p style="padding-bottom:7px"><input type="radio" class="rb-shipping" name="rShippingMethod" id="rShippingMethodFlatRate" value="Flat Rate" checked="checked" /> <label for="tShippingFlatRate"><?php echo _('Flate Rate'); ?></label> &nbsp; &nbsp;<span class="additional-shipping selected">$ <input type="text" class="tb price" name="tShippingFlatRate" id="tShippingFlatRate" maxlength="10" /></span></p>
					<p><input type="radio" class="rb-shipping" name="rShippingMethod" id="rShippingMethodPercentage" value="Percentage" /> <label for="tShippingPercentage"><?php echo _('Percentage'); ?></label> &nbsp;<span class="additional-shipping" style="visibility:hidden">% <input type="text" class="tb price" name="tShippingPercentage" id="tShippingPercentage" maxlength="10" /></span></p>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo _('Protection'); ?>:</td>
				<td>
					<p style="padding-bottom:7px"><input type="radio" class="rb-protection" name="rProtectionMethod" id="rProtectionMethodFlatRate" value="Flat Rate" checked="checked" /> <label for="tProtectionFlatRate"><?php echo _('Flate Rate'); ?></label> &nbsp; &nbsp;<span class="protection selected">$ <input type="text" class="tb price"name="tProtectionFlatRate" id="tProtectionFlatRate" maxlength="10" /></span></p>
					<p><input type="radio" class="rb-protection" name="rProtectionMethod" id="rProtectionMethodPercentage" value="Percentage" /> <label for="tProtectionPercentage"><?php echo _('Percentage'); ?></label> &nbsp;<span class="protection" style="visibility:hidden">% <input type="text" class="tb price" name="tProtectionPercentage" id="tProtectionPercentage" maxlength="10" /></span></p>
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
					<?php 
					if ( is_array( $coupons ) )
					foreach ( $coupons as $c ) {
					?>
					<option value="<?php echo $c['website_coupon_id']; ?>"><?php echo $c['name']; ?></option>
					<?php } ?>
				</select>
				<a href="javascript:;" id="aAddCoupon" title="<?php echo _('Add Coupon'); ?>"><?php echo _('Add Coupon'); ?>...</a>
				<input type="hidden" name="hCoupons" id="hCoupons" />
			</tr>
		</table>
	</div>
	<div class="float-right text-right">
		<input type="submit" class="button" value="<?php echo _('Save'); ?>" />
		<input type="button" class="button close" value="<?php echo _('Cancel'); ?>" />
	</div>
	<br clear="right" />

	<input type="hidden" id="hProductID" name="hProductID" />
	<?php nonce::field( 'update-product' , '_ajax_update_product' ); ?>
	</form>
	<input type="hidden" id="dDialogHeight" value="<?php echo ( $user['website']['shopping_cart'] ) ? 500 : 350; ?>" />
</div>

<?php get_footer(); ?>