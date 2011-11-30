<?php
/**
 * @page Sweepstakes
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '113993535359575', '16937c136a9c5237b520b075d0ea83c8', true );
$s = new Sweepstakes;
$v = new Validator;

// Get the signed request
$signed_request = $fb->getSignedRequest();

// Get the tab
$tab = $s->get_tab( $signed_request['page']['id'], $signed_request['page']['liked'] );

// If it's secured, make the images secure
if ( security::is_ssl() )
    $tab['content'] = ( stristr( $tab['content'], 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab['content'] ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab['content'] );

if( $signed_request['page']['liked'] && $tab['valid'] ) {
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
			$success = $s->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );
	}
}

// Get User
$user_id = $fb->user;

$title = _('Sweepstakes') . ' | ' . _('Online Platform');
get_header('facebook/');
?>

<div id="content">
	<?php if ( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="#" onclick="top.location.href='http://apps.facebook.com/op-sweepstakes/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	if ( $success )
		echo '<p>Your have been successfully added to our email list!</p>';
	
	if ( isset( $errs ) )
		echo "<p class='error'>$errs</p>";
	
	// Show the content if necessary, or else say no active sweepstakes
	echo ( !isset( $tab['valid'] ) || $tab['valid'] ) ? $tab['content'] : '<p>No active Sweepstakes...</p>';
	
	if( $signed_request['page']['liked'] && !empty( $tab['share_text'] ) ) {
		$link = 'http://www.facebook.com/dialog/feed?';
		$link .= 'app_id=113993535359575&';
		$link .= 'link=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_113993535359575&';
		$link .= 'picture=' . urlencode( $tab['share_image_url'] ) . '&';
		$link .= 'name=' . urlencode( $tab['share_title'] ) . '&';
		$link .= 'description=' . urlencode( $tab['share_text'] ) . '&';
		$link .= 'message=' . urlencode( 'Checkout these Sweepstakes!' ) . '&';
		$link .= 'redirect_uri=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_113993535359575';
		?>
		<p align="right"><a href="#" onclick="top.location.href='<?php echo $link; ?>';" title="<?php echo _('Share'); ?>"><img src="http://apps.imagineretailer.com/images/buttons/share.png" width="72" height="32" alt="<?php echo _('Share'); ?>" /></a>
		<?php 
	} 
	
	if ( $signed_request['page']['liked'] && !$success && $tab['valid'] ) {
	?>
		<form name="fSignUp" method="post" action="/facebook/sweepstakes/tab/">
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