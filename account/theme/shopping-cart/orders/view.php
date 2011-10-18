<?php
/**
 * @page View Order
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$sc = new Shopping_Cart;

$oid = (int) $_GET['oid'];
$order = $sc->get_order( $oid, $user['website']['website_id'] );

css( 'shopping-cart/orders/view' );
javascript( 'shopping-cart/orders/view' );

$title = _('View Order') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'view-order';
get_header();
?>

<div id="content">
	<h1><?php echo _('Order Receipt') . "#$oid"; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'orders' ); ?>
	<div id="subcontent">
		<?php if ( !empty( $order['email'] ) ) { ?>
			<p><strong><?php echo _('Email'); ?>:</strong> <?php echo $order['email']; ?></p>
		<?php 
		} 
		
		if ( !empty( $order['phone'] ) ) {
		?>
        	<p><strong><?php echo _('Phone'); ?>:</strong> <?php echo $order['phone']; ?></p>
        <?php } ?>
		<p id="pShipping">
			<strong><?php echo _('Shipping Information:'); ?></strong><br />
			<?php echo $order['shipping_first_name'], ' ', $order['shipping_last_name']; ?><br />
			<?php echo $order['shipping_address1']; ?><br />
			<?php echo $order['shipping_city'], ', ', $order['shipping_state'], ' ', $order['shipping_zip']; ?><br />
			<br />
            <?php if ( isset( $order['billing_phone'] ) ) { ?>
            <strong><?php echo _('Contact Info'); ?>:</strong><br />
            <?php echo _('Main Phone'), ': ', $order['billing_phone']; ?><br />
           	<?php if ( isset( $order['billing_alt_phone'] ) ) echo _('Alternate Phone'), ': ', $order['billing_alt_phone']; ?><br />
            <br />
            <?php } ?>
			<strong><?php echo _('Shipping Method');?>:</strong> <?php echo $order['shipping_method']; ?>
			<br />
			<label for="sStatus"><?php echo _('Status'); ?></label>:
			<select name="sStatus" id="sStatus">
				<option value="0"<?php if ( '0' == $order['status'] ) echo ' selected="selected"'; ?>><?php echo _('Purchased'); ?></option>
				<option value="1"<?php if ( '1' == $order['status'] ) echo ' selected="selected"'; ?>><?php echo _('Pending'); ?></option>
				<option value="2"<?php if ( '2' == $order['status'] ) echo ' selected="selected"'; ?>><?php echo _('Delivered'); ?></option>
				<option value="-1"<?php if ( '-1' == $order['status'] ) echo ' selected="selected"'; ?>><?php echo _('Declined'); ?></option>
			</select>
			<input type="hidden" id="hOrderID" value="<?php echo $order['website_order_id']; ?>" />
			<?php nonce::field( 'update-order-status' ); ?>
		</p><br/><br/>
		<table cellpadding="0" cellspacing="0" id="tPricing" width="500">
			<tr>
				<td width="20%" style="font-weight:bold;" valign="top">
					<?php echo _('Subtotal'); ?>:<br />
					<?php echo _('Tax'); ?>:<br />
					<?php echo _('Shipping'); ?>:<br />
					
					<?php if ( $order['website_coupon_id'] ) echo _('Coupon Discount'), ':<br />'; ?>
					<br />
					<strong><?php echo _('Total'); ?>:</strong>
				</td>
				<td width="10%" valign="top">
					$<?php echo number_format( $order['total_cost'] + $order['coupon_discount'] - $order['shipping_price'] - $order['tax_price'], 2 ); ?><br />
					$<?php echo number_format( $order['tax_price'], 2 ); ?><br />
					$<?php echo number_format( $order['shipping_price'], 2 ); ?><br />
					<?php if ( $order['website_coupon_id'] ) echo '-$', number_format( $order['coupon_discount'], 2 ), "<br />\n"; ?>
					<br />
					<span style="font-decoration:underline;">$<?php echo number_format( $order['total_cost'], 2 ); ?></span>
				</td>
			</tr>
		</table>
		<br clear="all" /><br/>
		<h2><?php echo _('Order Summary'); ?></h2>
		<table cellpadding="0" cellspacing="0" id="tItemList">
			<tr>
				<th width="123"><strong><?php echo _('Image'); ?></strong></th>
				<th width="435"><strong><?php echo _('Name / Description'); ?></strong></th>
				<th width="70"><strong><?php echo _('Price'); ?></strong></th>
				<th width="70"><strong><?php echo _('Qty'); ?></strong></th>
				<th width="77"><strong><?php echo _('Total'); ?></strong></th>
			</tr>
			<!-- End: Table Heading -->
			<?php foreach ( $order['items'] as $website_order_item_id => $item ) { ?>
			<tr>
				<td class="product"><img src="<?php echo $item['image'] ?>" width="80" alt="<?php echo $item['name']; ?>" /></td>
				<td class="description">
					<strong<?php if ( !$item['status'] ) echo ' class="out-of-stock"'; ?>><?php echo $item['name']; ?></strong>
					<?php 
					if ( !$item['status'] )
						echo '<p class="out-of-stock">(', _('Out of Stock'), ')</p>';
					?>
					<p><?php echo _('SKU'); ?>: <?php echo $item['sku']; ?></p>
					<?php if ( !empty( $item['store_sku'] ) ) echo _('<p>Store SKU: ') , $item['store_sku'], '</p>'; ?>
					<br />
					<?php
					$additional_price = 0;
					// Make sure there are options
					if ( ( count( $item['product_options'] ) > 0 ) || ( count( $item['extra'] ) > 0 ) ) {
					?>
						<a href="javascript:;" id="aExpandOptions<?php echo $website_order_item_id; ?>" class="expand-options" title="Show Options"><strong><span id="sExpandOptions<?php echo $website_order_item_id; ?>">[ + ]</span> Options</strong></a><br />
						<div id="dOptions<?php echo $website_order_item_id; ?>" class="options hidden">
						<br />
						<?php
						if ( is_array( $item['product_options'] ) )
						foreach ( $item['product_options'] as $po_id => $po ) { // Loop through the product options							
							
							switch ( $po['option_type'] ) {
								// If it's a checkbox
								case 'checkbox':
									echo '<p>', $po['option_name'];
									
									if ( $po['price'] != 0 )
										echo ' ($', number_format( $po['price'], 2 ), ')';
									
									echo '</p>';
								break;
								
								// If it's a dropdown
								case 'select':
									echo '<p>', $po['option_name'], ' - ', $po['list_item_value'];
							
									if ( $po['price'] != 0 )
										echo ' ($', number_format( $po['price'], 2 ), ')';
									
									echo '</p>';
								break;
							}
							
							$additional_price += $po['price'];
						}
						
						if ( 0 != $item['protection_price'] )
							echo '<p>Protection ($', number_format( $item['protection_price'], 2 ), ')</p>';
							
						if ( is_array( $item['extra'] ) && count( $item['extra'] ) > 0 )
						foreach ( $item['extra'] as $name => $value ) {
							echo '<p>' . ucwords( $name ) . ": " . $value . '</p>';								
						}
						?>
					</div>
					<?php } ?>
				</td>
				<td class="price"><strong>$<?php $item_price = $item['price'] + $item['protection_price'] + $additional_price; echo number_format( $item_price, 2 ); ?></strong></td>
				<td class="qty"><strong><?php echo number_format( $item['quantity'] ); ?></strong></td>
				<td class="total"><strong>$<?php echo number_format( $item_price * $item['quantity'], 2 ); ?></strong></td>
			</tr>
		<?php } ?>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>