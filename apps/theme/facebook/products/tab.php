<?php
/**
 * @page Products
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '163636730371197', '3dbe8bc58cf03523ad51603654ca50a6', true );
$p = new Products;

// Get the signed request
$signed_request = $fb->getSignedRequest();

// Get User
$user_id = $fb->user;

// Get the website
$tab = $p->get_tab( $signed_request['page']['id'] );

$title = _('Products') . ' | ' . _('Online Platform');
css('facebook/products');
get_header('facebook/');
?>

<div id="content">
	<?php if( $signed_request['page']['admin'] ) { ?>
		<p><strong>Admin:</strong> <a href="javascript:top.location.href='http://apps.facebook.com/op-products/?app_data=<?php echo url::encode( array( 'uid' => security::encrypt( $user_id, 'SecREt-Us3r!' ), 'pid' => security::encrypt( $signed_request['page']['id'], 'sEcrEt-P4G3!' ) ) ); ?>';">Update Settings</a></p>
	<?php 
	}
	
	echo $tab;
	?>
</div>

<?php get_footer('facebook/'); ?>