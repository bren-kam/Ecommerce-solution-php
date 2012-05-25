<?php
//library('ashley-api');
//$a = new Ashley_API();
$b = new Base_Class();

$w = new Websites;
exit;

$website_images = $b->db->get_col( "SELECT DISTINCT `image_url` FROM `website_categories` WHERE `status` <> 0 AND `image_url` NOT LIKE '%/small/%'" );

function _small_image( $image ) {
	return preg_replace( '/(.+\/products\/[0-9]+\/)(?:small\/)?([a-zA-Z0-9-.]+)/', "$1small/$2", $image );
}

foreach ( $website_images as $wi ) {
	echo $wi;
	$new_image = _small_image( $wi );

	if ( !getimagesize( $new_image ) ) {
        $b->db->update( 'website_categories', array( 'status' => 0 ), array( 'image_url' => $wi ), 'i', 's' );
		continue;
    }

	$b->db->update( 'website_categories', array( 'image_url' => $new_image ), array( 'image_url' => $wi ), 's', 's' );
}