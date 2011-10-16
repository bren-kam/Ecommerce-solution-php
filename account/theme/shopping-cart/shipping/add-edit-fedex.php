<?php
/**
 * @page Shipping - Add/Edit FedEx Shipping Method
 * @package Imagine Retailer
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
$website_shipping_method_id = (int) $_GET['wsmid'];

$settings = $w->get_settings( 'shipping-fedex' );
$settings = unserialize( $settings['shipping-fedex'] );

// Modify the settings
if ( nonce::verify( $_POST['_nonce'], 'add-edit-fedex-shipping-method' ) ) {
	if ( $website_shipping_method_id ) {
		$success = $sc->update_shipping_method( $website_shipping_method_id, $_POST['sService'], 'N/A', 'N/A', $_POST['extra'] );
	} else {
		$success = $sc->add_shipping_method( 'fedex', $_POST['sService'], 'N/A', 'N/A', $_POST['extra'] );
	}
}

// Get shipping methods
$shipping_method = $sc->get_shipping_method( $website_shipping_method_id );

$sub_title = ( $website_shipping_method_id ) ? _('Edit FedEx') : _('Add FedEx');
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
        <?php if( empty( $settings ) || in_array( '', $settings ) ) { ?>
        <p class="error">You must set up your Fedex Account before adding Fedex shipping methods.</p>
        <p><a href="/shopping-cart/shipping/settings/">Click here</a> to set up your Fedex account information.</p>
        <?php } else { ?>
        <form name="fAddEditShipping" id="fAddEditShipping" action="/shopping-cart/shipping/add-edit-fedex/<?php if ( !empty( $website_shipping_method_id ) ) echo "?wsmid=$website_shipping_method_id"; ?>" method="post">
            <table class="form">
                <tr>
                    <td><label for="sService"><?php echo _('Service:'); ?></label></td>
                    <td>
                       <select id="sService" name="sService">
					   <?php
							$services = array(
								'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => _('Europe First International Priority')
								, 'FEDEX_1_DAY_FREIGHT' => _('FedEx 1 Day Freight')
								, 'FEDEX_1_DAY_FREIGHT' => _('FedEx 1 Day Freight')
								, 'FEDEX_2_DAY' => _('FedEx 2 Day')
								, 'FEDEX_2_DAY_FREIGHT' => _('FedEx 2 Day Freight')
								, 'FEDEX_3_DAY_FREIGHT' => _('FedEx 3 Day Freight')
								, 'FEDEX_EXPRESS_SAVER' => _('FedEx Express Saver')
								, 'FEDEX_GROUND' => _('FedEx Ground')
								, 'FIRST_OVERNIGHT' => _('First Overnight')
								, 'GROUND_HOME_DELIVERY' => _('Ground Home Delivery')
								, 'INTERNATIONAL_ECONOMY' => _('International Economy')
								, 'INTERNATIONAL_ECONOMY_FREIGHT' => _('International Economy Freight')
								, 'INTERNATIONAL_FIRST' => _('International First')
								, 'INTERNATIONAL_PRIORITY' => _('International Priority')
								, 'INTERNATIONAL_PRIORITY_FREIGHT' => _('International Priority Freight')
								, 'PRIORITY_OVERNIGHT' => _('Priority Overnight')
								, 'SMART_POST' => _('Smart Post')
								, 'STANDARD_OVERNIGHT' => _('Standard Overnight')
								, 'FEDEX_FREIGHT' => _('FedEx Freight')
								, 'FEDEX_NATIONAL_FREIGHT' => _('FedEx National Freight')
							);
							
							foreach( $services as $sv => $s ) {
								$selected = ( $shipping_method['name'] == $sv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $sv; ?>"<?php echo $selected; ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
				<tr>
                    <td><label for="sPackagingType"><?php echo _('Packaging Type:'); ?></label></td>
                    <td>
                       <select id="sPackagingType" name="extra[packaging_type]">
					   <?php
							$sizes = array(
								'YOUR_PACKAGING' => _('Your Packaging')
								, 'FEDEX_BOX' => _('FedEx Box')
								, 'FEDEX_ENVELOPE' => _('FedEx Evelope')
								, 'FEDEX_PAK' => _('FedEx Pak')
								, 'FEDEX_TUBE' => _('FedEx Tube')
								, 'FEDEX_10KG_BOX' => _('FedEx 10Kg Box')
								, 'FEDEX_25KG_BOX' => _('FedEx 25Kg Box')
							);
							
							foreach( $sizes as $sv => $s ) {
								$selected = ( $shipping_method['extra']['packaging_type'] == $sv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $sv; ?>"<?php echo $selected; ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
			</table>
            <br/><br/>
			<input type="submit" class="button" value="<?php echo ( $website_shipping_method_id ) ? _('Update Shipping Method') : _('Add Shipping Method'); ?>" />
			<?php nonce::field('add-edit-fedex-shipping-method'); ?>
		</form>	
        <?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>