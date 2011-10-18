<?php
/**
 * @page View Request
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure we have a request selected
if ( empty( $_GET['rid'] ) )
	url::redirect( '/requests/' );

css( 'requests/view', 'jquery.ui' );
javascript( 'jquery', 'jquery.ui', 'jquery.common', 'jquery.form', 'requests/view' );

$r = new Requests;
$request = $r->get( $_GET['rid'] );

// Make the status english readable
switch ( $request['status'] ) {
	case 0:
		$status_title = '<span class="orange">' . _('Open') . '</span>';
		break;

	case 1:
		$status_title = '<span class="green">' . _('Approved') . '</span>';
		break;

	case 2:
		$status_title = '<span class="red">' . _('Disapproved') . '</span>';
		break;
}


$selected = 'requests';
$title = _('View Request') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><strong><?php echo _('View Request'), ' - ', $status_title; ?></strong></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'requests/' ); ?> 
	
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" width="100%" id="tRequestData">
			<tr>
				<td width="100"><strong><?php echo _('Website'); ?>:</strong></td>
				<td><?php echo $request['title']; ?></td>
				<td width="100"><strong><?php echo _('Date Created'); ?>:</strong></td>
				<td><?php echo date( 'F jS, Y', strtotime( $request['date_created'] ) ); ?></td>
				<td width="30%">&nbsp;</td>
			</tr>
			<tr>
				<td><strong><?php echo _('User'); ?>:</strong></td>
				<td><?php echo $request['contact_name']; ?></td>
				<td><strong><?php echo _('Date Updated'); ?>:</strong></td>
				<td><?php echo date( 'F jS, Y', strtotime( $request['date_updated'] ) ); ?></td>
			</tr>
			<tr><td colspan="4">&nbsp;</td>
			<tr>
				<td><strong><?php echo _('Request Type'); ?>:</strong></td>
				<td><?php echo $request['type']; ?></td>
			</tr>
			<tr>
				<td><strong><?php echo _('Request'); ?>:</strong></td>
				<td>
					<?php
					switch ( $request['type'] ) {
						case 'Header Update':
							$request_data = unserialize( html_entity_decode( $request['request'] ) );
							echo 'See new data below';
						break;
						
						case 'Product Request':
							$request_data = explode( '|', $request['request'] );
							echo 'New products have been requested.';
						break;
						
						default:
							echo $request['request'];
						break;
					}
					?>
				</td>
			</tr>
		</table>
		<br />
		<?php
		switch ( $request['type'] ) {
			case 'Header Update': {
			?>
			<br />
			<h2><?php echo _('Page Data'); ?></h2>
			<br />
			<table cellpadding="0" cellspacing="0" id="tPageData" width="100%">
				<?php if ( 0 == $request['status'] && $request['web']['phone'] != $request_data['phone_number'] ) { ?>
				<tr>
					<td width="150">
						<strong><?php echo _('Phone Number'); ?>:</strong><br />
						<a href="#" id="aOriginalPhone" class="original" title="View Original Phone">View Original Phone Number</a>
					</td>
					<td class="changed">
						<div id="dRequestedPhone"><?php echo $request_data['phone_number']; ?></div>
						<div id="dOriginalPhone" class="hidden"><?php echo $request['web']['phone']; ?></div>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td width="150"><strong><?php echo _('Phone Number'); ?>:</strong></td>
					<td><?php echo $request_data['phone_number']; ?></td>
				</tr>
				<?php
				}
				
				if ( isset( $request_data['image'] ) ) {
					$remote_logo = 'http://' . $request['web']['domain'] . '/custom/uploads/images/' . $request['web']['logo'];
					if ( 0 == $request['status'] && @file_get_contents( $request_data['image'], FILE_BINARY ) != @file_get_contents( $remote_logo, FILE_BINARY ) ) {
					?>
					<tr>
						<td>
							<strong><?php echo _('Logo'); ?>:</strong><br />
							<a href="#" id="aOriginalLogo" class="original" title="<?php echo _('View Original Logo'); ?>"><?php echo _('View Original Logo'); ?></a>
						</td>
						<td class="changed">
							<div id="dRequestedLogo"><img src="<?php echo $request_data['image']; ?>" alt="<?php echo _('Logo'); ?>" /></div>
							<div id="dOriginalLogo" class="hidden"><img src="<?php echo $remote_logo; ?>" alt="<?php echo _('Logo'); ?>" /></div>
						</td>
					</tr>
					<?php } else { ?>
					<tr>
						<td><strong><?php echo _('Logo'); ?>:</strong></td>
						<td class="changed"><img src="<?php echo $request_data['image']; ?>" alt="<?php echo _('Logo'); ?>" /></td>
					</tr>
					<?php 
					} 
				}
				?>
			</table>
			<br /><br />
			<?php
			}
			break;
			
			case 'Page Update': {
			?>
			<br />
			<h2><?php echo _('Page Data'); ?></h2>
			<br />
			<table cellpadding="0" cellspacing="0" id="tPageData" width="100%">
				<tr>
					<td width="150"><strong><?php echo _('Title'); ?>:</strong></td>
					<td><?php echo $request['page']['title']; ?></td>
				</tr>
				<?php if ( 0 == $request['status'] && $request['page']['content'] != $request['original_page']['content'] ) { ?>
				<tr>
					<td>
						<strong><?php echo _('Content'); ?>:</strong><br />
						<a href="#" id="aOriginalContent" class="original" title="<?php echo _('View Original Content'); ?>"><?php echo _('View Original Content'); ?></a>
					</td>
					<td class="changed">
						<div id="dRequestedContent"><?php echo html_entity_decode( $request['page']['content'], ENT_QUOTES, 'UTF-8' ); ?></div>
						<div id="dOriginalContent" class="hidden"><?php echo html_entity_decode( $request['original_page']['content'], ENT_QUOTES, 'UTF-8' ); ?></div>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td><strong><?php echo _('Content'); ?>:</strong></td>
					<td><?php echo html_entity_decode( $request['page']['content'], ENT_QUOTES, 'UTF-8' ); ?></td>
				</tr>
				<?php 
				}
				
				if ( 0 == $request['status'] && $request['page']['meta_title'] != $request['original_page']['meta_title'] ) {
				?>
				<tr>
					<td>
						<strong><?php echo _('Meta Title'); ?>:</strong><br />
						<a href="#" id="aOriginalMetaTitle" class="original" title="<?php echo _('View Original Meta Title'); ?>"><?php echo _('View Original Meta Title'); ?></a>
					</td>
					<td class="changed">
						<div id="dRequestedMetaTitle"><?php echo $request['page']['meta_title']; ?></div>
						<div id="dOriginalMetaTitle" class="hidden"><?php echo $request['original_page']['meta_title']; ?></div>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td><strong><?php echo _('Meta Title'); ?>:</strong></td>
					<td><?php echo $request['page']['meta_title']; ?></td>
				</tr>
				<?php 
				}
				
				if ( 0 == $request['status'] && $request['page']['meta_description'] != $request['original_page']['meta_description'] ) {
				?>
				<tr>
					<td>
						<strong><?php echo _('Meta Description'); ?>:</strong><br />
						<a href="#" id="aOriginalMetaDescription" class="original" title="<?php echo _('View Original Meta Description'); ?>"><?php echo _('View Original Meta Descripton'); ?></a>
					</td>
					<td class="changed">
						<div id="dRequestedMetaDescription"><?php echo $request['page']['meta_description']; ?></div>
						<div id="dOriginalMetaDescription" class="hidden"><?php echo $request['original_page']['meta_description']; ?></div>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td><strong><?php echo _('Meta Description'); ?>:</strong></td>
					<td><?php echo $request['page']['meta_description']; ?></td>
				</tr>
				<?php 
				}
				
				if ( 0 == $request['status'] && $request['page']['meta_keywords'] != $request['original_page']['meta_keywords'] ) {
				?>
				<tr>
					<td>
						<strong><?php echo _('Meta Keywords'); ?>:</strong><br />
						<a href="#" id="aOriginalMetaKeywords" class="original" title="<?php echo _('View Original Meta Keywords'); ?>"><?php echo _('View Original Meta Keywords'); ?></a>
					</td>
					<td class="changed">
						<div id="dRequestedMetaKeywords"><?php echo $request['page']['meta_keywords']; ?></div>
						<div id="dOriginalMetaKeywords" class="hidden"><?php echo $request['original_page']['meta_keywords']; ?></div>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td><strong><?php echo _('Meta Keywords'); ?>:</strong></td>
					<td><?php echo $request['page']['meta_keywords']; ?></td>
				</tr>
				<?php } ?>
			</table>
			<br /><br />
			
			<?php if ( isset( $request['meta'] ) && count( $request['meta'] ) > 0 ) { ?>
			<h2><?php echo _('Extra Data'); ?></h2>
			<br/ >
			<table cellpadding="0" cellspacing="0" id="tMetaData" width="100%">
				<?php
				//print_r( $request['meta'] );
				switch ( $request['meta'] as $key => $pm ) {
					$uc_key = ucwords( str_replace( '-', ' ', $key ) );
					switch ( $key ) {
						case 'addresses':
							$uc_key = _('Address(es)');
							
							// Format the addresses
							$addresses = '';
							$address_array = unserialize( html_entity_decode( $pm['value'], ENT_QUOTES, 'UTF-9' ) );
							//echo html_entity_decode( str_replace( '&', '&amp;', $pm['value'] ), ENT_QUOTES, 'UTF-8' );
							//exit;
							
							if ( is_array( $address_array ) )
							foreach ( $address_array as $address ) {
								if ( !isset( $address['fax'] ) )
									$address['fax'] = '';
		
								$addresses .= "<p>\n";
								$addresses .= "<strong>" . $address['location'] . "</strong><br />\n" . $address['address'] . "<br />\n" . $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'] . "<br /><br />";
								$addresses .= $address['phone'] . '<br />' . $address['fax'] . '<br />' . $address['email'] . '<br />' . $address['website'] . '<br /><br />';
								
								if ( isset( $address['store-hours'] ) )
									$addresses .= '<strong>' . _('Store Hours') . ':</strong><br />' . $address['store-hours'];
								
								$addresses .= "</p>\n";
							}
							
							$pm['value'] = $addresses;
							
							if ( isset( $request['original_meta'] ) && isset( $request['original_meta'][$key] ) ) {
								
								$old_addresses = '';
								$old_address_array = unserialize( $request['original_meta'][$key]['value'] );
								
								if ( is_array( $old_address_array ) )
								foreach ( $old_address_array as $old_address ) {
									if ( !isset( $old_address['fax'] ) )
										$old_address['fax'] = '';
		
									$old_addresses .= "<p>\n";
									$old_addresses .= "<strong>" . $old_address['location'] . "</strong><br />\n" . $old_address['address'] . "<br />\n" . $old_address['city'] . ', ' . $old_address['state'] . ' ' . $old_address['zip'] . "<br /><br />";
									$old_addresses .= $old_address['phone'] . '<br />'. $old_address['fax'] . '<br />' . $old_address['email'] . '<br />' . $old_address['website'] . '<br /><br />';
									
									if ( isset( $old_address['store-hours'] ) )
										$old_addresses .= '<strong>' .  _('Store Hours') . ':</strong><br />' . $old_address['store-hours'];
									
									$old_addresses .= "</p>\n";
								}
								
								$request['original_meta'][$key]['value'] = $old_addresses;
							}
							break;
						
						default:
							break;
					}
					
					$original_key_exists = ( isset( $request['original_meta'][$key] ) ) ? true : false;
					
					if ( $original_key_exists && 0 == $request['status'] && $pm['value'] != $request['original_meta'][$key]['value'] ) {
					?>
					<tr>
						<td width="150">
							<strong><?php echo $uc_key; ?>:</strong><br />
							<a href="#" id="aOriginal<?php echo $pm['request_pagemeta_id']; ?>" class="original" title="<?php echo _('View Original '), $uc_key; ?>"><?php echo _('View Original '), $uc_key; ?></a>
						</td>
						<td class="changed">
							<div id="dRequested<?php echo $pm['request_pagemeta_id']; ?>"><?php echo $pm['value']; ?></div>
							<div id="dOriginal<?php echo $pm['request_pagemeta_id']; ?>" class="hidden"><?php echo $request['original_meta'][$key]['value']; ?></div>
						</td>
					</tr>
					<?php } else { ?>
					<tr>
						<td width="150"><strong><?php echo $uc_key; ?>:</strong></td>
						<td<?php if ( !$original_key_exists ) echo ' class="changed"'; ?>><?php echo $pm['value']; ?></td>
					</tr>
					<?php 
					} 
				}
				?>
			</table>
			<?php } ?>
			
			<?php if ( 0 == $request['status'] && isset( $request['attachments'] ) && count( $request['attachments'] ) > 0 ) { ?>
			<h2><?php echo _('Attachments'); ?></h2>
			<br/ >
			<table cellpadding="0" cellspacing="0" id="tAttachments" width="100%">
				<?php
				switch ( $request['attachments'] as $attachment ) {
					$uc_key = ucwords( str_replace( '-', ' ', $attachment['key'] ) );
					$image = ( @getimagesize( $attachment['value'] ) ) ? true : false;
					if ( isset( $request['original_attachments'][$attachment['key']] ) && $attachment['value'] != $request['original_attachments'][$attachment['key']]['value'] ) {
							
					?>
					<tr>
						<td width="150">
							<strong><?php echo $uc_key; ?>:</strong><br />
							<a href="#" id="aOriginal<?php echo $attachment['request_attachment_id']; ?>" class="original" title="View Original Attachment'); ?>">View Original Attachment'); ?></a>
						</td>
						<td class="changed">
							<div id="dRequested<?php echo $attachment['request_attachment_id']; ?>">
								<?php if ( $image ) { list( $width, $height ) = getimagesize( $attachment['value'] ); ?>
								<img src="<?php echo $attachment['value']; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $uc_key; ?>" />
								<?php } else { ?>
								<a href="<?php echo $attachment['value']; ?>" title="<?php echo $uc_key; ?>"><?php echo $uc_key; ?></a>
								<?php } ?>
							</div>
							<div id="dOriginal<?php echo $attachment['request_attachment_id']; ?>" class="hidden">
								<?php if ( $image ) { list( $width, $height ) = getimagesize( $request['original_attachments'][$attachment['key']]['value'] ); ?>
								<img src="<?php echo $request['original_attachments'][$attachment['key']]['value']; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $uc_key; ?>" />
								<?php } else { ?>
								<a href="<?php echo $request['original_attachments'][$attachment['key']]['value']; ?>" title="<?php echo $uc_key; ?>"><?php echo $uc_key; ?></a>
								<?php } ?>
							</div>
						</td>
					</tr>
					<?php } else { ?>
					<tr>
						<td width="150"><strong><?php echo $uc_key; ?>:</strong></td>
						<td<?php if ( !isset( $request['original_attachments'][$attachment['key']] ) ) echo ' class="changed"'; ?>>
							<?php if ( $image ) { list( $width, $height ) = getimagesize( $attachment['value'] ); ?>
							<img src="<?php echo $attachment['value']; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $uc_key; ?>" />
							<?php } else { ?>
							<a href="<?php echo $attachment['value']; ?>" title="<?php echo $uc_key; ?>"><?php echo $uc_key; ?></a>
							<?php } ?>
						</td>
					</tr>
					<?php 
					} 
				}
				?>
			</table>
			<?php } ?>
			
			<?php
			}
			break;
			
			case 'Product Request':{
			?>
			<br />
			<h2><?php echo _('Products Requested'); ?></h2>
			<br />
			<table cellpadding="0" cellspacing="0" id="tProductsRequested" width="100%">
				<tr>
					<th><?php echo _('Brand'); ?></th>
					<th><?php echo _('SKU'); ?></th>
					<th><?php echo _('Collections/Product'); ?></th>
				</tr>
				<?php foreach ( $request_data as $product ) { @list( $brand_id, $sku, $collections_product ) = explode( '~', $product ); ?>
				<tr>
					<td><?php echo $brand_id; ?></td>
					<td><?php echo $sku; ?></td>
					<td><?php echo $collections_product; ?></td>
				</tr>
				<?php } ?>
			</table>
			<br /><br />
			<?php 
			}
			break;
		}
		?>
		<br /><br />
		
		<br /><br />
		<div align="center">
			<form name="fUpdateRequest" id="fUpdateRequest" method="post" action="/ajax/requests/update/">
				<?php nonce::field( 'update-request' ); ?>
				<input type="hidden" name="hRequestID" value="<?php echo $request['request_id']; ?>" />
				<input type="hidden" name="hAction" id="hAction" value="" />
				<input type="button" id="bApprove" class="button" value="<?php echo _('Approve'); ?>" />
				<input type="button" id="bDisapprove" class="button" value="<?php echo _('Disapprove'); ?>" />
			</form>
		</div>
		<div id="dSendDisapproveMessage" class="hidden">
			<p><?php echo _('Write a message letting the user know what why his request has been disapproved'); ?>:</p>
			<form name="fSendMessage" id="fSendMessage" action="/ajax/requests/send-message/" method="post">
				<?php nonce::field( 'send-message', '_send_message_nonce' ); ?>
				<?php // nonce::field( 'send-message', '_send_message_nonce' ); ?>
				<input type="hidden" name="hRequestID" value="<?php echo $request['request_id']; ?>" />
				<textarea name="taMessage" cols="50" rows="3" style="width:370px"></textarea>
				<br /><br />
				<div align="center"><input type="submit" class="button" value="<?php echo _('Send Message &amp; Disapprove Request'); ?>" /></div>
			</form>
		</div>
		<br />
		<br />
		<div id="dMessages">
			<h2><?php echo _('Messages'); ?></h2>
			<?php nonce::field( 'get-messages', '_ajax_get_messages' ); ?>
			<div id="dMessageList"></div>
			<br />
			<form id="fNewMessage" method="post" action="/ajax/requests/send-message/">
				<?php nonce::field( 'send-message', '_send_message_nonce' ); ?>
				<input type="hidden" name="hRequestID" id="hRequestID" value="<?php echo $request['request_id']; ?>" />
				<textarea id="taMessage" name="taMessage" rows="3" cols="50" ></textarea><br />
				<input type="submit" class="button" id="bSendMessage" value="<?php echo _('Send a Message'); ?>" />
			</form>
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>