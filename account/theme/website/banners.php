<?php
/**
 * @page Website - Banners
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

// Instantiate classes
$w = new Websites;
$wa = new Website_Attachments;

$page = $w->get_page_by_slug( 'home' );
$settings = $w->get_settings( 'banner-width', 'banner-height' );

// Set dimensions if they are empty
foreach( $settings as $k => &$v ) {
	if ( !empty( $v ) )
		continue;
	
	$v = ( 'banner-width' == $k ) ? 680 : 300;
	
	$new_settings[$k] = $v;
}

$dimensions = $settings['banner-width'] . 'x' . $settings['banner-height'];

// Update any new settings
if( is_array( $new_settings ) )
	$w->update_settings( $new_settings );

$attachments = $wa->get_by_page( $page['website_page_id'] );

css( 'jquery.uploadify', 'website/banners');
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/banners');

$selected = "website";
$title = _('Banners | Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Banners'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'banners' ); ?>
	<div id="subcontent">
		<input type="file" id="fUploadBanner" /><br />
		<p class="red"><?php echo _('(Note: The changes you make to your banners are immediately live on your website)'); ?></p>
		<input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
		<input type="hidden" id="hWebsitePageID" value="<?php echo $page['website_page_id']; ?>" />
		<input type="hidden" id="hBannerWidth" value="<?php echo $settings['banner-width']; ?>" />
		<?php 
		nonce::field( 'upload-banner', '_ajax_upload_banner' );
		nonce::field( 'update-sequence', '_ajax_update_sequence' );
		?>
		<div id="dContactBoxes">
			<?php
			$remove_attachment_nonce = nonce::create('remove-attachment');
			$update_status_nonce = nonce::create('update-status');
			$confirm_disable = _('Are you sure you want to deactivate this banner?');
			$confirm_remove = _('Are you sure you want to remove this banner?');
			
			foreach( $attachments as $a ) {
				if( '0' == $a['status'] ) {
					$disabled = ' disabled';
					$confirm = '';
				} else {
					$disabled = '';
					$confirm = ' confirm="' . $confirm_disable . '"';
				}
				?>
				<div class="contact-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a['website_attachment_id']; ?>">
					<h2><?php echo _('Flash Banner'); ?></h2>
					<p><small><?php echo $dimensions; ?></small></p>
					<a href="/ajax/website/sidebar/update-status/?_nonce=<?php echo $update_status_nonce; ?>&amp;waid=<?php echo $a['website_attachment_id']; ?>&amp;s=<?php echo ( '0' == $a['status'] ) ? '1' : '0'; ?>" id="aEnableDisable<?php echo $a['website_attachment_id']; ?>" class="enable-disable<?php echo $disabled; ?>" title="<?php echo _('Enable/Disable'); ?>" ajax="1"<?php echo $confirm; ?>><img src="/images/trans.gif" width="26" height="28" alt="<?php echo _('Enable/Disable'); ?>" /></a>
					
					<div id="dBanner<?php echo $a['website_attachment_id']; ?>" class="text-center">
						<img src="http://<?php echo ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'], $a['value']; ?>" alt="<?php echo _('Sidebar Image'); ?>" />
					</div>
					<br />
					
					<form action="/ajax/website/sidebar/update-extra/" method="post" ajax="1">
						<p id="pTempSuccess<?php echo $a['website_attachment_id']; ?>" class="success hidden"><?php echo _('Your banner link has been successfully updated.'); ?></p>
						<input type="text" class="tb" name="extra" tmpval="<?php echo _('Enter Link...'); ?>" value="<?php echo ( empty( $a['extra'] ) ) ? 'http://' : $a['extra']; ?>" />
						<input type="submit" class="button" value="<?php echo _('Save'); ?>" />
						
						<input type="hidden" name="hWebsiteAttachmentID" value="<?php echo $a['website_attachment_id']; ?>" />
						<input type="hidden" name="target" value="pTempSuccess<?php echo $a['website_attachment_id']; ?>" />
						<?php nonce::field('update-extra', '_ajax_update_extra'); ?>
					</form>
					<a href="/ajax/website/sidebar/remove-attachment/?_nonce=<?php echo $remove_attachment_nonce; ?>&amp;waid=<?php echo $a['website_attachment_id']; ?>&amp;t=dAttachment_<?php echo $a['website_attachment_id']; ?>&amp;si=1" class="remove" title="<?php echo _('Remove Banner'); ?>" ajax="1" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a>
					<br clear="all" />
				</div>
			<?php } ?>
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>