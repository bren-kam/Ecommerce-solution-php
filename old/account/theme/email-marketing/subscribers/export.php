<?php
/**
 * @page Email Marketing - Export Subscribers
 * @package Grey Suit Retail
 */

header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . format::slug( $user['website']['title'] ) . '-email-subscribers.csv"');

// Initialize class
$e = new Email_Marketing;

// Get the email list ID
$email_list_id = ( isset( $_GET['elid'] ) ) ? $_GET['elid'] : 0;

// Get subscribers
$subscribers = $e->export_subscribers( $email_list_id );

// Set it so we can use fputcsv
$outstream = fopen("php://output", 'w');

// Set up the head section
fputcsv( $outstream, array( 'Email', 'Name', 'Phone' ) );

// Put the rest of the items
foreach ( $subscribers as $s ) {
    fputcsv( $outstream, array(
        $s['email']
        , $s['name']
        , $s['phone']
    ));
}