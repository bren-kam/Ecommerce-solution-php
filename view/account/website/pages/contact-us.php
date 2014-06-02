<?php
/**
 * @var string $hide_all_maps
 * @var string $multiple_location_map
 * @var WebsiteLocation[] $locations
 */

?>
<div id="dlocationUs">
	<h3><?php echo _('Locations'); ?>:</h3>
    <p><?php echo _('Note: These locations are updated in real-time. Leaving the page will not undo a created or deleted location.'); ?></p>
	<div id="dContactUsList">
		<?php
		nonce::field( 'set_pagemeta', '_set_pagemeta' );
        nonce::field( 'get_location', '_get_location' );
        nonce::field( 'update_location_sequence', '_update_location_sequence' );
        $delete_location = nonce::create( 'delete_location' );
        $confirm_delete = _('Are you sure you want to delete this location? This cannot be undone.');
        
		if ( !empty( $locations ) )
        foreach ( $locations as $location ) {
			?>
			<div class="location" id="location-<?php echo $location->id; ?>">
				<h2><span class="name"><?php echo $location->name; ?></span></h2>
				<div class="location-left">
					<span class="address"><?php echo $location->address; ?></span><br />
					<span class="city"><?php echo $location->city; ?></span>, <span class="state"><?php echo $location->state; ?></span> <span class="zip"><?php echo $location->zip; ?></span>
				</div>
				<div class="location-right">
					<span class="phone"><?php echo $location->phone; ?></span><br />
					<span class="fax"><?php echo $location->fax; ?></span><br />
				</div>

				<div class="float-right">
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
					<a href="<?php echo url::add_query_arg( array( '_nonce' => $delete_location, 'wlid' => $location->id ), '/website/delete-location/' ); ?>" class="delete-location" title="<?php echo _('Delete'); ?>" ajax="1" confirm="<?php echo $confirm_delete; ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
					<a href="#<?php echo $location->id; ?>" class="edit-location" title="<?php echo _('Edit'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit'); ?>" /></a>
				</div>
			</div>
		<?php } ?>
	</div>
	<br clear="all" /><br />
	<br />

    <p><a href="#" class="button" id="add-location" rel="dialog"><?php echo _('Add Location'); ?></a></p>
	<br />
	<input type="checkbox" class="cb" id="cbHideAllMaps" value="yes"<?php if ( 'true' == $hide_all_maps ) echo ' checked="checked"'; ?> /> <label for="cbHideAllMaps"><?php echo _('Hide All Maps'); ?></label><br/>
	<br/>
	<input type="checkbox" class="cb" id="cbMultipleLocationMap" value="yes"<?php if ( 'true' == $multiple_location_map ) echo ' checked="checked"'; ?> /> <label for="cbMultipleLocationMap"><?php echo _('Multiple Location Map'); ?></label>
    <br/><br/>
    <label for="tEmail"><?php echo _('Email:'); ?></label><br/>
    <input type="text" class="tb" id="tEmail" name="tEmail" value="<?php echo $email ?>" />
</div>