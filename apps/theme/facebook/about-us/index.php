<?php
/**
 * @page About Us
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '233746136649331', '298bb76cda7b2c964e0bf752cf239799' );
$au = new About_Us;
$v = new Validator;

// Get User
$user = $fb->user;

// Set Validation
$v->add_validation( 'tFBConnectionKey', 'req', _('The "Facebook Connection Key" field is required') );

// Make sure they are validly editing the app
if( isset( $_GET['app_data'] ) ) {
	// Get App Data
	$app_data = url::decode( $_GET['app_data'] );
	$other_user_id = security::decrypt( $app_data['uid'], 'SecREt-Us3r!' );
	$page_id = security::decrypt( $app_data['pid'], 'sEcrEt-P4G3!' );
}

// Make sure it's a valid request
if( $other_user_id == $user && $page_id ) {
	if( nonce::verify( $_POST['_nonce'], 'connect-to-field' ) ) {
		$errs = $v->validate();
		
		// if there are no errors
		if( empty( $errs ) )
			$success = $au->connect( $page_id, $_POST['tFBConnectionKey'] );
	}
}

// Get the website
if( $page_id )
	$website = $au->get_connected_website( $page_id );

add_footer('<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "233746136649331", status: true, cookie: true,
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
$title = _('About Us') . ' | ' . _('Online Platform');
get_header('facebook/');
?>

<div id="content">
	<h1><?php echo _('Online Platform - About Us'); ?></h1>
	<?php if( $success && $website ) { ?>
	<div class="success">
		<p><?php echo _('Your information has been successfully updated!'); ?></p>
	</div>
	<?php 
	}
	
	if( isset( $errs ) )
			echo "<p class='error'>$errs</p>";
	
	if( !$page_id ) {
	?>
	<ol>
		<li>Go to this page: <a href="javascript:top.location.href='http://www.facebook.com/apps/application.php?id=233746136649331';" title="Online Platform - About Us">Online Platform - About Us</a> and click "Add to My Page" in the left-hand bar, select your page and add it.</li>
		<li>Go to the page you selected, click on the "Home" tab and click "Update Settings" to connect to your page to the platform.</li>
	</ol>
	<?php } else { ?>
	<form name="fConnect" method="post" action="/facebook/about-us/<?php if( isset( $_GET['app_data'] ) ) echo '?app_data=' . $_GET['app_data']; ?>">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td width="200"><strong><?php echo _('Website'); ?>:</strong></td>
			<td><?php echo ( $website ) ? $website['title'] : 'N/A'; ?></td>
		</tr>
		<tr>
			<td><label for="tFBConnectionKey"><?php echo _('Facebook Connection Key'); ?>:</label></td>
			<td><input type="text" class="tb" name="tFBConnectionKey" id="tFBConnectionKey" value="<?php echo ( $website ) ? $website['key'] : ''; ?>" /> <strong><?php echo ( $website ) ? '<span class="success">(' . _('Connected') . ')</span>' : '<span class="error">(' . _('Not Connected') . ')</span>'; ?></strong></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" class="button" value="<?php echo _('Connect'); ?>" /></td>
		</tr>
	</table>
	<?php nonce::field('connect-to-field'); ?>
	</form>
	<?php } ?>
</div>

<?php get_footer('facebook/'); ?>