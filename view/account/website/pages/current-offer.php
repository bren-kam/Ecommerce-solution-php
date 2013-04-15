<?php
/**
 * @var User $user
 * @var AccountPageAttachment $coupon
 */
?>
<table>
	<tr>
		<td><label for="tEmail"><?php echo _('Email'); ?>:</label></td>
		<td><input type="text" class="tb" name="tEmail" id="tEmail" value="<?php echo $metadata['email']; ?>" /></td>
	</tr>
	<tr>
		<td class="top"><label><?php echo _('Coupon'); ?>:</label></td>
		<td>
			<div id="dCouponContent"><?php if ( !empty( $coupon ) ) { ?><img src="<?php echo ( stristr( $coupon->value, 'http' ) ) ? $coupon->value : 'http://' . $user->account->domain . $coupon->value; ?>" alt="<?php echo _('Coupon'); ?>" style="padding-bottom: 10px;" /><br /><?php } ?></div>
            <a href="#" id="aUploadImage" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
            <a href="#" class="button loader hidden" id="upload-image-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
            <div class="hidden-fix position-absolute" id="upload-image"></div>
            <?php nonce::field( 'upload_image', '_upload_image' ); ?>
			<br /><br />
			<p><input type="checkbox" class="cb" name="cbDisplayCoupon" id="cbDisplayCoupon" value="yes"<?php if ( 'yes' == $metadata['display-coupon'] ) echo ' checked="checked"'; ?> /> <label for="cbDisplayCoupon"><?php echo _('Display coupon on Current Offer page?'); ?></label></p>
			<p><input type="checkbox" class="cb" name="cbEmailCoupon" id="cbEmailCoupon" value="yes"<?php if ( 'no' != $metadata['email-coupon'] ) echo ' checked="checked"'; ?> /> <label for="cbEmailCoupon"><?php echo _('Email coupon on Current Offer page?'); ?></label></p>
		</td>
	</tr>
</table>