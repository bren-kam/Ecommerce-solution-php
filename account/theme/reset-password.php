<?php
/**
 * @page Reset Password
 * @package Grey Suit Retail
 */

// They need to have a user id and activation code
if( empty( $_GET['uID'] ) || empty( $_GET['t'] ) )
	url::redirect( 'http://www.' . DOMAIN . '/', 417 );

// If they're already logged in, lets log them out
if ( isset( $user ) && $user ) {
	$u->logout();
	$user = false;
}

$token = new Tokens();
$user_id = $token->check( $_GET['t'], 'reset-password' ); // returns the token_id

// If the tocken didn't check out, redirect them
if( !$user_id )
	url::redirect( 'http://www.' . DOMAIN . '/', 417 );

global $u;
$rp_user = $u->get_user( $_GET['uID'] );

$v = new Validator();
$v->form_name = 'fResetPassword';

$v->add_validation( 'tPassword', 'req', _('The "Password" field is required') );
$v->add_validation( 'tPassword', 'minlen=6', _('The "Password" field must be at least 6 characters long') );
$v->add_validation( 'tPassword|tRePassword', 'match', _('The "Password" and "Confirm Password" field must match') );

// Make sure they posted
if( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'reset-password' ) ) {
	$errs = $v->validate();

	if( empty( $errs ) ) {
		$success = $u->update_information( $rp_user['user_id'], array( 'password' => $_POST['tPassword'] ) );

		// Delete the token
		if( $success )
			$token->delete( $_GET['t'] );
	}
}

// If it was successful, we don't need to validate anything
if( !$success )
	add_footer( $v->js_validation() ); // Add it to the footer javascript

$title = _('Reset Passsword') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Reset Passsword'); ?></h1>
	<br clear="all" />
	<br />
	
	<?php if( $success ) { ?>
	<p><?php echo _('Your password has been successfully changed!'); ?></p>
	<p><?php echo _('<a href="/login/" title="Login">Click here</a> to login.'); ?></p>
	<?php } else { ?>
	<p><?php echo _('Enter your new password below:'); ?></p>
	<br />
	<?php
		if( !empty( $errs ) )
			echo '<p style="color: red;">', $errs, "</p>\n";
	?>
	<form method="post" name="fResetPassword" action="">
	<table>
		<tr>
			<td valign="top"><label for="tPassword"><?php echo _('Password:'); ?></label></td>
			<td><input name="tPassword" id="tPassword" type="password" class="tb" /></td>
		</tr>
		<tr>
			<td valign="top"><label for="tRePassword"><?php echo _('Verify Password:'); ?></label></td>
			<td><input name="tRePassword" id="tRePassword" type="password" class="tb" /></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="<?php echo _('Change Password'); ?>" class="button" /></td>
		</tr>
		<?php nonce::field('reset-password'); ?>
	</table>
	</form>
	<?php } ?>
</div>

<?php get_footer(); ?>
