<?php
/**
 * @page Shipping - Add/Edit
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$sc = new Shopping_Cart;
$v = new Validator;

$v->form_name = 'fAddEditShipping';
$v->add_validation( 'tName', 'req', _('The "Name" field is required' ) );
$v->add_validation( 'sMethod', 'req', _('Please select a shipping method' ) );
$v->add_validation( 'tAmount', 'req', _('Please enter a valid amount' ) );

add_footer( $v->js_validation() );

$website_shipping_method_id = false;

// Modify the settings
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-custom-shipping-method' ) ) {
	$errs = $v->validate();

	if ( empty( $errs ) ) {
		if ( '' != $_POST['hID'] ) {
			$success = $sc->update_shipping_method( $_POST['tName'], $_POST['sMethod'], $_POST['tAmount'], $_POST['hID'] );
			$success *= $sc->update_shipping_zip_codes( $_POST['hID'], $_POST['tZip'] );
		} else {
			$website_shipping_method_id = $sc->add_shipping_method( $_POST['tName'], $_POST['sMethod'], $_POST['tAmount'], $_POST['hID'] );
			$success = $sc->update_shipping_zip_codes( $shipping_method_id, $_POST['tZip'] );
		}
		
		// If we were successful
		if ( $success ) 
			url::redirect( '/shopping-cart/shipping/' );
	}
}

// Determine website shipping method id
$website_shipping_method_id = $_GET['wsmid'];

// Get shipping methods
$methods = $sc->get_shipping_methods( $user['website']['website_id'] );

// Define variables
$name = $method = $amount = '';

// Assign the methods
foreach ( $methods as $m ) {
	// Has to equal it
	if ( $m['website_shipping_method_id'] != $website_shipping_method_id )
		continue;
	
	$name = $m['name'];
	$method = $m['method'];
	$amount = $m['amount'];
	break;
}


// Get zip codes
if ( $website_shipping_method_id || !empty( $_POST['tZip'] ) )
	$zips = ( $website_shipping_method_id ) ? $sc->get_shipping_zip_codes( $website_shipping_method_id ) : $_POST['tZip'];

javascript( '/shopping-cart/shipping/add-edit-custom' );

$sub_title = ( $website_shipping_method_id ) ? _('Edit Custom') : _('Add Custom');
$title = $sub_title . ' ' . _('Shipping Method') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'shipping';
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title . ' ' . _('Shipping Method'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
        <?php 
		if ( isset( $errs ) )
				echo "<p class='error'>$errs</p>";
		?>
        <form name="fAddEditShipping" id="fAddEditShipping" action="/shopping-cart/shipping/add-edit/" method="post">
            <table class="form">
                <tr>
                    <td><label for="tName"><?php echo _('Name: '); ?></label></td>
                    <td><input name="tName" tmpval="<?php echo _('Method Name'); ?>..." class="tb" maxlength="5" type="text" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $name; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="sMethod"><?php echo _('Method: '); ?></label></td>
                    <td>
                        <select name="sMethod" class="tb" value="<?php echo ( !$success && isset( $_POST['sMethod'] ) ) ? $_POST['sMethod'] : $method; ?>">
                            <option value="">-- <?php echo _('Select a Method'); ?> --</option>
                            <option value="Flat Rate"<?php if ( 'Flat Rate' == $method ) echo ' selected="selected"'; ?>><?php echo _('Flat Rate'); ?></option>
                            <option value="Percentage"<?php if ( 'Percentage' == $method ) echo ' selected="selected"'; ?>><?php echo _('Percentage'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
					<td><label for="tAmount"/><?php echo _('Amount: '); ?></td>
					<td><input name="tAmount" tmpval="<?php echo _('Enter Amount'); ?>..." class="tb" type="text" value="<?php echo ( !$success && isset( $_POST['tAmount'] ) ) ? $_POST['tAmount'] : $amount; ?>" /></td>
				</tr>
                <tr>
                	<td><label><?php echo _('Zip Codes'); ?>:</label></td>
                    <td>
                    	<?php 
						if ( is_array( $zips ) ) {
						foreach ( $zips as $zip ) {
						?>
                        <span id="sZip<?php echo $zip; ?>">
                        	<input type="text" class="tb" maxlength="5" name="tZip[]" value="<?php echo $zip; ?>"/>&nbsp;
                            <a href="javascript:;" id="aDeleteZip<?php echo $zip; ?>" class="delete-zip" title="<?php echo _('Delete Zip'); ?>">
                            	<img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" />
							</a>
                            <br />
						</span>
						<?php } ?>
                        <br id="brInsertZipAbove" />
						<?php } ?>
                        <input type="text" class="tb" maxlength="5" id="tAddNewZip" tmpval="<?php echo _('New Zip...'); ?>"/> <a href="javascript:;" class="button" id="aAddNewZip"><?php echo _('Add Zip'); ?></a>
                    </td>
                </tr>
			</table>
            <br/><br/>
			<input type="hidden" name="hID" value="<?php echo ( $_POST['hID'] ) ? $_POST['hID'] : $_GET['wsmid']; ?>" />
			<input type="submit" class="button" value="<?php echo _('Save Shipping Method') ; ?>" />
			<?php nonce::field('add-edit-custom-shipping-method'); ?>
		</form>	
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>