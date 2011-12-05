<?php
/**
 * @page Get Craigslist Template
 * @package Imagine Retailer
 * @subpackage Account
 */
$ajax = new AJAX( $_POST['_nonce'], 'get-template' );
$ajax->ok( $user, _('You must be signed in to set a craigslist template.') );

// Instantiate Class
$c = new Craigslist;
$p = new Products;

$template = $c->get_template( $_POST['cid'], $_POST['d'], $_POST['tid'] );

$ajax->ok( $product = $p->get_product( $_POST['pid'] ), _('Failed to get template. Please refresh the page and try again.') );
$ajax->ok( $images = $p->get_product_image_urls( $_POST['pid'] ), _('Failed to get product images. Please refresh the page and try again.') );

$search = array( 
	'[Product Name]'
	, '[Store Name]'
	, '[Store Logo]'
	, '[Category]'
	, '[Brand]'
	, '[Product Description]'
	, '[Product Specs]'
	, '[SKU]'
);

$replace = array( 
	$product['name']
	, $user['website']['title']
	, $user['website']['logo']
	, $product['category']
	, $product['brand']
	, $product['description']
	, $product['specifications']
	, $product['sku']
);

$description = '<h2><strong>' . $template['title'] . '</strong></h2><hr />' . _('Date') . ': 2011-4-25, 11:35 ' . _('CST') . '<br />' . _('Reply to') . ': <a href="mailto:test@test.com">sale-rgf3-2123432@craigslist.org</a><hr />' . $template['description'];
$html = str_replace( $search, $replace, $description );

$images_html = '';

if ( is_array( $images ) )
foreach ( $images as $i ) {
	$images_html .= '<img src="' . $i . '" class="hiddenImage" />';
	$html = preg_replace( '/\[Photo\]/', '<img src="' . $i . '" />', $html, 1);
}

jQuery('#dCraigslistPreview')->html( $html );
jQuery('#hTemplateID')->val( $template['craigslist_template_id'] );
jQuery('#hTemplateTitle, #tTitle')->val( str_replace( $search, $replace, $template['title'] ) );
jQuery('#hTemplateDescription')->val( $template['description'] );

jQuery('#hProductCategoryID')->val( $product['category_id'] );
jQuery('#hProductID')->val( $product['product_id'] );
jQuery('#hProductName')->val( $product['name'] );
jQuery('#hStoreName')->val( $user['website']['title'] );
jQuery('#hStoreURL')->val( 'http://' . $user['website']['domain'] );
jQuery('#hStoreLogo')->val( $user['website']['logo'] );
jQuery('#hProductCategoryName')->val( $product['category'] );
jQuery('#hProductBrandName')->val( $product['brand'] );
jQuery('#hProductDescription')->val( $product['description'] );
jQuery('#hProductSKU')->val( $product['sku'] );

jQuery("#dProductPhotos")->html( $images_html );

$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();