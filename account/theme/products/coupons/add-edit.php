<?php
/**
 * @page Add Edit Coupon
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$c = new Coupons;
$s = new Shopping_Cart;
$v = new Validator;

// Get the coupon id if there is one
$website_coupon_id = (int) $_GET['wcid'];

$v->form_name = 'fAddEditCoupon';
$v->add_validation( 'tName', 'req', _('The "Name" field is required') );
$v->add_validation( 'tCode', 'req', _('The "Code" field is required') );
$v->add_validation( 'tItemLimit', 'int', _('The "Item Limit" field may only contain a number') );

// Add validation
add_footer( $v->js_validation() );

// Make sure it's a valid request
if ( nonce::verify( $_POST['_nonce'], 'add-edit-coupon' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $website_coupon_id ) {
			// Update coupon
			$success = $c->update( $website_coupon_id, $_POST['tName'], $_POST['tCode'], $_POST['rType'], $_POST['tAmount'], $_POST['tMinimumPurchaseAmount'], $_POST['cbStoreWide'], $_POST['cbBuyOneGetOneFree'], $_POST['tItemLimit'], $_POST['tStartDate'], $_POST['tEndDate'], $_POST['cbFreeShippingMethods'] );
		} else {
			// Create coupon
			$success = $c->create( $_POST['tName'], $_POST['tCode'], $_POST['rType'], $_POST['tAmount'], $_POST['tMinimumPurchaseAmount'], $_POST['cbStoreWide'], $_POST['cbBuyOneGetOneFree'], $_POST['tItemLimit'], $_POST['tStartDate'], $_POST['tEndDate'], $_POST['cbFreeShippingMethods'] );
		}
	}
}

// Get the email list if necessary
if ( $website_coupon_id ) {
	$coupon = $c->get( $website_coupon_id );
	$free_shipping_methods = $c->get_free_shipping_methods( $website_coupon_id );
}

$shipping_methods = $s->get_shipping_methods();

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
javascript( 'products/coupons/add-edit' );

$selected = "products";
$sub_title = ( $website_coupon_id ) ? _('Edit Coupon') : _('Add Coupon');
$title = "$sub_title | " . _('Product Catalog') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'coupons' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $website_coupon_id ) ? _('Your coupon has been updated successfully!') : _('Your coupon has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/products/coupons/" title="', _('Coupons'), '">', _('view your coupons'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$website_coupon_id )
			$website_coupon_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditCoupon" action="/products/coupons/add-edit/?wcid=<?php echo $website_coupon_id; ?>" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
					<td><input type="text" class="tb" name="tName" id="tName" value="<?php echo ( !$success && $website_coupon_id && empty( $_POST['tName'] ) ) ? $coupon['name'] : $_POST['tName']; ?>" maxlength="50" /></td>
				</tr>
				<tr>
					<td><label for="tCode"><?php echo _('Code'); ?>:</label></td>
					<td><input type="text" class="tb" name="tCode" id="tCode" value="<?php echo ( !$success && $website_coupon_id && empty( $_POST['tCode'] ) ) ? $coupon['code'] : $_POST['tCode']; ?>" maxlength="20" /></td>
				</tr>
				<tr>
					<td><label for="rType">Type:</label></td>
					<td>
						<?php 
						$coupon_type = ( !$success && $website_coupon_id && empty( $_POST['rType'] ) ) ? $coupon['type'] : $_POST['rType'];
						
						if ( 'Flat Rate' == $coupon_type ) {
							$flat_rate = true;
							$percentage = false;
						} else {
							$flat_rate = false;
							$percentage = true;
						}
						
						// @Fix Flat Rate uses the english lange -- need to make it language independent
						?>
						<p style="padding-bottom:7px"><input type="radio" name="rType" id="rType" class="rb" value="Flat Rate"<?php if ( $flat_rate ) echo ' checked="checked"'; ?> /> <?php echo _('Dollar Amount'); ?></p>
						<p><input type="radio" name="rType" class="rb" value="Percentage"<?php if ( $percentage ) echo ' checked="checked"'; ?> /> <?php echo _('Percentage'); ?></p>
					</td>
				</tr>
				<tr>
					<td><label for="tAmount"><?php echo _('Amount Discounted'); ?>:</label></td>
					<td><input type="text" class="tb" name="tAmount" id="tAmount" value="<?php echo ( !$success && $website_coupon_id && empty( $_POST['tAmount'] ) ) ? $coupon['amount'] : $_POST['tAmount']; ?>" maxlength="20" /></td>
				</tr>
				<tr>
					<td><label for="tMinimumPurchaseAmount"><?php echo _('Minimum Purchase'); ?>:</label></td>
					<td><input type="text" class="tb" name="tMinimumPurchaseAmount" id="tMinimumPurchaseAmount" value="<?php echo ( !$success && $website_coupon_id && empty( $_POST['tMinimumPurchaseAmount'] ) ) ? $coupon['minimum_purchase_amount'] : $_POST['tMinimumPurchaseAmount']; ?>" maxlength="20" /></td>
				</tr>
				<tr>
					<td><label for="cbStoreWide"><?php echo _('Store-Wide'); ?>:</label></td>
					<td><input type="checkbox" name="cbStoreWide" id="cbStoreWide" class="cb" value="1"<?php if ( $success && $_POST['cbStoreWide'] || !$success && $coupon['store_wide'] ) echo ' checked="checked"'; ?> /> <?php echo _('Store-Wide Coupon?'); ?></td>
				</tr>
				<tr>
					<td><label for="cbBuyOneGetOneFree"><?php echo _('Buy One Get One Free'); ?>:</label></td>
					<td><input type="checkbox" name="cbBuyOneGetOneFree" id="cbBuyOneGetOneFree" class="cb" value="1"<?php if ( $success && $_POST['cbBuyOneGetOneFree'] || !$success && $coupon['buy_one_get_one_free'] ) echo ' checked="checked"'; ?> /> <?php echo _('Buy One Get One Free'); ?></td>
				</tr>
				<tr>
					<td><label for="cbFreeShippingOptions"><?php echo _('Free Shipping Methods'); ?>:</label></td>
					<td>
						<?php
						$check_shipping_methods = ( !$success && $website_coupon_id ) ? $free_shipping_methods : $_POST['cbFreeShippingMethods'];
						
						foreach( $shipping_methods as $method ) {
							$checked = ( in_array( $method['website_shipping_method_id'], $check_shipping_methods ) ) ? ' checked="checked"' : '';
							
							echo '<p><input type="checkbox" class="cb" name="cbFreeShippingMethods[]" id="cbFreeShippingMethod' . $method['website_shipping_method_id'] . '" value="' . $method['website_shipping_method_id'] . '"' . $checked . '> <label for="cbFreeShippingMethod' . $method['website_shipping_method_id'] . '">' . $method['name'] . '</label></p>';
						}
						?>
					</td> 
				</tr>
				<tr>
					<td><label for="tItemLimit"><?php echo _('Item Limit'); ?>:</label></td>
					<td><input type="text" class="tb" name="tItemLimit" id="tItemLimit" maxlength="10" value="<?php echo ( !$success && $website_coupon_id && empty( $_POST['tItemLimit'] ) ) ? $coupon['item_limit'] : $_POST['tItemLimit']; ?>" /></td>
				</tr>
				<tr>
					<td>
						<label for="tStartDate"><?php echo _('Start &amp; End Date'); ?>:</label><br />
						<small>(<?php echo _('optional'); ?>)</small>
					</td>
					<td>
						<input type="text" name="tStartDate" id="tStartDate" class="tb date" maxlength="10" value="<?php echo ( ( !$success && $website_coupon_id && empty( $_POST['tStartDate'] ) ) && '0000-00-00' != $coupon['date_start'] ) ? $coupon['date_start'] : $_POST['tStartDate']; ?>" style="width:75px" />
						<input type="text" name="tEndDate" id="tEndDate" class="tb date" maxlength="10" value="<?php echo ( ( !$success && $website_coupon_id && empty( $_POST['tEndDate'] ) ) && '0000-00-00' != $coupon['date_end'] ) ? $coupon['date_end'] : $_POST['tEndDate']; ?>" style="width:75px" />
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo ( $website_coupon_id ) ? _('Update Coupon') : _('Add Coupon'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field('add-edit-coupon'); ?>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>