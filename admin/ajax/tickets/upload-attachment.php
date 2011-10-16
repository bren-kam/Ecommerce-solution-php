<?php
/**
 * @page Upload Attachment
 * @package Imagine Retailer
 */
if( empty( $_FILES ) || !nonce::verify( $_POST['_nonce'], 'upload-attachment' ) ) 
	return;
	
global $user;

$user = $u->get_user( $_POST['uid'], 1, 1 );

// Instantiate file-handling class
$f = new Files;

$website_id = (int) $_POST['wid'];

// @Fix @Security needs to make sure you don't upload under someone elses website

// Upload the attachment
list( $ticket_upload_id, $attachment_name, $url ) = $f->upload_attachment( $_FILES['Filedata']['name'], $_FILES['Filedata']['tmp_name'], $website_id );
?>
<div class="attachment" id="dAttachment<?php echo $ticket_upload_id; ?>"><a href="<?php echo $url; ?>" target="_blank"><?php echo ucwords( str_replace( '-', ' ', $attachment_name ) ); ?></a><a href="javascript:;" id="aDeleteAttachment<?php echo $ticket_upload_id; ?>" class="remove-attachment" title="Delete"><img src="/images/icons/x.png" width="16" height="16" alt="Delete" /></a></div>
