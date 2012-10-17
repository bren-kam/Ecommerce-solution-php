<?php
/**
 * @page Shipping - Add/Edit UPS Shipping Method
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$sc = new Shopping_Cart;
$w = new Websites;

// Determine website shipping method id
$website_shipping_method_id = ( isset( $_GET['wsmid'] ) ) ? $_GET['wsmid'] : '';

$settings = $w->get_settings( 'shipping-ups' );
$settings = unserialize( $settings['shipping-ups'] );

// Define it for everything else
$success = false;

// Modify the settings
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-ups-shipping-method' ) ) {
	if ( $website_shipping_method_id ) {
		$success = $sc->update_shipping_method( $website_shipping_method_id, $_POST['sService'], 'N/A', 'N/A', $_POST['extra'] );
	} else {
		$success = $sc->add_shipping_method( 'ups', $_POST['sService'], 'N/A', 'N/A', $_POST['extra'] );
	}
}

// Get shipping methods
$shipping_method = $sc->get_shipping_method( $website_shipping_method_id );

$sub_title = ( $website_shipping_method_id ) ? _('Edit UPS') : _('Add UPS');
$title = $sub_title . ' ' . _('Shipping Method') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'shipping';
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title . ' ' . _('Shipping Method'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'shipping' ); ?>
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<p class="success"><?php echo ( $website_shipping_method_id ) ? _('Your shipping method has been updated successfully!') : _('Your shipping method has been added successfully!'); ?></p>
		<?php 
		}
		
		// Assign shipping method id
		if ( $success && !$website_shipping_method_id )
			$website_shipping_method_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='error'>$errs</p>";
		?>
        <?php if ( empty( $settings ) || in_array( '', $settings ) ) { ?>
        <p class="error">You must set up your UPS Account before adding UPS shipping methods.</p>
        <p><a href="/shopping-cart/shipping/settings/">Click here</a> to set up your UPS account information.</p>
        <?php } else { ?>
        <form name="fAddEditShipping" id="fAddEditShipping" action="/shopping-cart/shipping/add-edit-ups/<?php if ( !empty( $website_shipping_method_id ) ) echo "?wsmid=$website_shipping_method_id"; ?>" method="post">
            <table class="form">
                <tr>
                    <td><label for="sService"><?php echo _('Service:'); ?></label></td>
                    <td>
                       <select id="sService" name="sService">
					   <?php
							$services = array(
								'02' => _('UPS Second Day Air')
								, '03' => _('UPS Ground')
								, '07' => _('UPS Worldwide Express')
								, '08' => _('UPS Worldwide Expedited')
								, '11' => _('UPS Standard')
								, '12' => _('UPS Three-Day Select')
								, '13' => _('Next Day Air Saver')
								, '14' => _('UPS Next Day Air Early AM')
								, '54' => _('UPS Worldwide Express Plus')
								, '59' => _('UPS Second Day Air AM')
								, '65' => _('UPS Saver')
							);
							
							foreach ( $services as $sv => $s ) {
								$selected = ( $shipping_method['name'] == $sv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $sv; ?>"<?php echo $selected; ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
				<tr>
                    <td><label for="sPickupType"><?php echo _('Pickup Type:'); ?></label></td>
                    <td>
                       <select id="sPickupType" name="extra[pickup_type]">
					   <?php
							$pickup_types = array(
								'01' => _('Daily Pickup')
								, '03' => _('Customer Counter')
								, '06' => _('One Time Pickup')
								, '07' => _('On Call Air')
								);
							
							foreach ( $pickup_types as $ptv => $pt ) {
								$selected = ( $shipping_method['extra']['pickup_type'] == $ptv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $ptv; ?>"<?php echo $selected; ?>><?php echo $pt; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
				<tr>
                    <td><label for="sPackagingType"><?php echo _('Packaging Type:'); ?></label></td>
                    <td>
                       <select id="sPackagingType" name="extra[packaging_type]">
					   <?php
							$packaging_types = array(
								'01' => 'UPS Letter'
								, '02' => 'Your Packaging'
								, '03' => 'Tube'
								, '04' => 'PAK'
								, '21' => 'Express Box'
								, '24' => '25KG Box'
								, '25' => '10KG Box'
								, '30' => 'Pallet'
								, '2a' => 'Small Express Box'
								, '2b' => 'Medium Express Box'
								, '2c' => 'Large Express Box'
							);
							
							foreach ( $packaging_types as $ptv => $pt ) {
								$selected = ( $shipping_method['extra']['packaging_type'] == $ptv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $ptv; ?>"<?php echo $selected; ?>><?php echo $pt; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
			</table>
            <br/><br/>
			<input type="submit" class="button" value="<?php echo ( $website_shipping_method_id ) ? _('Update Shipping Method') : _('Add Shipping Method'); ?>" />
			<?php nonce::field('add-edit-ups-shipping-method'); ?>
		</form>	
        <?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>