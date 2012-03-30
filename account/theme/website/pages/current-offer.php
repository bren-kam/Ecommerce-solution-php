<table cellpadding="0" cellspacing="0" class="form">
	<tr>
		<td><label for="tEmail"><?php echo _('Email'); ?>:</label></td>
		<td><input type="text" class="tb" name="tEmail" value="<?php echo $metadata['email']; //['value']; ?>" /></td>
	</tr>
	<tr>
		<td class="top" style="padding-top: 7px"><label for="fCoupon"><?php echo _('Coupon'); ?>:</label></td>
		<td>
			<div id="dCouponContent"><?php if ( !empty( $coupon ) ) { ?><img src="<?php echo 'http://', ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'], $coupon['value']; ?>" alt="<?php echo _('Coupon'); ?>" style="padding-bottom: 10px;" /><br /><?php } ?></div>
			<input type="file" name="fCoupon" id="fCoupon" />
			<br /><br />
			<p><input type="checkbox" class="cb" name="cbDisplayCoupon" id="cbDisplayCoupon" value="yes"<?php if ( 'yes' == $metadata['display-coupon'] ) echo ' checked="checked"'; ?> /> <label for="cbDisplayCoupon"><?php echo _('Display coupon on Current Offer page?'); ?></label></p>
			<p><input type="checkbox" class="cb" name="cbEmailCoupon" id="cbEmailCoupon" value="yes"<?php if ( 'no' != $metadata['email-coupon'] ) echo ' checked="checked"'; ?> /> <label for="cbEmailCoupon"><?php echo _('Email coupon on Current Offer page?'); ?></label></p>
		</td>
	</tr>
</table>