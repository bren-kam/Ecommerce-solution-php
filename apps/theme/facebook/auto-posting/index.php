<?php
/**
 * @page Auto Posting
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '268649406514419', '6ca6df4c7e9d909a58d95ce7360adbf3', false, array( 'scope' => 'manage_pages,offline_access,publish_stream' ) );
$ap = new Auto_Posting;
$v = new Validator;

// Get User
$user = $fb->user;

// Set Validation
$v->add_validation( 'tFBConnectionKey', 'req', _('The "Facebook Connection Key" field is required') );

// See if we're connected
$connected = $ap->connected( $user );

// Make sure it's a valid request
if ( nonce::verify( $_POST['_nonce'], 'connect-to-field' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if( empty( $errs ) )
		$success = $ap->connect( $user, $_POST['tFBConnectionKey'], $fb->getAccessToken() );
}

add_footer('<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "268649406514419", status: true, cookie: true,
             xfbml: true});
	FB.setSize({ width: 720, height: 500 });
  };
  (function() {
    var e = document.createElement("script"); e.async = true;
    e.src = document.location.protocol +
      "//connect.facebook.net/en_US/all.js";
    document.getElementById("fb-root").appendChild(e);
  }());
</script>');

$title = _('Auto Posting') . ' | ' . _('Online Platform');
get_header('facebook/');
?>

<div id="content">
	<h1><?php echo _('Online Platform - Share and Save'); ?></h1>
	<?php if( $success && $website ) { ?>
	<div class="success">
		<p><?php echo _('Your information has been successfully updated!'); ?></p>
	</div>
	<?php 
	}
	
	if( isset( $errs ) )
			echo "<p class='error'>$errs</p>";
	
	if( !$connected ) { 
	?>
		<form name="fConnect" method="post" action="/facebook/auto-posting/">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><label for="tFBConnectionKey"><?php echo _('Facebook Connection Key'); ?>:</label></td>
				<td><input type="text" class="tb" name="tFBConnectionKey" id="tFBConnectionKey" value="" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" class="button" value="<?php echo _('Connect'); ?>" /></td>
			</tr>
		</table>
		<?php nonce::field('connect-to-field'); ?>
		</form>
	<?php } else { ?>
		<p class="success"><?php echo _('You are connected!'); ?></p>
		<p><?php echo _('You can now sign into your dashboard to control the posting to your pages.'); ?></p>
	<?php } ?>
</div>

<?php get_footer('facebook/'); ?>