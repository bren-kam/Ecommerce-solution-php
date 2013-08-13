<?php
/**
 * @var string $hide_all_maps
 * @var string $multiple_location_map
 * @var WebsiteLocation[] $locations
 */

?>
<div id="dContactUs">
	<h3><?php echo _('Locations'); ?>:</h3>

	<div id="dContactUsList">
		<?php
		nonce::field( 'set_pagemeta', '_set_pagemeta' );
        
		if ( !empty( $locations ) )
        foreach ( $locations as $location ) {
			?>
			<div class="contact" id="dLocation<?php echo $location->id; ?>">
				<h2><span class="location"><?php echo $location->name; ?></span></h2>
				<div class="contact-left">
					<span class="address"><?php echo $location->address; ?></span><br />
					<span class="city"><?php echo $location->city; ?></span>, <span class="state"><?php echo $location->state; ?></span> <span class="zip"><?php echo $location->zip; ?></span>
				</div>
				<div class="contact-right">
					<span class="phone"><?php echo $location->phone; ?></span><br />
					<span class="fax"><?php echo $location->fax; ?></span><br />
				</div>

				<div style="float:right">
					<span class="email"><?php echo $location->email; ?></span><br />
					<span class="website"><?php echo $location->website; ?></span>
				</div>

				<br />
				<br clear="all" />
				<br />
				<strong><?php echo _('Store Hours'); ?>:</strong>
				<br />
				<span class="store-hours"><?php echo $location->store_hours; ?></span>
				<div class="actions">
					<a href="#" class="delete-address" title="<?php echo _('Delete Address'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete Address'); ?>" /></a>
					<a href="#" class="edit-address" title="<?php echo _('Edit Address'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit Address'); ?>" /></a>
				</div>
			</div>
		<?php } ?>
	</div>
	<br clear="all" /><br />
	<br />

    <p><a href="#dAddEditAddress" class="button" id="add-location" rel="dialog"><?php echo _('Add Location'); ?></a></p>
	<br />
	<input type="checkbox" class="cb" id="cbHideAllMaps" value="yes"<?php if ( 'true' == $hide_all_maps ) echo ' checked="checked"'; ?> /> <label for="cbHideAllMaps"><?php echo _('Hide All Maps'); ?></label><br/>
	<br/>
	<input type="checkbox" class="cb" id="cbMultipleLocationMap" value="yes"<?php if ( 'true' == $multiple_location_map ) echo ' checked="checked"'; ?> /> <label for="cbMultipleLocationMap"><?php echo _('Multiple Location Map'); ?></label>
</div>

<div id="dAddEditAddress" class="hidden">
    <form action="/website/add-edit-location/" method="post" ajax="1">
        <table class="form width-auto">
            <tr>
                <td><input type="text" class="tb" name="name" id="name" tabindex="1" placeholder="Name"></td>
                <td width="10%">&nbsp;</td>
                <td><input type="text" class="tb" name="address" id="address" tabindex="6" placeholder="Address"></td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="phone" id="phone" maxlength="21" tabindex="2" placeholder="Phone"></td>
                <td>&nbsp;</td>
                <td><input type="text" class="tb" name="city" id="city" tabindex="7" placeholder="City"></td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="fax" id="fax" maxlength="21" tabindex="2" placeholder="Fax"></td>
                <td>&nbsp;</td>
                <td>
                    <select id="sState" tabindex="8">
                        <option value="">-- <?php echo _('Select State'); ?> --</option>
                        <?php data::states(); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="email" id="email" maxlength="200" tabindex="3" placeholder="Email"></td>
                <td>&nbsp;</td>
                <td><input type="text" class="tb" name="zip" id="zip" maxlength="10" tabindex="9" placeholder="Zip"></td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="website" id="website" maxlength="200" tabindex="4" placeholder="Website"></td>
            </tr>
            <tr>
                <td><textarea  name="store-hours" id="store-hours" cols="30" rows="3" tabindex="5" placeholder="Store Hours"></textarea></td>
            </tr>
            <tr>
                <td><input type="submit" class="button" value="<?php echo _('Add'); ?>"></td>
            </tr>
        </table>
        <input type="hidden" name="wlid" value="">
    </form>
</div>