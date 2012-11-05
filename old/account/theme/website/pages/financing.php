<table cellpadding="0" cellspacing="0" class="form">
	<tr>
		<td class="top" style="padding-top: 7px" width="140"><label for="fApplyNow"><?php echo _('Apply Now Button'); ?>:</label></td>
		<td>
			<div id="dApplyNowContent">
				<?php if ( !empty( $apply_now ) ) { ?>
					<img src="http://<?php echo ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'], $apply_now['value']; ?>" alt="<?php echo _('Apply Now'); ?>" style="padding-bottom: 10px;" />
					<br />
					<p><?php echo _('Place "[apply-now]" into the page content above to place the location of your image. When you view your website, this will be replaced with the image uploaded.'); ?></p>
				<?php } ?>
			</div>
			<input type="file" name="fApplyNow" id="fApplyNow" />
		</td>
	</tr>
	<tr>
		<td><label for="tApplyNowLink"><?php echo _('Apply Now Link'); ?>:</label></td>
		<td><input type="text" class="tb" name="tApplyNowLink" id="tApplyNowLink" value="<?php echo $apply_now_link; ?>" /></td>
	</tr>
</table>