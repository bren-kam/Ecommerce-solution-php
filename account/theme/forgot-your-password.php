<?php
/**
 * @page Forgot Your Password
 * @package Grey Suit Retail
 */

// If they are already logged in, redirect them
if ( isset( $user ) && $user )
	url::redirect('/');

$v = new Validator();
$v->form_name = 'fForgotYourPassword';

$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email address') );

add_footer( $v->js_validation() );

// Make sure they posted
if( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'forgot-your-password' ) ) {
	$errs = $v->validate();

	// Check for errs
	if( empty( $errs ) ) {
		global $u;

		$response_code = $u->forgot_password( $_POST['tEmail'] );

		switch( $response_code ) {
			case 0:
			default:
				$response = _("The email you entered doesn't exist. Please <a href='http://www." . DOMAIN . ".com/get-started/' title='Sign Up'>sign up</a> first.");
			break;

			case 1:
				$response = _('You have been sent an email with further instructions to reset your password.');
			break;
		}
	}
}

$title = _('Forgot Your Passsword') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Forgot Your Passsword'); ?></h1>
	<br clear="all" />
	<br />
	
	<?php
	if( $response_code > 0 ) {
		echo "<p>$response</p><br /><br />";
	} else {
	?>
	<p><?php echo _('If you have forgotten your password and would like to reset it, enter your email below:'); ?></p>
	<br />
	<?php
	if( !empty( $errs ) )
		echo "<p class='red'>$errs</p><br />";

	if( !empty( $response ) )
		echo "<p class='red'>$response</p>\n";
	?>
	<form action="" method="post" name="fForgotYourPassword">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td><label for="tEmail"><?php echo _('Email:'); ?></label></td>
                <td><input type="text" class="tb" name="tEmail" id="tEmail" value="<?php if ( isset( $_POST['tEmail'] ) ) echo $_POST['tEmail']; ?>" maxlength="200" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value="<?php echo _('Reset Password'); ?>" class="button" /></td>
            </tr>
        </table>
        <?php nonce::field( 'forgot-your-password' ); ?>
	</form>
	<?php } ?>
</div>

<?php get_footer(); ?>
