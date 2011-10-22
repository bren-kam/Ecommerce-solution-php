<?php 
global $user;

$w = new Websites;

$taxes = $w->get_settings('taxes');
$taxes = unserialize( html_entity_decode( $taxes['taxes'] ) ); 

if ( array_key_exists( $_GET['state'], $taxes['zip_codes'] ) ) {
	$zip_codes = $taxes['zip_codes'][$_GET['state']];
} else {
	$zip_codes = false;
}

?>
<div style="height:400px; overflow:auto">
<table cellpadding="0" cellspacing="0" class="form" id="tEditZipCodes">
	<?php switch ( $zip_codes as $zip => $cost ) { ?>
	<tr>
		<td><?php echo $zip; ?></td>
		<td><input type="text" class="tb zip-code-cost" id="tZipCost<?php echo $zip; ?>" value="<?php echo $cost; ?>"/></td>
		<td><a href="javascript:;" class="remove-zip"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>"/></a></td>
	</tr>
	<?php } ?>
	<tr id="trAddZipCode">
		<td><input type="text" id="tNewTaxZipCode" class="tb" tmpval="<?php echo _('Enter Zip Code'); ?>..."/></td>
		<td><input type="text" id="tNewTaxZipCost" class="tb" tmpval="<?php echo _('Enter Tax'); ?>..."/></td>
		<td><a href="javascript:;" class="button" id="aNewTaxZipCode" title="<?php echo _('Add Zip'); ?>" error="<?php echo _('You must enter a valid zip code and amount.'); ?>"><?php echo _('Add Zip'); ?></a></td>
	</tr>
</table>
<br />
<a href="javascript:;" class="button" id="aSaveTaxZips"><?php echo _('Save Changes'); ?></a>
<input type="hidden" id="hState" value="<?php echo $_GET['state']; ?>" />
</div>