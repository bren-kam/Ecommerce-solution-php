#!/usr/local/bin/php -q
<?php

require( 'rfc822_addresses.php' );
require( 'mime_parser_class.php' );

$email_content = file_get_contents( 'php://stdin' ); 

$mime = new mime_parser_class;
$mime->ignore_syntax_errors = 1;

$mime->Decode( array( 'Data' => $email_content ), $emails );
$email = $emails[0];

$subject = $email['Headers']['subject:'];
$body = ( empty( $email['Body'] ) ) ? nl2br( $email['Parts'][0]['Body'] ) : nl2br( $email['Body'] );
$body = substr( $body, 0, strpos( $body, '******************* Reply Above This Line *******************' ) );
$reach_id = preg_replace( '/.*#([0-9]+).*/', '$1', $subject );

// Create MySQL DB Object
$mysqli =  mysqli_connect( '199.204.138.78', 'imaginer_admin', 'rbDxn6kkj2e4', 'imaginer_system' ); 

// Insert Reach  comment
$mysqli->query( "INSERT INTO `website_reach_comments` ( `website_reach_id`, `website_user_id`, `comment`, `date_created` ) SELECT `website_reach_id`, `website_user_id`, '" . $mysqli->real_escape_string( nl2br( $body ) ) . "', NOW() FROM `website_reaches` WHERE `website_reach_id` = " . (int) $reach_id );

// Set Reach as waiting
$mysqli->query( "UPDATE `website_reaches` SET `waiting` = 1 WHERE `website_reach_id` = " . (int) $reach_id );

// Get the email of the admin user assigned to their ticket, and the original ticket
$result = $mysqli->query( 'SELECT a.`email`, b.`message`, c.`name`, c.`domain` FROM `users` AS a LEFT JOIN `website_reaches` AS b ON ( a.`user_id` = b.`assigned_to_user_id` ) LEFT JOIN `companies` AS c ON ( a.`company_id` = c.`company_id` ) WHERE b.`website_reach_id` = ' . $reach_id );

if ( $result ) {
	// Get the row
	$row = $result->fetch_assoc();
	
	
	// Set email headers
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Additional headers
	$headers .= 'To: ' . $row['email'] . "\r\n";
	$headers .= 'From: ' . $row['name'] . ' <reaches@' . $row['domain'] . '.com>' . "\r\n";
	
	// Let assigned user know
	mail( $row['email'], "New Response on Reach #{$reach_id}", "<p>A new response from the client has been received. See message below:</p><p><strong>Original Message:</strong><br />" . nl2br( $row['message'] ) . "</p><p><strong>Client Response:</strong><br />{$body}</p><p><a href='http://account." . $row['domain'] . "/reaches/reach/?rid={$reach_id}'>http://account." . $row['domain'] . "/reaches/reach/?rid={$reach_id}</a></p>", $headers );
}

// Close connection
$mysqli->kill( $mysqli->thread_id );
$mysqli->close();

?>
