<?php
/**
 * @page Email Signup
 * @package Grey Suit Retail
 */

global $user;

// Instantiate Classes
$fb = new FB( '118945651530886', 'ef922d64f1f526079f48e0e0efa47fb7', 'share-and-save', true );
$sas = new Share_and_Save;
$v = new Validator;

// Get the signed request
$signed_request = $fb->getSignedRequest();

// Setup validation
$v->form_name = 'fSignUp';
$v->add_validation( 'tName', 'req', 'The "Name" field is required' );
$v->add_validation( 'tEmail', 'req', 'The "Email" field is required' );
$v->add_validation( 'tEmail', 'email', 'The "Email" field must contain a valid email' );

// Make sure it's a valid request
if( '1' == $signed_request['page']['liked'] && nonce::verify( $_POST['_nonce'], 'sign-up' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if( empty( $errs ) )
		$success = $sas->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );
}

// Get User
$user_id = $fb->user;

// Get the website
$tab = $sas->get_tab( $signed_request['page']['id'], $signed_request['page']['liked'] );

// If it's secured, make the images secure
if ( security::is_ssl() )
    $tab['content'] = ( stristr( $tab['content'], 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab['content'] ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab['content'] );

$title = _('Share and Save') . ' | ' . _('Online Platform');
get_header('facebook/tabs/');
?>

<div id="content">
	<?php if( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="#" onclick="top.location.href='http://apps.facebook.com/share-and-save/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	if ( $success )
		echo '<p>Your have been successfully added to our email list!</p>';
	
	if ( isset( $errs ) )
		echo "<p class='error'>$errs</p>";
	
	$remaining = $tab['minimum'] - $tab['total'];
	
	// How many are left
	if ( !empty( $tab['content'] ) && !empty( $tab['minimum'] ) )
		echo ( $remaining > 0 ) ?'<h2 class="share-save">Only ' . $remaining . ' more until this deal is active!</h2>' : '<h2 class="share-save">This deal is active!</h2>';
	
	if( $tab['total'] > $tab['maximum'] )
		echo '<p class="error">The maximum number of deals has been attained. Stay tuned for another offer...</p>';
	
	// Show the content
	echo $tab['content'];
	
	if ( '1' == $signed_request['page']['liked'] && !$success ) {
	?>
		<form name="fSignUp" method="post" action="/facebook/share-and-save/tab/">
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
	
	<p style="float:left; margin-top: 10px"><a href="#" onclick="window.print();" title="Print">Print</a></p>
	
	<?php
	if( $signed_request['page']['liked'] && !empty( $tab['share_text'] ) ) {
		$link = 'http://www.facebook.com/dialog/feed?';
		$link .= 'app_id=118945651530886&';
		$link .= 'link=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=118945651530886&';
		$link .= 'picture=' . urlencode( $tab['share_image_url'] ) . '&';
		$link .= 'name=' . urlencode( $tab['share_title'] ) . '&';
		$link .= 'description=' . urlencode( str_replace( "'", "\\'", $tab['share_text'] ) ) . '&';
		$link .= 'message=' . urlencode( 'Checkout this Offer!' ) . '&';
		$link .= 'redirect_uri=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_118945651530886';
	?>
	<p style="float:right"><a href="#" onclick="top.location.href='<?php echo $link; ?>';" title="<?php echo _('Share'); ?>"><img src="http://apps.imagineretailer.com/images/buttons/share.png" width="72" height="32" alt="<?php echo _('Share'); ?>" /></a>
	<?php } ?>
</div>

<?php get_footer('facebook/tabs/'); ?>