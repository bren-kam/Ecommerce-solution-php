<?php
/**
 * @page Settings
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

$v->form_name = 'fGeneralSettings';
$v->add_validation( 'email-receipt', 'req', _('The "Email" field is required') );
$v->add_validation( 'email-receipt', 'email', _('The "Email" field must contain a valid email') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'general-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) )
		$success = $w->update_settings( $settings );
}

// Get the settings
$settings = $w->get_settings( 'email-receipt' );

// Get the receipt
$email_receipt = $settings['email-receipt'];

$title = _('Settings') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'settings';
get_header();
?>

<div id="content">
	<h1><?php echo _('Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'settings' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<p class="success"><?php echo _('Settings successfully saved!'); ?></p>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='error'>$errs</p>";
		?>
        <form name="fGeneralSettings" id="fGeneralSettings" action="/shopping-cart/general-settings/" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="150"><label for="email-receipt"><?php echo _('Email Receipt'); ?>:</label></td>
                <td><input type="text" class="tb" name="email-receipt" id="email-receipt" value="<?php echo $email_receipt; ?>" maxlength="150" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" class="button" value="<?php echo _('Update Settings'); ?>" /></td>
            </tr>
        </table>
		<?php nonce::field('general-settings'); ?>
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