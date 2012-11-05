<?php
/**
 * @page Shipping Settings
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate class
$w = new Websites;

// Get settings
$free_shipping = $w->get_settings( 'free-shipping-quantity' );

javascript( 'shopping-cart/shipping' );

$title = _('Shopping Cart - Shipping Settings') . ' | ' . TITLE;
$page = 'shipping';
get_header();
?>

<div id="content">
	<h1><?php echo _('Shipping'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'shipping' ); ?>
	<div id="subcontent">
	
		<div id="dFreeShippingQuantity">
			<input id="cbEnableFreeShipping" name="cbEnableFreeShipping" type="checkbox" <?php echo( $free_shipping['free-shipping-quantity'] ) ? 'checked="checked"' : ''; ?> /> 
			<?php echo _('Enable free shipping for purchases of'); ?>&nbsp;
			<input id="tFreeShippingQuantity" name="tFreeShippingQuantity" type="text" class="tb" style="width:100px;" value="<?php echo ( $free_shipping['free-shipping-quantity'] ) ? $free_shipping['free-shipping-quantity'] : ''; ?>" />
			&nbsp;<?php echo _('or more items'); ?>.<br/>
		</div>
		<br /><br />
		<br /><br />
		
		<table id="tWebsiteShippingMethods" ajax="/ajax/shopping-cart/shipping/list/" sort="1" perPage="20,40,60" width="100%">
			<thead>
				<tr>
					<th sort="1"><?php echo _('Name'); ?></th>
					<th><?php echo _('Type'); ?></th>
					<th><?php echo _('Method'); ?></th>
					<th><?php echo _('Amount'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<?php nonce::field( 'set-free-shipping', 'free_shipping_nonce' ); ?>
    </div>
	<br /><br />
</div>

<?php get_footer(); ?>