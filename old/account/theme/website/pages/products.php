<table cellpadding="0" cellspacing="0" class="form">
	<tr>
		<td><label for="sTop"><?php echo _('Top or bottom of the page'); ?>:</label></td>
		<td>
			<select name="sTop" id="sTop">
				<option value="1"><?php echo _('Top'); ?></option>
				<option value="0"<?php if ( '0' == $top ) echo ' selected="selected"'; ?>><?php echo _('Bottom'); ?></option>
			</select>
		</td>
	</tr>
</table>