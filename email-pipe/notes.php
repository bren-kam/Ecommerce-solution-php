#!/usr/local/bin/php -q
<?php

require 'includes/rfc822_addresses.php';
require 'includes/mime_parser_class.php';

$email_content = file_get_contents( 'php://stdin' );

$mime = new mime_parser_class;
$mime->ignore_syntax_errors = 1;
$emails = array();

$mime->Decode( array( 'Data' => $email_content ), $emails );
$email = $emails[0];

mail( 'kerry@studio98.com', 'Email', var_export('mail') );
exit;

$subject = $email['Headers']['subject:'];
$body = ( empty( $email['Body'] ) ) ? $email['Parts'][0]['Body'] : $email['Body'];
$body = nl2br( substr( $body, 0, strpos( $body, '******************* Reply Above This Line *******************' ) ) );
$ticket_id = (int) preg_replace( '/.*Ticket #([0-9]+).*/', '$1', $subject );

// Create MySQL DB Object
$mysqli =  mysqli_connect( 'localhost', 'imaginer_admin', 'rbDxn6kkj2e4', 'imaginer_system' );

// Insert Ticket comment
$mysqli->query( "INSERT INTO `ticket_comments` ( `ticket_id`, `user_id`, `comment`, `date_created` ) SELECT `ticket_id`, `user_id`, '" . $mysqli->real_escape_string( $body ) . "', NOW() FROM `tickets` WHERE `ticket_id` = " . $ticket_id );

// Get the email of the admin user assigned to their ticket, and the original ticket
$result = $mysqli->query( 'SELECT a.`email`, b.`message`, c.`name`, c.`domain` FROM `users` AS a LEFT JOIN `tickets` AS b ON ( a.`user_id` = b.`assigned_to_user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) WHERE b.`ticket_id` = ' . $ticket_id );

// Get the row
$row = $result->fetch_assoc();

// Set email headers
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: ' . $row['email'] . "\r\n";
$headers .= 'From: ' . $row['name'] . ' Support <noreply@' . $row['domain'] . '>' . "\r\n";

// Let assigned user know
mail( $row['email'], "New Response on Ticket #{$ticket_id}", "<p>A new response from the client has been received. See message below:</p><p><strong>Original Message:</strong><br />" . $row['message'] . "</p><p><strong>Client Response:</strong><br />{$body}</p><p><a href='http://admin." . $row['domain'] . "/tickets/ticket/?tid={$ticket_id}'>http://admin" . $row['domain'] . "/tickets/ticket/?tid={$ticket_id}</a></p>", $headers );

// Close connection
$mysqli->kill( $mysqli->thread_id );
$mysqli->close();

?>
