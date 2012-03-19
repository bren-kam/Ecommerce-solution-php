<?php
/**
 * @page Contact Us
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '245607595465926', 'b29a7efe3a1329bae0b425de96acd84b', true );
$cu = new Contact_Us;

// Get the signed request
$signed_request = $fb->getSignedRequest();

// Get User
$user_id = $fb->user;

// Get the website
$tab = $cu->get_tab( $signed_request['page']['id'] );

// If it's secured, make the images secure
if ( security::is_ssl() )
    $tab = ( stristr( $tab, 'websites.retailcatalog.us' ) ) ? preg_replace( '/(?<=src=")(http:\/\/)/i', 'https://s3.amazonaws.com/', $tab ) : preg_replace( '/(?<=src=")(http:)/i', 'https:', $tab );

$title = _('Contact Us') . ' | ' . _('Online Platform');
get_header('facebook/tabs/');
?>

<div id="content">
	<?php if( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="#" onclick="top.location.href='http://apps.facebook.com/op-contact-us/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	echo $tab;
	?>
</div>

<?php get_footer('facebook/tabs/'); ?>