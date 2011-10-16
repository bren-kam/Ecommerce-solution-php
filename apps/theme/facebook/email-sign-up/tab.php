<?php
/**
 * @page Email Sign Up
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '165553963512320', 'b4957be2dbf78991750bfa13f844cb68', true );
$esu = new Email_Sign_Up;
$v = new Validator;

// Get the signed request
$signed_request = $fb->getSignedRequest();

// Setup validation
$v->form_name = 'fSignUp';
$v->add_validation( 'tName', 'req', 'The "Name" field is required' );
$v->add_validation( 'tEmail', 'req', 'The "Email" field is required' );
$v->add_validation( 'tEmail', 'email', 'The "Email" field must contain a valid email' );

// Make sure it's a valid request
if( nonce::verify( $_POST['_nonce'], 'sign-up' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if( empty( $errs ) )
		$success = $esu->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );
}

// Get User
$user_id = $fb->user;

// Get the website
$tab = $esu->get_tab( $signed_request['page']['id'] );

$title = _('Email Sign Up') . ' | ' . _('Online Platform');
get_header('facebook/');
?>

<div id="content">
	<?php if ( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="javascript:top.location.href='http://apps.facebook.com/op-email-sign-up/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	if ( $success )
		echo '<p>Your have been successfully added to our email list!</p>';
	
	if ( isset( $errs ) )
		echo "<p class='error'>$errs</p>";
	
	echo $tab;
	
	if ( !$success ) {
	?>
		<form name="fSignUp" method="post" action="/facebook/email-sign-up/tab/">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
				<td><input type="text" class="tb" name="tName" id="tName" value="<?php echo $_POST['tName']; ?>" /></td>
			</tr>
			<tr>
				<td><label for="tEmail"><?php echo _('Email'); ?>:</label></td>
				<td><input type="text" class="tb" name="tEmail" id="tEmail" value="<?php echo $_POST['tEmail']; ?>" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" class="button" value="<?php echo _('Sign Up'); ?>" /></td>
			</tr>
		</table>
		<input type="hidden" name="signed_request" value="<?php echo $_REQUEST['signed_request']; ?>" />
		<?php nonce::field('sign-up'); ?>
		</form>
	<?php } ?>
</div>

<?php get_footer('facebook/'); ?>