<?php
/**
 * @var AccountPageAttachment $apply_now
 * @var User $user
 */
?>
<table>
	<tr>
		<td class="top" width="140"><label><?php echo _('Apply Now Button'); ?>:</label></td>
		<td>
			<div id="dApplyNowContent">
				<?php if ( !empty( $apply_now ) ) { ?>
					<img src="<?php echo $apply_now->value; ?>" alt="<?php echo _('Apply Now'); ?>" style="padding-bottom: 10px;" />
					<br />
					<p><?php echo _('Place "[apply-now]" into the page content above to place the location of your image. When you view your website, this will be replaced with the image uploaded.'); ?></p>
				<?php } ?>
			</div>
			<a href="#" id="aUploadImage" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
            <a href="#" class="button loader hidden" id="upload-image-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
            <div class="hidden" id="upload-image"></div>
            <?php nonce::field( 'upload_image', '_upload_image' ); ?>
		</td>
	</tr>
	<tr>
		<td><label for="tApplyNowLink"><?php echo _('Apply Now Link'); ?>:</label></td>
		<td><input type="text" class="tb" name="tApplyNowLink" id="tApplyNowLink" value="<?php echo $apply_now_link; ?>" /></td>
	</tr>
</table>