<?php
/**
 * @page Current Ad
 * @package Grey Suit Retail
 */

global $user;

// Instantiate Classes
$fb = new FB( '186618394735117', 'd4cbf0c45ed772cf1ca0d98e0adb1383', 'current-ad', true );
$ca = new Current_Ad;

// Get the signed request
$signed_request = $fb->getSignedRequest();

$v = new Validator();
$v->form_name = 'fSidebarNewsletter';
$v->add_validation( 'tSidebarName', 'req', 'The "Name" field is required' );
$v->add_validation( 'tSidebarName', '!val=Name:', 'The "Name" field is required' );

$v->add_validation( 'tSidebarEmail', 'req', 'The "Email" field is required' );
$v->add_validation( 'tSidebarEmail', '!val=Email:', 'The "Email" field is required' );
$v->add_validation( 'tSidebarEmail', 'email', 'The "Email" field must contain a valid email' );

if ( nonce::verify( $_POST['_nonce'], 'sign-up' ) ) {
	$errs = $v->validate();
	
	// Insert email into the default category
	if( empty( $errs ) )
		$success = $ca->add_email( $signed_request['page']['id'], $_POST['tSidebarName'], $_POST['tSidebarEmail'] );
}

// Get User
$user_id = $fb->user;

// Get the website
$tab = $ca->get_tab( $signed_request['page']['id'], $success );

// If it's secured, make the images secure
if ( security::is_ssl() )
    $tab = ( stristr( $tab, 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab );

$title = _('Current Ad') . ' | ' . _('Online Platform');
get_header('facebook/tabs/');
?>

<div id="content">
	<?php if( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="#" onclick="top.location.href='http://apps.facebook.com/current-ad/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	if ( $success )
		echo '<p>Your have been successfully added to our email list!</p>';
	
	if ( isset( $errs ) )
		echo "<p class='error'>$errs</p>";
	
	echo $tab;
	?>
</div>

<?php get_footer('facebook/tabs/'); ?>