#!/usr/local/bin/php -q
<?php
require 'includes/rfc822_addresses.php';
require 'includes/mime_parser_class.php';

$email_content = file_get_contents( 'php://stdin' );

$mime = new mime_parser_class;
$mime->ignore_syntax_errors = 1;
$emails = array();

$headers = 'From: noreply@noreply.com' . "\r\n" . 
	"Reply-to: noreply@noreply.com" . "\r\n" . 
	'X-Mailer: PHP/' . phpversion();
	
mail('kerry.jones@earthlink.net', 'made it', __LINE__, $headers );

$mime->Decode( array( 'Data' => $email_content ), $emails );
$email = $emails[0];

$subject = $email['Headers']['subject:'];
$from = $email['ExtractedAddresses']['from:'][0];
$to = preg_replace( '/.+for ([^;]+).+/', '$1', $email['Headers']['received:'] );
list( $username, $domain ) = explode ( '@', $to );
list ( $username, $tag ) = explode( '+', $username );

$body = ( empty( $email['Body'] ) ) ? $email['Parts'][0]['Body'] : $email['Body'];
$body = nl2br( substr( $body, 0, strpos( $body, '>> ' ) ) );

// Create MySQL DB Object
$mysqli = mysqli_connect( '199.204.138.78', 'imaginer_admin', 'rbDxn6kkj2e4', 'imaginer_system' );

// Get the first website
$website_domain = $mysqli->real_escape_string( $tag );

$result = $mysqli->query( "SELECT `website_id` FROM `websites` WHERE `status` = 1 AND `domain` LIKE '%$website_domain%' LIMIT 1" );

// Get the row
$row = $result->fetch_assoc();

if ( is_null( $row ) ) {
	// Try variation one
	$website_domain = str_replace( 'www.', '', $tag );
	
	$result = $mysqli->query( "SELECT `website_id` FROM `websites` WHERE `status` = 1 AND `domain` LIKE '%$website_domain%' LIMIT 1" );

	// Get the row
	$row = $result->fetch_assoc();

	if ( is_null( $row ) ) {
		// Try variation two
		$website_domain = preg_replace( '/(.+?)(?:\.[a-zA-Z]{2,4}){1,2}$/', '$1', $website_domain );
		
		$result = $mysqli->query( "SELECT `website_id` FROM `websites` WHERE `status` = 1 AND `domain` LIKE '%$website_domain%' LIMIT 1" );
	
		// Get the row
		$row = $result->fetch_assoc();
		
		if ( is_null( $row ) )
			exit;
	}
}

$website_id = (int) $row['website_id'];

// Try to get the user that sent the email
$user_email = $mysqli->prepare( $from['address'] );

// Try to get the user based off email
$result = $mysqli->query( "SELECT `user_id` FROM `users` WHERE `status` = 1 AND `email` = '$user_email'" );

// Get the row
$row = $result->fetch_assoc();

// Determine the user id
if ( is_null( $row ) ) {
	$contact_name = $from['name'];
	
	if ( empty( $contact_name ) )
		$contact_name = $user_email;
	
	$contact_name = $mysqli->real_escape_string( $contact_name );
	$password = $mysqli->real_escape_string( md5( microtime() ) );
	
	// Create User
	$mysqli->query( "INSERT INTO `users` ( `email`, `password`, `contact_name`, `role`, `date_created` ) VALUES ( '$user_email', '$password', '$contact_name', 1, NOW() )" );

	$user_id = (int) $mysqli->insert_id;
} else {
	$user_id = (int) $row['user_id'];
}

$message = $mysqli->real_escape_string( "Email: $subject<br /><br />$body" );

// Insert website note
$mysqli->query( "INSERT INTO `website_note` ( `website_id`, `user_id`, `message`, `date_created` ) VALUES ( $website_id, $user_id, '$message', NOW() )");

// Close connection
$mysqli->kill( $mysqli->thread_id );
$mysqli->close();

?>
