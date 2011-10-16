<?php
/**
 * @page Website Sidebar
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

// Instantiate Classes
$w = new Websites;
$wa = new Website_Attachments;
$wf = new Website_Files;

// Get all the website files
$website_files = $wf->get_all();

// Get the Page info
$page = $w->get_page_by_slug( 'sidebar' );

$attachments = $wa->get_by_page( $page['website_page_id'] );

//@temp
$attachments_by_key = ar::assign_key( $attachments, 'key' );

css( 'jquery.uploadify', 'website/website-sidebar' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page', 'website/website-sidebar' );

$selected = "website";
$title = _('Sidebar | Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Sidebar'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'website-sidebar' ); ?>
	<div id="subcontent">
		<div id="dNewImage"><input type="file" class="hidden" id="fNewImage" /></div>
		<a href="#dUploadFile" class="button" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload  File'); ?></a>
		<br /><br /><br />
		<p class="alert">(<?php echo _('Note: The changes you make to your sidebar are immediately live on your website'); ?>)</p>
		<input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
		<input type="hidden" id="hWebsitePageID" value="<?php echo $page['website_page_id']; ?>" />
		<?php 
		$remove_attachment_nonce = nonce::create( 'remove-attachment' );
		nonce::field( 'update-sequence', '_ajax_update_sequence' );
		
		nonce::field( 'new-image', '_ajax_new_image' );
		nonce::field( 'upload-video', '_ajax_upload_video' );
		nonce::field( 'upload-file', '_ajax_upload_file' );
		?>

		<div id="dContactBoxes">
		<?php
		$h2 = $content_id = $placerholder = $buttons = $value = '';
		$update_status_nonce = nonce::create( 'update-status' );
		$confirm_disable = _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.');
		$confirm_remove = _('Are you sure you want to remove this sidebar element?');
		
		foreach( $attachments as $a ) {
			$continue = false;
			$remove = true;
			
			if ( '0' == $a['status'] ) {
				$disabled =  ' disabled';
				$confirm = '';
				$status = '1';
			} else {
				$confirm = ' confirm="' . $confirm_disable . '"';
				$disabled = '';
				$status = '0';
			}
			
			$enable_disable_link = '<a href="/ajax/website/sidebar/update-status/?_nonce=' . $update_status_nonce . '&amp;waid=' . $a['website_attachment_id'] . '&amp;s=' . $status . '" id="aEnableDisable' . $a['website_attachment_id'] . '" class="enable-disable' . $disabled . '" title="' . _('Enable/Disable') . '" ajax="1"' . $confirm . '><img src="/images/trans.gif" width="76" height="25" alt="' . _('Enable/Disable') . '" /></a>';
			
			switch( $a['key'] ) {
				case 'current-ad-image':
					// Phrase this out
					$continue = true;
					continue;
					
					$h2 = _('Current Ad Image');
					$content_id = 'dCurrentAdImageContent';
					$placerholder = '<img src="/images/placeholders/240x300.png" width="240" height="300" alt="' . _('Placeholder') . '" />';
					$value = '<img src="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $a['value'] . '" id="iCurrentAdImage" alt="' . _('Current Ad Image') . '" />';
					
					$buttons = '<input type="file" id="fUploadCurrentAdImage" /> <input type="file" id="fUploadCurrentAdPDF" />';
					
					if( empty( $attachments_by_key['current-ad-pdf']['value'] ) )
					$buttons .=  '
					<div style="float:left">
						<div id="dCurrentAdPDFContent">
							<a href="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $attachments_by_key['current-ad-pdf']['value'] . '" title="' . _('Current Ad PDF') . '">' . _('Current Ad PDF') . '</a>
						</div>
						<span id="sCurrentAdPDFRemove">
							<a href="/ajax/website/sidebar/remove/?_nonce=' . $remove_attachment_nonce . '&amp;waid=' . $attachments_by_key['current-ad-pdf']['website_attachment_id'] . '&amp;t=dCurrentAdPDFContent" title="' . _('Remove Current Ad PDF') . '">' . _('Remove') . '</a>
						</span>
					</div>';
				break;
				
				case 'current-offer':
					$continue = true;
					continue;
				
					$h2 = _('Current Offer Image');
					$content_id = 'dCurrentOfferContent';
					$placerholder = '<img src="/images/placeholders/240x300.png" width="240" height="300" alt="' . _('Placeholder') . '" />';
					$value = '<img src="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $a['value'] . '" id="iCurrentOffer" alt="' . _('Current Offer Image') . '" />';
					
					$buttons = '<input type="file" id="fUploadCurrentOffer" />';
				break;
				
				case 'email':
					?>
					<div class="contact-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a['website_attachment_id']; ?>">
						<h2><?php echo _('Email Sign Up'); ?></h2>
						
						<?php echo $enable_disable_link; ?>
						
						<div id="dEmailContent">
							<br />
							<form action="/ajax/website/sidebar/update-email/" method="post" ajax="1">
								<textarea name="taEmail" id="taEmail" cols="50" rows="3"><?php echo $a['value']; ?></textarea>
								<p id="pTempEmailMessage" class="success hidden"><?php echo _('Your Email Sign Up text has been successfully updated.'); ?></p>
								<input type="hidden" name="hWebsiteAttachmentID" value="<?php echo $a['website_attachment_id']; ?>" />
								<br /><br />
								<p align="center"><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></p>
								<?php nonce::field( 'update-email', '_ajax_update_email' ); ?>
							</form>
						</div>
					</div>
					<?php
					$continue = true;
					continue;
				break;
				
				case 'room-planner':
					if( !empty( $disabled ) || empty( $a['value'] ) ) {
						$continue = true;
						continue;
					}
						
					$h2 = _('Room Planner');
					$content_id = 'dRoomPlannerContent';
					$placerholder = '<img src="/images/placeholders/240x100.png" width="240" height="100" alt="' . _('Placeholder') . '" />';
					$value = '<img src="http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $a['value'] . '" alt="' . _('Room Planner Image') . '" />';
					
					$buttons = '<input type="file" id="fUploadRoomPlanner" />';
				break;
				
				case 'search':
					$h2 = _('Search');
					$content_id = $placerholder = $value = $buttons = '';
					$remove = false;
				break;
				
				case 'sidebar-image':
					?>
					<div class="contact-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a['website_attachment_id']; ?>">
						<h2><?php echo _('Sidebar Image'); ?></h2>
						
						<?php echo $enable_disable_link; ?>
						
						<div id="dSidebarImage<?php echo $a['website_attachment_id']; ?>">
							<br />
							<form action="/ajax/website/sidebar/update-extra/" method="post" ajax="1">
								<div align="center">
									<p><img src="http://<?php echo ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'], $a['value']; ?>" alt="<?php echo _('Sidebar Image'); ?>" /></p>
									<p><a href="/ajax/website/sidebar/remove-attachment/?_nonce=<?php echo nonce::create('remove-attachment'); ?>&amp;waid=<?php echo $a['website_attachment_id']; ?>&amp;t=dAttachment_<?php echo $a['website_attachment_id']; ?>&amp;si=1" id="aRemove<?php echo $a['website_attachment_id']; ?>" title="<?php echo _('Remove Image'); ?>" ajax="1" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a></p>
									<p><input type="text" class="tb" name="extra" id="tSidebarImage<?php echo $a['website_attachment_id']; ?>" tmpval="<?php echo _('Enter Link...'); ?>" value="<?php echo ( empty( $a['extra'] ) ) ? 'http://' : $a['extra']; ?>" /></p>
									<p id="pTempSidebarImage<?php echo $a['website_attachment_id']; ?>" class="success hidden"><?php echo _('Your Sidebar Image link has been successfully updated.'); ?></p>
									<br />
									<p align="center"><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></p>
								</div>
								
								<input type="hidden" name="hWebsiteAttachmentID" value="<?php echo $a['website_attachment_id']; ?>" />
								<input type="hidden" name="target" value="pTempSidebarImage<?php echo $a['website_attachment_id']; ?>" />
								<?php nonce::field( 'update-extra', '_ajax_update_extra' ); ?>
							</form>
						</div>
					</div>
					<?php
					$continue = true;
					continue;
				break;
				
				case 'video':
					$h2 = _('Video');
					$content_id = 'dVideoContent';
					$placerholder = '<img src="/images/placeholders/354x235.png" width="354" height="235" alt="' . _('Placeholder') . '" />';
					
					$key = substr( substr( md5( DOMAIN . '17e972798ee5066d58c' ), 11, 30 ), 0, -2 );
					
					$value = '<div id="player" style="width:239px; height:213px; margin:0 auto"></div>';
					
					add_footer('
					<script type="text/javascript" language="javascript">
						head.js( "/js2/?f=flowplayer", function() {
								$f("player", "/media/flash/flowplayer.unlimited-3.1.5.swf", {
								key: \'' . $key . '\',
								playlist: [
									{
										url: \'http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . $a['value'] . "',
										autoPlay: false, 
										autoBuffering: true
									}
								],
								plugins: {
									controls: {
										autoHide: 'never',
										backgroundColor: '#111009',
										backgroundGradient: [0.2,0.1,0],
										borderRadius: '0px',
										bufferColor: '#151515',
										bufferGradient: [0.2,0.1,0],
										buttonColor: '#888888',
										buttonOverColor: '#adadad',
										durationColor: '#FFFFFF',
										fullscreen: false,
										height: 25,
										opacity: 1,
										progressColor: '#6A6969',
										progressGradient: [0.8,0.3,0],
										sliderBorder: '1px solid rgba(15, 15, 15, 1)',
										sliderColor: '#151515',
										sliderGradient: [0.2,0.1,0],
										timeBgColor: '#0E0E0E',
										timeBorder: '0px solid rgba(0, 0, 0, 0.3)',
										timeColor: '#656565',
										timeSeparator: ' / ',
										volumeBorder: '1px solid rgba(128, 128, 128, 0.7)',
										volumeColor: '#ffffff',
										volumeSliderColor: '#000000',
										volumeSliderGradient: [0.1,0],
										tooltipColor: '#000000',
										tooltipTextColor: '#ffffff'
									}
								}
							});
						});
					</script>" );
					
					$remove = false;
					
					$buttons = '<input type="file" id="fUploadVideo" />';
				break;
				
				case 'current-ad-pdf':
				default: 
					$continue = true;
					continue;
				break;
			}
			
			if( $continue )
				continue;
			?>
			<div class="contact-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a['website_attachment_id']; ?>">
				<h2><?php echo $h2; ?></h2>
				
				<?php 
				echo $enable_disable_link;
				
				if( !empty( $content_id ) ) { ?>
				<div id="<?php echo $content_id; ?>" class="center-content">
					<?php echo ( empty( $a['value'] ) ) ? $placeholder : $value; ?>
				</div>
				<?php } ?>
				<br />
				
				<?php if( !empty( $buttons ) ) { ?>
				<div align="center" class="buttons">
					<?php if( !empty( $a['value'] ) && $remove ) { ?>
						<a href="/ajax/website/sidebar/remove-attachment/?_nonce=<?php echo $remove_attachment_nonce; ?>&amp;waid=<?php echo $a['website_attachment_id']; ?>&amp;t=<?php echo $content_id; ?>" id="aRemove<?php echo $a['website_attachment_id']; ?>" title="<?php echo _('Remove'); ?>" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a>
						<br /><br />
					<?php 
					}
					
					echo $buttons;
					?>
					<br clear="left" />
				</div>
				<?php } ?>
			</div>
		<?php } ?>
		</div>
	</div>
	<div id="dUploadFile" class="hidden">
		<ul id="ulUploadFile">
			<?php
			if( is_array( $website_files ) ) {
				// Set variables
				$ajax_delete_file_nonce = nonce::create('delete-file');
				$confirm = _('Are you sure you want to delete this file?');
				
				foreach( $website_files as $wf ) {
					$file_name = format::file_name( $wf['file_path'] );
					echo '<li id="li' . $wf['website_file_id'] . '"><a href="', $wf['file_path'], '" id="aFile', $wf['website_file_id'], '" class="file" title="', $file_name, '">', $file_name, '</a><a href="/ajax/website/page/delete-file/?_nonce=' . $ajax_delete_file_nonce . '&amp;wfid=' . $wf['website_file_id'] . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
				}
			} else {
				echo '<li>', _('You have not uploaded any files.') . '</li>';
			}
			?>
		</ul>
		<br />
		
		<input type="text" class="tb" id="tFileName" tmpval="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" style="position:relative; bottom: 11px;" /> 
		<input type="file" name="fUploadFile" id="fUploadFile" />
		<br /><br />
		<div id="dCurrentLink" class="hidden">
			<p><strong><?php echo _('Current Link'); ?>:</strong></p>
			<p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
			<br />
		</div>
		<p align="right"><a href="javascript:;" class="button close" title="<?php echo _('Close'); ?>"><?php echo _('Close'); ?></a></p>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>