<?php
/**
 * @page Activate Account
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is logged in
if ( $user )
	url::redirect('/');

// Instantiate class
$tokens = new Tokens;

// Get token
$token = $_GET['t'];

$user_id = $tokens->check( $token, 'activate-account' );

if ( !$user_id )
	url::redirect('http://www.' . DOMAIN . '/');

$v = new Validator();
$v->form_name = 'fChangePassword';
$v->add_validation( 'tPassword', 'req', _('The "Password" field is required') );
$v->add_validation( 'tPassword|tVerifyPassword', 'match', _('The Password and Confirm Password must match') );

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'change-password' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		$success = $u->update_information( $user_id, array( 'password' => $_POST['tPassword'] ) );
		
		// If it was successful, delete the token
		if ( $success ) {
			$success = $tokens->delete( $token );
			
			if ( $success ) {
				$au = new Authorized_Users();
				$stores = $au->get-stores( $user_id );
			}
		}
	}
}

// Add the validation
if ( !$success )
	add_footer( $v->js_validation() );

$title = _('Activate Account') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<?php if ( $success ) { ?>
		<h1><?php echo _('Account Activated'); ?></h1>
		<br clear="all" />
		<p><?php echo _('Thank you for updating your password. You are now an Authorized User for'), ' ', implode( ', ', $stores ), '.'; ?></p>
		<p><?php echo _('Please bookmark') ?> <a href="http://<?php echo DOMAIN; ?>/" title="http://<?php echo DOMAIN; ?>/">http://<?php echo DOMAIN; ?>/</a>.</p>
		<p><?php echo _('If you are an Authorized User for multiple retailers, you can access any of your dealers by selecting the "Change" link in the top right of the website.'); ?></p>
	<?php } else { ?>
	<h1><?php echo _('Change Password'); ?></h1>
		<br clear="all" />
		<br /><br />
		<p><?php echo _('Fill in your password to activate your account.'); ?></p>
		<form name="fChangePassword" action="/activate-account/?t=<?php echo $_GET['t']; ?>" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label id="tPassword"><?php echo _('New Password:'); ?></label></td>
					<td><input type="password" class="tb" name="tPassword" id="tPassword" maxlength="20" /></td>
				</tr>
				<tr>
					<td><label for="tVerifyPassword"><?php echo _('Verify Password:'); ?></label></td>
					<td><input type="password" class="tb" name="tVerifyPassword" id="tVerifyPassword" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" class="button" value="<?php echo _('Change Password'); ?>" />
						<br /><br />
						<a href="/login/" title="<?php echo _('Already A User?'); ?>"><?php echo _('Already a user?'); ?></a>
					</td>
				</tr>
			</table>
			<?php nonce::field('change-password'); ?>
		</form>
	<?php } ?>
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php get_footer(); ?>