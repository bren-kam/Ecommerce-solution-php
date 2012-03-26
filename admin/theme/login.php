<?php
/**
 * @page Login
 * @package Real Statistics
 */

// If they are already logged in, redirect them
if ( isset( $user ) )
	url::redirect('/');

css( 'login', 'form' );
javascript( 'jquery', 'jquery.tmp-val', 'validator' );

$v = new Validator();
$v->form_name = 'fLogin';

$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email address') );

$v->add_validation( 'tPassword', 'req', _('The "Password" field is required') );

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'login' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		global $u;
		
		if ( $u->login( $_POST['tEmail'], $_POST['tPassword'], isset( $_POST['cbRememberMe'] ) ) ) {
			if( !isset( $_POST['referer'] ) || isset( $_POST['referer'] ) && empty( $_POST['referer'] ) ) {
				url::redirect( '/accounts/' );
			} else {
				unset( $_SESSION['referer'] );
				url::redirect( $_POST['referer'] );
			}
		} else {
			$errs = _('Your email and password do not match. Please try again.');
		}
	}
}
		
$title = _('Login') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Login'); ?></h1>
	<br clear="all" />
	<br />
	<?php if ( !empty( $errs ) ) echo "<p class='red'>$errs</p><br />"; ?>
	<form action="" method="post" name="fLogin">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td><label for="tEmail"><?php echo _('Email:'); ?></label></td>
                <td><input type="text" class="tb" name="tEmail" id="tEmail" value="<?php if ( isset( $_POST['tEmail'] ) ) echo $_POST['tEmail']; ?>" maxlength="200" /></td>
            </tr>
            <tr>
                <td><label for="tPassword"><?php echo _('Password:'); ?></label></td>
                <td><input type="password" class="tb" name="tPassword" id="tPassword" maxlength="30" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="checkbox" class="cb" name="cbRememberMe" id="cbRememberMe" value="yes" /> <label for="cbRememberMe"><?php echo _('Remember me?'); ?></label></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" value="Login" class="button" />
                    <br /><br />
                    <a href="/forgot-your-password/" title="<?php echo _('Forgot Your Password?'); ?>"><?php echo _('Forgot Your Password?'); ?></a>
                </td>
            </tr>
        </table>
        <input type="hidden" name="referer" value="<?php echo $_SESSION['referer']; ?>" />
	    <?php nonce::field( 'login' ); ?>
	</form>
	<?php echo $v->js_validation(); ?>
</div>

<?php get_footer(); ?>