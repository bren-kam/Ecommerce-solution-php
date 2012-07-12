<?php
/**
 * @page Shipping - Add/Edit USPS Shipping Method
 * @package Grey Suit Retail
 */

// @fix need to get this working before making the page visible
url::redirect( '/shopping-cart/shipping/' );

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

$settings = $w->get_settings( 'shipping-usps' );
$settings = unserialize( $settings['shipping-usps'] );

// Modify the settings
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-usps-shipping-method' ) ) {
	if ( $website_shipping_method_id ) {
		$success = $sc->update_shipping_method( $website_shipping_method_id, $_POST['sService'], 'N/A', 'N/A', $_POST['extra'] );
	} else {
		$success = $sc->add_shipping_method( 'usps', $_POST['sService'], 'N/A', 'N/A', $_POST['extra'] );
	}
}

// Get shipping methods
$shipping_method = $sc->get_shipping_method( $website_shipping_method_id );

$sub_title = ( $website_shipping_method_id ) ? _('Edit USPS') : _('Add USPS');
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
        <p class="error">You must set up your USPS Account before adding USPS shipping methods.</p>
        <p><a href="/shopping-cart/shipping/settings/">Click here</a> to set up your USPS account information.</p>
        <?php } else { ?>
        <form name="fAddEditShipping" id="fAddEditShipping" action="/shopping-cart/shipping/add-edit-usps/<?php if ( !empty( $website_shipping_method_id ) ) echo "?wsmid=$website_shipping_method_id"; ?>" method="post">
            <table class="form">
                <tr>
                    <td><label for="sService"><?php echo _('Service:'); ?></label></td>
                    <td>
                       <select id="sService" name="sService">
					   <?php
							$services = array(
								'FIRST CLASS' => _('First Class')
								, 'PRIORITY' => _('Priority')
								, 'PRIORITY COMMERCIAL' => _('Priority Commercial')
								, 'EXPRESS' => _('Express')
								, 'EXPRESS COMMERCIAL' => _('Express Commercial')
								, 'EXPRESS SH' => _('Express SH')
								, 'EXPRESS SH COMMERCIAL' => _('Express SH Commercial')
								, 'EXPRESS HFP' => _('Express HFP')
								, 'COMMERCIAL' => _('Commercial')
								, 'BPM' => _('BPM')
								, 'PARCEL' => _('Parcel')
								, 'MEDIA' => _('Media')
								, 'LIBRARY' => _('Library')
								, 'ALL' => _('All')
								, 'ONLINE' => _('Online')
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
                    <td><label for="sSize"><?php echo _('Size:'); ?></label></td>
                    <td>
                       <select id="sSize" name="extra[size]">
					   <?php
							$sizes = array(
								'REGULAR' => _('Regular')
								, 'LARGE' => _('Large (Length + girth over 84in under 109in)')
								, 'OVERSIZE' => _('Oversize (girth over 108in under 131in)')
								);
							
							foreach ( $sizes as $sv => $s ) {
								$selected = ( $shipping_method['extra']['size'] == $sv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $sv; ?>"<?php echo $selected; ?>><?php echo $s; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
				<tr>
                    <td><label for="sFirstClassMailType"><?php echo _('First Class Mail Type:'); ?></label></td>
                    <td>
                       <select id="sFirstClassMailType" name="extra[mail_type]">
					   <?php
							$mail_types = array(
								'' => '-- ' . _('None') . ' --'
								, 'FLAT' => _('Flat')
							);
							
							foreach ( $mail_types as $mtv => $mt ) {
								$selected = ( $shipping_method['extra']['mail_type'] == $sv ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $mtv; ?>"<?php echo $selected; ?>><?php echo $mt; ?></option>
							<?php } ?>
						</select>
                    </td>
                </tr>
			</table>
            <br/><br/>
			<input type="submit" class="button" value="<?php echo ( $website_shipping_method_id ) ? _('Update Shipping Method') : _('Add Shipping Method'); ?>" />
			<?php nonce::field('add-edit-usps-shipping-method'); ?>
		</form>	
        <?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>