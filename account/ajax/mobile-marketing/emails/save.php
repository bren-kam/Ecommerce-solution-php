<?php
/**
 * @page Save Email Message
 * @package Imagine Retailer
 */

// Create new AJAX
$ajax = new AJAX( $_POST['_nonce'], 'save-email' );
$ajax->ok( $user, _('You must be signed in to save an email message.') );

// Instantiate class
$e = new Email_Marketing();

// Get variables
$email_template_id = ( empty( $_POST['hEmailTemplateID'] ) ) ? 0 : $_POST['hEmailTemplateID'];
$email_type = ( empty( $_POST['hEmailType'] ) ) ? 'none' : $_POST['hEmailType'];
$date_sent = $_POST['tDate'];

// Turn it into machine-readable time
if ( !empty( $_POST['tTime'] ) ) {
	list( $time, $am_pm ) = explode( ' ', $_POST['tTime'] );
	
	if ( 'pm' == strtolower( $am_pm ) ) {
		list( $hour, $minute ) = explode( ':', $time );
		
		$date_sent .= ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
	} else {
		$date_sent .= ' ' . $time . ':00';
	}
}

// Adjust for time zone
$date_sent = date( 'Y-m-d H:i:s', strtotime( $date_sent ) - ( $e->get_setting( 'timezone' ) * 3600 ) - 18000 );

// Get email lists
$email_list_ids = $_POST['email_lists'];

// Form message meta
$message_meta = array();

// Extra data
switch ( $email_type ) {
	case 'product';
		$i = 0;
		
		if ( isset( $_POST['products'] ) )
		foreach ( $_POST['products'] as $product_data ) {
			list( $product_id, $product_price ) = explode( '|', $product_data );
			$message_meta[] = array( 'product', serialize( array( 'product_id' => $product_id, 'price' => $product_price, 'order' => $i ) ) );
			$i++;
		}
	break;
	
	case 'offer';
		$hProductBox1 = ( isset( $_POST['hProductBox1'] ) ) ? $_POST['hProductBox1'] : '';
		$hProductBox2 = ( isset( $_POST['hProductBox2'] ) ) ? $_POST['hProductBox2'] : '';
		
		$message_meta[] = ( 'text' == $_POST['sBox1'] ) ? array( 'offer_1', 'text|' . $_POST['taBox1'] ) : array( 'offer_1', 'product|' . $hProductBox1 );
		$message_meta[] = ( 'text' == $_POST['sBox2'] ) ? array( 'offer_2', 'text|' . $_POST['taBox2'] ) : array( 'offer_2', 'product|' . $hProductBox2 );
	break;
	
	default: break;
}

// Find out if we need to add or update
if ( 0 == $_POST['hEmailMessageID'] ) {
	$ajax->ok( $email_message_id = $e->add_email_message( $email_template_id, stripslashes( $_POST['tSubject'] ), stripslashes( $_POST['taMessage'] ), $email_type, $date_sent, $email_list_ids, $message_meta ), _('An error occurred while trying to save your email. Please refresh the page and try again.') );
	$ajax->add_response( 'email_message_id', $email_message_id );
} else {
	$ajax->ok( $e->update_email_message( $_POST['hEmailMessageID'], $email_template_id, stripslashes( $_POST['tSubject'] ), stripslashes( $_POST['taMessage'] ), $email_type, $date_sent, $email_list_ids, $message_meta ), _('An error occurred while trying to save your email message. Please refresh the page and try again.') );
	$ajax->add_response( 'email_message_id', $_POST['hEmailMessageID'] );
}

// Send response
$ajax->respond();