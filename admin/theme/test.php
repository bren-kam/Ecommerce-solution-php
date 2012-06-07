<?php
library('ashley-api/ashley-api');
$a = new Ashley_API();
$a->get_packages();

echo "Message: " . $a->message();

exit;
// Load the library
library( 'craigslist-api' );
$b = new Base_Class();
// Create API object
$craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );

$craigslist_markets = $craigslist_api->get_cl_markets();
$values = array();

foreach ( $craigslist_markets as $cm ) {
    if ( 0 == $cm->postable )
        continue;

    list ( $state, $city, $submarket ) = explode( '-', $cm->name, 3 );

    $values[] = "( " . (int) $cm->cl_market_id . ', ' . (int) $cm->parent_id . ", '" . $b->db->escape( $state ) . "', '" . $b->db->escape( $city ) . "', '" . $b->db->escape( $submarket ) . "', " . (int) $cm->submarket . ')';
}

$sql = "INSERT INTO `craigslist_markets` ( `market_id`, `parent_market_id`, `state`, `city`, `area`, `submarket` ) VALUES " . implode( ',', $values ) . " ON DUPLICATE KEY UPDATE `market_id` = VALUES( `market_id` ), `parent_market_id` = VALUES ( `parent_market_id` ), `submarket` = VALUES( `submarket` )";
echo $sql;
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