<?php
/**
 * @page Ashley Replacement - Get Product
 * @package Grey Suit Retail
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'get-product' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to get a product') ) );
		exit;
	}
	
	$p = new Products;
	$c = new Categories;
	$b = new Brands;
	$i = new Industries;
	$a = new Attributes;
	$ta = new Tags;
	
	$pid = (int) $_POST['pid'];
	
	// Get categories
	$categories = $c->get_list();
	
	// Get brands
	$brands = ar::assign_key( $b->get_all(), 'brand_id' );
	
	// Get industries
	$industries = ar::assign_key( $i->get_all(), 'industry_id' );
	
	// Get attributes
	$attribute_list = $a->get_attribute_items();

	// Get product information
	$product = $p->get( $pid );
	$images = $p->get_images( $pid );
	$categories_list = $p->get_categories( $pid );
	$tags = $ta->get( 'product', $pid );
	$attribute_items = $a->get_attribute_items_by_product( $pid );
	
	// Define empty variables
	$product['images'] = $product['categories'] = $product_specifications = $product['tags'] = $product['attributes'] = '';
	
	/***** Start setting variables *****/
	
	// Images
	if ( count( $images ) > 0 )
	foreach ( $images as $swatch => $image_array  ) {
		foreach ( $image_array as $img ) {
			$product['images'] .= '<img src="http://' . $industries[$product['industry_id']]['name'] . '.retailcatalog.us/products/' . $product['product_id'] . "/thumbnail/$img" . '" width="46" height="46" alt="" /> ';
		}
	}
	
	// Status
	$product['status'] = ucwords( str_replace( '-', ' ', $product['status'] ) );
	
	// Brand
	$product['brand'] = $brands[$product['brand_id']]['name'];
	
	// Industry
	$product['industry'] = $industries[$product['industry_id']]['name'];
	
	// Categories
	if ( is_array( $categories_list ) )
	foreach ( $categories_list as $c ) {
		$product['categories'] .= $c['name'] . '<br />';
	}
	
	// Specfications
	$specifications = unserialize( html_entity_decode( $product['product_specifications'], ENT_QUOTES, 'UTF-8' ) );
	$new_slugs = 0;
	
	if ( !empty( $product['product_specifications'] ) ) {
		$product_specifications .= '<table>';
		
		$specifications = unserialize( html_entity_decode( $product['product_specifications'], ENT_QUOTES, 'UTF-8' ) );
		$new_slugs = 0;
		
		if ( is_array( $specifications ) && count( $specifications ) > 0 )
		foreach ( $specifications as $ps ) {
			$ps_slug = str_replace( ' ', '-', strtolower( $ps[0] ) );
			if ( empty( $ps_slug ) ) {
				$ps_slug = $new_slugs;
				$new_slugs++;
			}
			
			$product_specifications .= '<tr>';
			$product_specifications .= '<td>' . html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ) . '</td>';
			$product_specifications .= '<td>' . html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ) . '</td>';
			$product_specifications .= '</tr>';
		}
		
		$product_specifications .= '</table>';
	}
	
	$product['product_specifications'] = $product_specifications;
	
	// Tags
	if ( is_array( $tags ) )
	foreach ( $tags as $t ) {
		$product['tags'] .= ucwords( $t ) . '<br />';
	}
	
	// Attribute Items
	foreach ( $attribute_items as $ai ) {
		$product['attributes'] .= '<strong>' . $ai['title'] . ' &ndash;</strong> ' . $ai['attribute_item_name'] . '<br />';
	}
	
	// Publish Visibility
	$product['publish_visibility'] = ucwords( $product['publish_visibility'] );
	
	// Needs to be in JSON
	echo json_encode( array( 'result' => true, 'product' => $product, 'error' => _('Failed to get product. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}