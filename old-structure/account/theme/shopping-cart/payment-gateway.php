<?php
/**
 * @page Craigslist
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$w = new Websites;
$v = new Validator;

// Connell's original values: keep for testing purposes...
// 2r73vyTjGr
// 79da2kP7dYD88gu6
// enc -> gF7x+s9QeEWFpA==
// enc -> hRWiqItCfBimj/nOCRezbg==

$v->form_name = 'fPaymentGateWay';
$v->add_validation( 'aim-login', 'req', _('The "AIM Login" field is required') );
$v->add_validation( 'aim-transaction-key', 'req', _('The "AIM Transaction Key" field is required') );

$settings = $_POST;

if( !empty( $settings ) ) {
	$a = $v->Validate();
	if ( $a ) $errs[] = $a;
	
	if( empty( $errs ) ) {
		$settings['aim-login'] = base64_encode( security::encrypt( $settings['aim-login'], PAYMENT_DECRYPTION_KEY ) );
		$settings['aim-transaction-key'] = base64_encode( security::encrypt( $settings['aim-transaction-key'], PAYMENT_DECRYPTION_KEY ) );
		$success = $w->update_settings( $settings );
	}
}

$settings = $w->get_settings( 'payment-gateway-status', 'aim-login', 'aim-transaction-key' );

css( "shopping-cart/view" );

$title = _('Shopping Cart - Payment Gateway') . ' | ' . TITLE;
$page = 'settings';
get_header();
?>

<div id="content">
	<h1><?php echo _('Payment Gateway'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
		<div id="dErrors">
        <?php 
			foreach( $errs as $err ) {
				echo "<p class='error'>" . $err . "</p>";
			}
			if( $success ) echo "<p class='success'>" . _("Settings successfully updated!") . "</p>";
		?>
		</div>
        
        <form name="fPaymentGateWay" action="/shopping-cart/payment-gateway/" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="150"><label><?php echo _("Payment Gateway:"); ?></label></td>
                <td>Authorize.net AIM</td>
            </tr>
            <tr>
                <td><label for="payment-gateway-status"><?php echo _("Status:"); ?></label></td>
                <td>
                    <?php
                    if( isset( $success ) ) {
                        $status = ( $success ) ? $settings['payment-gateway-status'] : $_POST['payment-gateway-status'];
                    } else {
                        $status = $settings['payment-gateway-status'];
                    }
                    ?>
                    <select name="payment-gateway-status" id="payment-gateway-status">
                        <option value="0"<?php if( 0 == $status ) echo ' selected="selected"'; ?>>Testing</option>
                        <option value="1"<?php if( 1 == $status ) echo ' selected="selected"'; ?>>Live</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150"><label for="aim-login"><?php echo _("AIM Login:"); ?></label></td>
                <td><input class="tb" type="text" name="aim-login" id="aim-login" value="<?php echo ( isset( $success ) && !$success ) ? $_POST['aim-login'] : security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ); ?>" maxlength="30" /></td>
            </tr>
            <tr>
                <td width="150"><label for="aim-transaction-key"><?php echo _("AIM Transaction Key:"); ?></label></td>
                <td><input class="tb" type="text" name="aim-transaction-key" id="aim-transaction-key" value="<?php echo ( isset( $success ) && !$success ) ? $_POST['aim-transaction-key'] : security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ); ?>" maxlength="30" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" class="button" value="Update Settings" /></td>
            </tr>
        </table>
        <?php nonce::field( 'update-payment-gateway-settings' ); ?>
        </form>
        
	<?php echo $form_validation; ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>