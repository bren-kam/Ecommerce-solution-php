<?php 
global $user;

$sc = new Shopping_Cart;

$zip_codes = $sc->get_shipping_zip_codes( $_GET['wsmid'] );

$html = '';

foreach ( $zip_codes as $z ) {
	$html .= '<tr id="trZip' . $z . '"><td>' . $z . '</td><td><a href="#" id="aRemoveZip' . $z . '" class="remove-zip"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete') . '"/></a><input type="hidden" name="hZip' . $z . '" value="' . $z . '"/></td></tr>';
}

?>
<form action="/ajax/shopping-cart/edit-shipping-zip-codes/" ajax="1" method="post">
	<table>
    	<?php echo $html; ?>
        <tr id="trAddZipCode">
        	<td><input id="tNewZipCode" name="tNewZipCode" type="text" class="tb" /></td>
            <td><a href="#" id="aNewZipCode" class="button"><?php echo _('Add Zip'); ?></a></td>
        </tr>
	</table>
    <input type="hidden" name="hID" value="<?php echo $_GET['wsmid']; ?>" />
    <?php nonce::field('edit-shipping-zip-codes'); ?>
    <input type="submit" class="button" value="Save Changes" />
</form>