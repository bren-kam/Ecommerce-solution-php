<?php
/**
 * @page Payment Gateway
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate classes
$w = new Websites;
$v = new Validator;

$v->form_name = 'fPaymentGateway';
$v->add_validation( 'aim-login', 'req', _('The "AIM Login" field is required') );
$v->add_validation( 'aim-transaction-key', 'req', _('The "AIM Transaction Key" field is required') );

add_footer( $v->js_validation() );

// Initialize Variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-payment-gateway-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	// @fix - had to change $settings to $_POST below!
	if ( empty( $errs ) ) {
		$settings['aim-login'] = base64_encode( security::encrypt( $_POST['aim-login'], PAYMENT_DECRYPTION_KEY ) );
		$settings['aim-transaction-key'] = base64_encode( security::encrypt( $_POST['aim-transaction-key'], PAYMENT_DECRYPTION_KEY ) );
		$settings['payment-gateway-status'] = $_POST['payment-gateway-status'];
		
		$success = $w->update_settings( $settings );
	}
}

// Get settings
$settings = $w->get_settings( 'payment-gateway-status', 'aim-login', 'aim-transaction-key' );

$title = _('Payment Gateway') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'settings';
get_header();
?>

<div id="content">
	<h1><?php echo _('Payment Gateway'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'settings' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<p class="success"><?php echo _('Payment Gateway Settings successfully saved!'); ?></p>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='error'>$errs</p>";
		?>
        <form name="fPaymentGateway" action="/shopping-cart/settings/payment-gateway/" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="150"><label><?php echo _("Payment Gateway:"); ?></label></td>
                <td><?php echo _('Authorize.net AIM'); ?></td>
            </tr>
            <tr>
                <td><label for="payment-gateway-status"><?php echo _("Status:"); ?></label></td>
                <td>
                    <?php $status = ( $success || !isset( $_POST['payment-gateway-status'] ) ) ? $settings['payment-gateway-status'] : $_POST['payment-gateway-status']; ?>
                    <select name="payment-gateway-status" id="payment-gateway-status">
                        <option value="0"<?php if ( 0 == $status ) echo ' selected="selected"'; ?>><?php echo _('Testing'); ?></option>
                        <option value="1"<?php if ( 1 == $status ) echo ' selected="selected"'; ?>><?php echo _('Live'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150"><label for="aim-login"><?php echo _("AIM Login:"); ?></label></td>
                <td><input class="tb" type="text" name="aim-login" id="aim-login" value="<?php echo ( !$success && isset( $_POST['aim-login'] ) ) ? $_POST['aim-login'] : security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ); ?>" maxlength="30" /></td>
            </tr>
            <tr>
                <td width="150"><label for="aim-transaction-key"><?php echo _("AIM Transaction Key:"); ?></label></td>
                <td><input class="tb" type="text" name="aim-transaction-key" id="aim-transaction-key" value="<?php echo ( !$success && isset( $_POST['aim-transaction-key'] ) ) ? $_POST['aim-transaction-key'] : security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ); ?>" maxlength="30" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" class="button" value="<?php echo _('Update Settings'); ?>" /></td>
            </tr>
        </table>
        <?php nonce::field( 'update-payment-gateway-settings' ); ?>
        </form>
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>