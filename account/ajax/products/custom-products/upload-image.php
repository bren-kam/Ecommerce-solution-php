<?php
/**
 * @page Upload Image
 * @package Grey Suit Retail
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'upload-image' );
$ajax->ok( !empty( $_FILES ), _('No files were uploaded') );

$product_id = (int) $_POST['pid'];

$ajax->ok( $product_id, _('Please enter a name for your product first') );

$new_image_name = format::slug( f::strip_extension( $_FILES["Filedata"]['name'] ) );
$image_extension = strtolower( f::extension( $_FILES["Filedata"]['name'] ) );
$full_image_name = $new_image_name . '.' . $image_extension;

// Instantiate file-handling class
$f = new Files;
$p = new Products;
$w = new Websites;

$user['website'] = $w->get_website( $_POST['wid'] );
$max_image_size = $w->get_setting('custom-image-size');
$industry = $p->get_industry( $product_id );

// Assign the max image size
if ( !$max_image_size || 0 == $max_image_size )
    $max_image_size = 700;

// Remove spaces
$industry = str_replace( ' ', '', $industry );

$ajax->ok( !empty( $industry ), _('You must select an industry before uploading an image') );

$f->upload_image( $_FILES["Filedata"], $new_image_name, 320, 320, $industry, 'products/' . $product_id . '/', false, true );
$f->upload_image( $_FILES["Filedata"], $new_image_name, 46, 46, $industry, 'products/' . $product_id . '/thumbnail/', false, true );
$f->upload_image( $_FILES["Filedata"], $new_image_name, 200, 200, $industry, 'products/' . $product_id . '/small/', false, true );
$f->upload_image( $_FILES["Filedata"], $new_image_name, $max_image_size, $max_image_size, $industry, 'products/' . $product_id . '/large/', false, true);

$image = '<div class="product-image" id="dProductImage_' . $new_image_name . '">';
$image .= '<img src="http://' . $industry . '.retailcatalog.us/products/' . $product_id . '/thumbnail/' . $full_image_name . '" width="50" />';
$image .= '<a href="http://' . $industry . '.retailcatalog.us/products/' . $product_id . '/large/' . $full_image_name . '" title="' . _('View Image') . '" target="_blank">' . _('View') . '</a>';
$image .= '<br /><a href="/ajax/products/custom-products/remove-image/?_nonce=' . nonce::create('remove-image') . '&amp;pid=' . $product_id . '&amp;i=' . $new_image_name . '" title="' . _('Remove Image') . '" ajax="1" confirm="' . _('Are you sure you want to remove this image? This cannot be undone') . '">' . _('Remove') . '</a>';
$image .= '<input type="hidden" class="hidden-value" name="hProductImages[]" id="' . $new_image_name . '" value="/' . $full_image_name . '" />';
$image .= '</div>';

jQuery('#dUploadedImages .loading:first')
	->replaceWith( $image )
	->updateImageSequence()
	->sparrow();

// Add the response
$ajax->add_response( 'jquery', jquery::getResponse() );

// Send respond
$ajax->respond();