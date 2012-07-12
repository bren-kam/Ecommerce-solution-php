<?php
/**
 * @page Fan Offer
 * @package Grey Suit Retail
 */

global $user;

// Instantiate Classes
$fb = new FB( '165348580198324', 'dbd93974b5b4ee0c48ae34cb3aab9c4a', 'op-fan-offer', true );
$fo = new Fan_Offer;

// Get the signed request
$signed_request = $fb->getSignedRequest();


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
			$success = $fo->add_email( $signed_request['page']['id'], $_POST['tName'], $_POST['tEmail'] );
	}
}

// Get User
$user_id = $fb->user;

// Get the website
$tab = $fo->get_tab( $signed_request['page']['id'], $signed_request['page']['liked'] );

// If it's secured, make the images secure
if ( security::is_ssl() )
    $tab['content'] = ( stristr( $tab['content'], 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab['content'] ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab['content'] );

$title = _('Fan Offer') . ' | ' . _('Online Platform');
get_header('facebook/tabs/');
?>

<div id="content">
	<?php if( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="#" onclick="top.location.href='http://apps.facebook.com/op-fan-offer/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	if ( $success )
		echo '<p>Your have been successfully added to our email list!</p>';
	
	if ( isset( $errs ) )
		echo "<p class='error'>$errs</p>";
	
	echo $tab['content'];
	
	?>
	
	<p style="float:left; margin-top: 10px"><a href="#" onclick="window.print();" title="Print">Print</a></p>
	
	<?php
	if( $signed_request['page']['liked'] && !empty( $tab['share_text'] ) ) {
		$link = 'http://www.facebook.com/dialog/feed?';
		$link .= 'app_id=165348580198324&';
		$link .= 'link=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_165348580198324&';
		$link .= 'picture=' . $tab['share_image_url'] . '&';
		$link .= 'name=' . urlencode( $tab['share_title'] ) . '&';
		$link .= 'description=' . urlencode( $tab['share_text'] ) . '&';
		$link .= 'message=' . urlencode( 'Checkout this Fan Offer!' ) . '&';
		$link .= 'redirect_uri=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_165348580198324';
	?>
	<p style="float:right"><a href="#" onclick="top.location.href='<?php echo $link; ?>';" title="Share"><img src="http://apps.imagineretailer.com/images/buttons/share.png" width="72" height="32" alt="<?php echo _('Share'); ?>" /></a>
	<?php
	} 
	
	if ( $signed_request['page']['liked'] && !$success && $tab['valid'] ) {
	?>
		<br clear="left" />
		<form name="fSignUp" method="post" action="/facebook/fan-offer/tab/">
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

<?php get_footer('facebook/tabs/'); ?>