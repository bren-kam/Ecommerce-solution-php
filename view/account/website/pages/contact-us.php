<?php
/**
 * @var array $contacts
 */

?>
<div id="dContactUs">
	<h3><?php echo _('Locations'); ?>:</h3>

	<div id="dContactUsList">
		<?php
		nonce::field( 'set_pagemeta', '_set_pagemeta' );
		if ( !empty( $contacts ) ) {
			$i = 0;
			$addresses = unserialize( htmlspecialchars_decode( $contacts ) );

			if ( is_array( $addresses ) )
			foreach ( $addresses as $contact ) {
				if ( !isset( $contact['fax'] ) )
					$contact['fax'] = '';

				$i++;
			?>
			<div class="contact" id="dContact<?php echo $i; ?>">
				<h2><span class="location"><?php echo $contact['location']; ?></span></h2>
				<div class="contact-left">
					<span class="address"><?php echo $contact['address']; ?></span><br />
					<span class="city"><?php echo $contact['city']; ?></span>, <span class="state"><?php echo $contact['state']; ?></span> <span class="zip"><?php echo $contact['zip']; ?></span>
				</div>
				<div class="contact-right">
					<span class="phone"><?php echo $contact['phone']; ?></span><br />
					<span class="fax"><?php echo $contact['fax']; ?></span><br />
				</div>

				<div style="float:right">
					<span class="email"><?php echo $contact['email']; ?></span><br />
					<span class="website"><?php echo $contact['website']; ?></span>
				</div>

				<br />
				<br clear="all" />
				<br />
				<strong><?php echo _('Store Hours'); ?>:</strong>
				<br />
				<span class="store-hours"><?php echo $contact['store-hours']; ?></span>
				<div class="actions">
					<a href="#" class="delete-address" title="<?php echo _('Delete Address'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete Address'); ?>" /></a>
					<a href="#" class="edit-address" title="<?php echo _('Edit Address'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit Address'); ?>" /></a>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
		<input type="hidden" name="hAddresses" id="hAddresses" value="<?php if ( isset( $contacts['value'] ) ) echo htmlentities( $contacts['value'] ); ?>" />
	</div>
	<br clear="all" /><br />
	<br />

	<h3><?php echo _('Add Location'); ?></h3>
	<table cellpadding="0" cellspacing="0" class="form">
		<tr>
			<td width="100"><label for="tLocation"><?php echo _('Location'); ?>:</label></td>
			<td><input type="text" class="tb" id="tLocation" tabindex="1" /></td>
			<td width="10%">&nbsp;</td>
			<td width="100"><label for="tAddress"><?php echo _('Address'); ?>:</label></td>
			<td><input type="text" class="tb" id="tAddress" tabindex="6" /></td>
		</tr>
		<tr>
			<td><label for="tPhone"><?php echo _('Phone'); ?>:</label></td>
			<td><input type="text" class="tb" id="tPhone" maxlength="21" tabindex="2" /></td>
			<td>&nbsp;</td>
			<td><label for="tCity"><?php echo _('City'); ?>:</label></td>
			<td><input type="text" class="tb" id="tCity" tabindex="7" /></td>
		</tr>
		<tr>
			<td><label for="tFax"><?php echo _('Fax'); ?>:</label></td>
			<td><input type="text" class="tb" id="tFax" maxlength="21" tabindex="2" /></td>
			<td>&nbsp;</td>
			<td><label for="sState"><?php echo _('State'); ?>:</label></td>
			<td>
				<select id="sState" tabindex="8">
					<option value="">-- <?php echo _('Select a State'); ?> --</option>
					<?php data::states(); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="tEmail"><?php echo _('Email'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEmail" maxlength="200" tabindex="3" /></td>
			<td>&nbsp;</td>
			<td><label for="tZip"><?php echo _('Zip'); ?>:</label></td>
			<td><input type="text" class="tb" id="tZip" maxlength="10" tabindex="9" /></td>
		</tr>
		<tr>
			<td><label for="tWebsite"><?php echo _('Website'); ?>:</label></td>
			<td><input type="text" class="tb" id="tWebsite" maxlength="200" tabindex="4" /></td>
		</tr>
		<tr>
			<td class="top"><label for="taStoreHours"><?php echo _('Store Hours'); ?>:</label></td>
			<td><textarea id="taStoreHours" cols="30" rows="3" tabindex="5"></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><a href="#" id="aAddLocation" title="<?php echo _('Add Address'); ?>" tabindex="10"><?php echo _('Add Location'); ?></a></td>
		</tr>
	</table>
	<br />
	<input type="checkbox" class="cb" id="cbHideAllMaps" value="yes"<?php if ( 'true' == $hide_all_maps ) echo ' checked="checked"'; ?> /> <label for="cbHideAllMaps"><?php echo _('Hide All Maps'); ?></label><br/>
	<br/>
	<input type="checkbox" class="cb" id="cbMultipleLocationMap" value="yes"<?php if ( 'true' == $multiple_location_map ) echo ' checked="checked"'; ?> /> <label for="cbMultipleLocationMap"><?php echo _('Multiple Location Map'); ?></label>
</div>
<div id="dEditAddress" class="hidden">
<table cellpadding="0" cellspacing="0" class="form">
		<tr>
			<td width="100"><label for="tEditLocation"><?php echo _('Location'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditLocation" tabindex="11" /></td>
			<td width="10%">&nbsp;</td>
			<td width="100"><label for="tEditAddress"><?php echo _('Address'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditAddress" tabindex="16" /></td>
		</tr>
		<tr>
			<td><label for="tEditPhone"><?php echo _('Phone'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditPhone" maxlength="20" tabindex="12" /></td>
			<td>&nbsp;</td>
			<td><label for="tEditCity"><?php echo _('City'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditCity" tabindex="17" /></td>
		</tr>
		<tr>
			<td><label for="tEditFax"><?php echo _('Fax'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditFax" maxlength="21" tabindex="12" /></td>
			<td>&nbsp;</td>
			<td><label for="sEditState"><?php echo _('State'); ?>:</label></td>
			<td>
				<select id="sEditState" tabindex="18">
					<option value="">-- <?php echo _('Select a State'); ?> --</option>
					<?php data::states(); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="tEditEmail"><?php echo _('Email'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditEmail" maxlength="200" tabindex="13" /></td>
			<td>&nbsp;</td>
			<td><label for="tEditZip"><?php echo _('Zip'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditZip" maxlength="20" tabindex="19" /></td>
		</tr>
		<tr>
			<td><label for="tEditWebsite"><?php echo _('Website'); ?>:</label></td>
			<td><input type="text" class="tb" id="tEditWebsite" maxlength="200" tabindex="14" /></td>
		</tr>
		<tr>
			<td><label for="taEditStoreHours"><?php echo _('Store Hours'); ?>:</label></td>
			<td><textarea id="taEditStoreHours" cols="30" rows="3" tabindex="15"></textarea></td>
		</tr>
	</table>
	<input type="hidden" id="hContactID" />
    <div class="boxy-footer hidden">
        <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
        <p class="text-right col-2 float-right"><a href="#" id="aSaveAddress" title="<?php echo _('Save Address'); ?>" tabindex="20" class="button"><?php echo _('Save Address'); ?></a></p>
    </div>
</div>