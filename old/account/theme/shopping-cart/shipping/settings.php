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

// Instantiate Classes
$w = new Websites;
$v = new Validator;

$v->form_name = 'fShippingSettings';
$v->add_validation( 'shipper-zip', 'zip', _('The "Shipping Zip" field must contain a valid zip code') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'shipping-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) )
		$success = $w->update_settings( array( 
			'shipping-settings' => serialize( $_POST['generic_settings'] )
			, 'shipping-ups' => serialize( $_POST['ups'] )
			, 'shipping-fedex' => serialize( $_POST['fedex'] )
			, 'shipping-usps' => serialize( $_POST['usps'] )
            , 'taxable-shipping' => ( isset( $_POST['taxable-shipping'] ) ) ? '1' : '0'
		) );
}

// Get the settings
$settings = $w->get_settings( 'shipping-settings', 'shipping-ups', 'shipping-fedex', 'shipping-usps', 'taxable-shipping' );

$generic_settings = unserialize( $settings['shipping-settings'] );
$ups = unserialize( $settings['shipping-ups'] );
$fedex = unserialize( $settings['shipping-fedex'] );
$usps = unserialize( $settings['shipping-usps'] );

$title = _('Settings') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'settings';
get_header();
?>

<div id="content">
	<h1><?php echo _('Shipping Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'shipping' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<p class="success"><?php echo _('Shipping settings successfully saved!'); ?></p>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='error'>$errs</p>";
		?>
        <form name="fShippingSettings" id="fShippingSettings" action="/shopping-cart/shipping/settings/" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
			<tr><td class="title" colspan="2"><strong><?php echo _('Generic Settings'); ?></strong></td></tr>
            <tr>
                <td width="200"><label for="shipper-company"><?php echo _('Shipper Company'); ?>:</label></td>
                <td><input type="text" class="tb" name="generic_settings[shipper_company]" id="shipper-company" value="<?php echo $generic_settings['shipper_company']; ?>" maxlength="100" /></td>
            </tr>
            <tr>
                <td><label for="shipper-contact"><?php echo _('Shipper Contact'); ?>:</label></td>
                <td><input type="text" class="tb" name="generic_settings[shipper_contact]" id="shipper-contact" value="<?php echo $generic_settings['shipper_contact']; ?>" maxlength="100" /></td>
            </tr>
            <tr>
                <td><label for="shipper-address"><?php echo _('Shipper Address'); ?>:</label></td>
                <td><input type="text" class="tb" name="generic_settings[shipper_address]" id="shipper-address" value="<?php echo $generic_settings['shipper_address']; ?>" maxlength="100" /></td>
            </tr>
            <tr>
                <td><label for="shipper-city"><?php echo _('Shipper City'); ?>:</label></td>
                <td><input type="text" class="tb" name="generic_settings[shipper_city]" id="shipper-city" value="<?php echo $generic_settings['shipper_city']; ?>" maxlength="100" /></td>
            </tr>
            <tr>
                <td><label for="shipper-state"><?php echo _('Shipper State/Province'); ?>:</label></td>
                <td><input type="text" class="tb" name="generic_settings[shipper_state]" id="shipper-state" value="<?php echo $generic_settings['shipper_state']; ?>" maxlength="100" /></td>
            </tr>
			<tr>
                <td><label for="shipper-zip"><?php echo _('Shipper Zip'); ?>:</label></td>
                <td><input type="text" class="tb" name="generic_settings[shipper_zip]" id="shipper-zip" value="<?php echo $generic_settings['shipper_zip']; ?>" maxlength="10" /></td>
            </tr>
			<tr>
                <td><label for="shipper-country"><?php echo _('Shipper Country'); ?>:</label></td>
                <td>
					<select name="generic_settings[shipper_country]" id="shipper-country">
						<?php data::countries( true, $generic_settings['shipper_country'] ); ?>
					</select>
				</td>
            </tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td class="title" colspan="2"><strong><?php echo _('UPS Settings'); ?></strong></td></tr>
			<tr>
                <td><label for="ups-access-key"><?php echo _('UPS Access Key'); ?>:</label></td>
                <td><input type="text" class="tb" name="ups[access_key]" id="ups-access-key" value="<?php echo $ups['access_key']; ?>" maxlength="100" /></td>
            </tr>
			<tr>
                <td><label for="ups-username"><?php echo _('UPS Username'); ?>:</label></td>
                <td><input type="text" class="tb" name="ups[username]" id="ups-username" value="<?php echo $ups['username']; ?>" maxlength="30" /></td>
            </tr>
			<tr>
                <td><label for="ups-password"><?php echo _('UPS Password'); ?>:</label></td>
                <td><input type="password" class="tb" name="ups[password]" id="ups-password" value="<?php echo $ups['password']; ?>" maxlength="30" /></td>
            </tr>
			<tr>
                <td><label for="ups-account-number"><?php echo _('UPS Account Number'); ?>:</label></td>
                <td><input type="text" class="tb" name="ups[account_number]" id="ups-account-number" value="<?php echo $ups['account_number']; ?>" maxlength="32" /></td>
            </tr>
			<tr>
                <td><label><?php echo _('Weight unit'); ?>:</label></td>
                <td><?php echo _('Pounds'); ?></td>
            </tr>
			<tr>
                <td><label><?php echo _('Length unit'); ?>:</label></td>
                <td><?php echo _('Inches'); ?></td>
            </tr>
			<?php /*<tr>
                <td><label for="ups-pickup-type"><?php echo _('Pickup Type'); ?>:</label></td>
                <td>
					<select name="ups[pickup_type]" id="ups-pickup-type">
						<option value="">-- <?php echo _('Select One'); ?> --</option>
						<?php
						$pickup_types = array(
							'01' => _('Daily Pickup')
							, '03' => _('Customer Counter')
							, '06' => _('One Time Pickup')
							, '07' => _('On Call Air')
						);
						
						foreach ( $pickup_types as $pt => $pt_name ) {
							$selected = ( $ups['pickup_type'] == $pt ) ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $pt; ?>"<?php echo $selected; ?>><?php echo $pt_name; ?></option>
						<?php } ?>
					</select>
				</td>
            </tr>
			<tr>
                <td><label for="ups-residential"><?php echo _('Residential'); ?>:</label></td>
                <td>
					<select name="ups[residential]" id="ups-residential">
						<option value="1"><?php echo _('Yes'); ?></option>
						<option value="0"<?php if ( '0' == $ups['residential'] ) echo ' selected="selected"'; ?>><?php echo _('No'); ?></option>
					</select>
				</td>
            </tr>
			<tr>
                <td><label><?php echo _('Insured Currency'); ?>:</label></td>
                <td><?php echo _('USD'); ?><input type="hidden" name="ups[insured-currency]" value="USD" /></td>
            </tr>
			*/ ?>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td class="title" colspan="2"><strong><?php echo _('FedEx Settings'); ?></strong></td></tr>
			<tr>
                <td><label for="fedex-development-key"><?php echo _('FedEx Development Key'); ?>:</label></td>
                <td><input type="text" class="tb" name="fedex[development_key]" id="fedex-development-key" value="<?php echo $fedex['development_key']; ?>" maxlength="100" /></td>
            </tr>
			<tr>
                <td><label for="fedex-password"><?php echo _('FedEx Password'); ?>:</label></td>
                <td><input type="password" class="tb" name="fedex[password]" id="fedex-password" value="<?php echo $fedex['password']; ?>" maxlength="30" /></td>
            </tr>
			<tr>
                <td><label for="fedex-account-number"><?php echo _('FedEx Account Number'); ?>:</label></td>
                <td><input type="text" class="tb" name="fedex[account_number]" id="fedex-account-number" value="<?php echo $fedex['account_number']; ?>" maxlength="100" /></td>
            </tr>
			<tr>
                <td><label for="fedex-meter-number"><?php echo _('FedEx Meter Number'); ?>:</label></td>
                <td><input type="text" class="tb" name="fedex[meter_number]" id="fedex-meter-number" value="<?php echo $fedex['meter_number']; ?>" maxlength="100" /></td>
            </tr>
			<?php /*<tr>
                <td><label for="fedex-packaging-type"><?php echo _('Packaging Type'); ?>:</label></td>
                <td>
					<select name="fedex[packaging_type]" id="fedex-packaging-type">
						<?php
						$packaging_types = array(
							'YOUR_PACKAGING' => _('Your Packaging')
							, 'FEDEX_BOX' => _('FedEx Box')
							, 'FEDEX_ENVELOPE' => _('FedEx Envelope')
							, 'FEDEX_PAK' => _('FedEx Pak')
							, 'FEDEX_TUBE' => _('FedEx Tube')
							, 'FEDEX_10KG_BOX' => _('FedEx 10Kg Box')
							, 'FEDEX_25KG_BOX' => _('FedEx 25Kg Box')
						);
						
						foreach ( $packaging_types as $pt => $pt_name ) {
							$selected = ( $fedex['packaging_type'] == $pt ) ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $pt; ?>"<?php echo $selected; ?>><?php echo $pt_name; ?></option>
						<?php } ?>
					</select>
				</td>
            </tr>*/ ?>
			<tr>
                <td><label><?php echo _('Weight unit'); ?>:</label></td>
                <td><?php echo _('Pounds'); ?></td>
            </tr>
			<tr>
                <td><label><?php echo _('Length unit'); ?>:</label></td>
                <td><?php echo _('Inches'); ?></td>
            </tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td class="title" colspan="2"><strong><?php echo _('USPS Settings'); ?></strong></td></tr>
			<tr>
                <td><label for="usps-username"><?php echo _('USPS Development Username'); ?>:</label></td>
                <td><input type="text" class="tb" name="usps[username]" id="usps-username" value="<?php echo $usps['username']; ?>" maxlength="30" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td class="title" colspan="2"><strong><?php echo _('Tax Settings'); ?></strong></td></tr>
            <tr>
                 <td>&nbsp;</td>
                 <td><input type="checkbox" class="cb" name="taxable-shipping" id="taxable-shipping" value="1"<?php if ( '1' == $settings['taxable-shipping'] ) echo ' checked="checked"'; ?> /> <label for="taxable-shipping"><?php echo _('Taxable Shipping'); ?>:</label></td>
             </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" class="button" value="<?php echo _('Update Settings'); ?>" /></td>
            </tr>
        </table>
		<?php nonce::field('shipping-settings'); ?>
        </form>
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>