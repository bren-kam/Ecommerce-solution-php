<?php
/**
 * @page Remove Image
 * @package Grey Suit Retail
 * @subpackage Admin
 */
 
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'remove-image' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'result' => false, 'error' => _('You must be signed in to remove a product image.') ) );
		exit;
	}
	
	$p = new Products;
	$i = new Industries;
	$f = new Files;
	
	$industry = $i->get_by_product( $_POST['pid'] );
	
	preg_match( '/\/products\/([0-9]+)\//', $_POST['image'] );
			
	$thumbnail = str_replace( 'http://' . $industry . '.retailcatalog.us/', '', $_POST['image'] );
	$base_image = str_replace( 'thumbnail/', '', $thumbnail );
	$small = preg_replace( '/(products\/[0-9]+\/(?:[^\/]+\/)?)?([^\/]+)/', "$1small/$2", $base_image );
	$large = preg_replace( '/(products\/[0-9]+\/(?:[^\/]+\/)?)?([^\/]+)/', "$1large/$2", $base_image );

	$f->delete_image( $thumbnail, $industry );
	$f->delete_image( $base_image, $industry );
	$f->delete_image( $small, $industry );
	$f->delete_image( $large, $industry );
	
	$result = $p->remove_image( basename( $base_image ), $_POST['pid'] );
	
	// If there was an error, let them know
	echo json_encode( array( 'result' => $result, 'error' => _('An error occurred while trying to change the industry on your product. Please refresh the page and try again.') ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}