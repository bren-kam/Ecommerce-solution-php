<?php
/**
 * @page Get Random Headline
 * @package Grey Suit Retail
 * @subpackage Account
 */
$ajax = new AJAX( $_POST['_nonce'], 'random-headline' );
$ajax->ok( $user, _('You must be signed in to get a random title.') );

// Instantiate Class
$c = new Craigslist;

$random_headline = $c->get_random_headline( $_POST['cid'] );

jQuery('#' . $_POST['eid'])->val( $random_headline );

$ajax->add_response( 'jquery', jQuery::getResponse() );

$ajax->respond();