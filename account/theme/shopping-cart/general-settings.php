<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$w = new Websites;
$v = new Validator;

$v->form_name = 'fGeneralSettings';
$v->add_validation( 'email-receipt', 'req', _('The "Email" field is required') );
$v->add_validation( 'email-receipt', 'email', _('The "Email" field must contain a valid email') );

$settings = $_POST;

if( !empty( $settings ) ) {
	$success = false;
	$a = $v->Validate();
	if ( $a ) $errs[] = $a;
	
	if( empty( $errs ) ) {
		$success = $w->update_settings( $settings );
	}
}

$settings = $w->get_settings( 'email-receipt' );

$email_receipt = $settings['email-receipt'];

css( "shopping-cart/view" );

$title = _('Shopping Cart - General Settings') . ' | ' . TITLE;
$page = 'settings';
get_header();
?>

<div id="content">
	<h1><?php echo _('General Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
		<div id="dErrors">
        <?php 
			foreach( $errs as $err ) {
				echo "<p class='error'>" . $err . "</p>";
			}
			if( $success ) echo "<p class='success'>Settings successfully updated!</p>";
		?>
		</div>
        <form name="fGeneralSettings" id="fGeneralSettings" action="/shopping-cart/general-settings/" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="150"><label for="email-receipt">Email Receipt:</label></td>
                <td><input type="text" class="tb" name="email-receipt" id="email-receipt" value="<?php echo $email_receipt; ?>" maxlength="150" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" class="button" value="Update Settings" /></td>
            </tr>
        </table>
        </form>
	<?php echo $form_validation; ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>