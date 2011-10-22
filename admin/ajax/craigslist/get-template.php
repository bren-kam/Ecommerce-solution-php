<?php
/**
 * @page Get Craigslist Template
 * @package Imagine Retailer
 * @subpackage Account
 */

if ( !nonce::verify( $_POST['nonce'], 'craigslist' ) ) return false;

$c = new Craigslist;

$category_id = intval( $_POST['category_id'] );
$template_id = intval( $_POST['template_id'] );
$direction = intval( $_POST['direction'] );

$result = $c->get_template( $category_id, $direction, $template_id, $order );

echo json_encode( array( 'results' => $result ) );