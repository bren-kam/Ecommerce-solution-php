<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Coupons | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteCoupon $coupon
 * @var WebsiteShippingMethod[] $shipping_methods
 * @var array $free_shipping_methods
 * @var string $errs
 * @var string $js_validation
 */

$title = ( $coupon->id ) ? _('Edit') : _('Add');
$title .= ' ' . _('Coupon');

echo $template->start( $title, '../sidebar' );

if ( isset( $errs ) )
    echo "<p class='red'>$errs</p>";
?>
<form name="fAddEditCoupon" action="<?php if ( $coupon->id ) echo '?wcid=' . $coupon->id; ?>" method="post">
<table>
    <tr>
        <td><label for="tName"><?php echo _('Name'); ?>:</label></td>
        <td><input type="text" class="tb" name="tName" id="tName" value="<?php echo ( empty( $_POST['tName'] ) ) ? $coupon->name : $_POST['tName']; ?>" maxlength="50" /></td>
    </tr>
    <tr>
        <td><label for="tCode"><?php echo _('Code'); ?>:</label></td>
        <td><input type="text" class="tb" name="tCode" id="tCode" value="<?php echo ( empty( $_POST['tCode'] ) ) ? $coupon->code : $_POST['tCode']; ?>" maxlength="20" /></td>
    </tr>
    <tr>
        <td><label for="rType"><?php echo _('Type'); ?>:</label></td>
        <td>
            <?php
            $coupon_type = ( empty( $_POST['rType'] ) ) ? $coupon->type : $_POST['rType'];

            if ( 'Flat Rate' == $coupon_type ) {
                $flat_rate = true;
                $percentage = false;
            } else {
                $flat_rate = false;
                $percentage = true;
            }

            // @Fix Flat Rate uses the english lange -- need to make it language independent
            ?>
            <p style="padding-bottom:7px"><input type="radio" name="rType" id="rType" class="rb" value="Flat Rate"<?php if ( $flat_rate ) echo ' checked="checked"'; ?> /> <label for="rType"><?php echo _('Dollar Amount'); ?></label></p>
            <p><input type="radio" name="rType" id="rType2" class="rb" value="Percentage"<?php if ( $percentage ) echo ' checked="checked"'; ?> /> <label for="rType2"><?php echo _('Percentage'); ?></label></p>
        </td>
    </tr>
    <tr>
        <td><label for="tAmount"><?php echo _('Amount Discounted'); ?>:</label></td>
        <td><input type="text" class="tb" name="tAmount" id="tAmount" value="<?php echo ( empty( $_POST['tAmount'] ) ) ? $coupon->amount : $_POST['tAmount']; ?>" maxlength="20" /></td>
    </tr>
    <tr>
        <td><label for="tMinimumPurchaseAmount"><?php echo _('Minimum Purchase'); ?>:</label></td>
        <td><input type="text" class="tb" name="tMinimumPurchaseAmount" id="tMinimumPurchaseAmount" value="<?php echo ( empty( $_POST['tMinimumPurchaseAmount'] ) ) ? $coupon->minimum_purchase_amount : $_POST['tMinimumPurchaseAmount']; ?>" maxlength="20" /></td>
    </tr>
    <tr>
        <td><label for="cbStoreWide"><?php echo _('Store-Wide'); ?>:</label></td>
        <td><input type="checkbox" name="cbStoreWide" id="cbStoreWide" class="cb" value="1"<?php if ( !empty( $_POST ) && isset( $_POST['cbStoreWide'] ) || ( empty( $_POST ) && $coupon->store_wide ) ) echo ' checked="checked"'; ?> /> <label for="cbStoreWide"><?php echo _('Store-Wide Coupon?'); ?></label></td>
    </tr>
    <tr>
        <td><label for="cbBuyOneGetOneFree"><?php echo _('Buy One Get One Free'); ?>:</label></td>
        <td><input type="checkbox" name="cbBuyOneGetOneFree" id="cbBuyOneGetOneFree" class="cb" value="1"<?php if ( !empty( $_POST ) && isset( $_POST['cbBuyOneGetOneFree' ]) || ( empty( $_POST ) && $coupon->buy_one_get_one_free ) ) echo ' checked="checked"'; ?> /> <label for="cbBuyOneGetOneFree"><?php echo _('Buy One Get One Free'); ?></label></td>
    </tr>
    <tr>
        <td><label for="cbFreeShippingOptions"><?php echo _('Free Shipping Methods'); ?>:</label></td>
        <td>
            <?php
            if ( empty( $_POST ) && $coupon->id ) {
                $check_shipping_methods = $free_shipping_methods;
            } elseif ( isset( $_POST['cbFreeShippingMethods'] ) ) {
                $check_shipping_methods = $_POST['cbFreeShippingMethods'];
            } else {
                $check_shipping_methods = '';
            }

            // Make sure it's the right type
            if ( !is_array( $check_shipping_methods ) )
                $check_shipping_methods = array();

            foreach ( $shipping_methods as $method ) {
                $checked = ( in_array( $method->id, $check_shipping_methods ) ) ? ' checked="checked"' : '';

                echo '<p><input type="checkbox" class="cb" name="cbFreeShippingMethods[]" id="cbFreeShippingMethod' . $method->id . '" value="' . $method->id . '"' . $checked . '> <label for="cbFreeShippingMethod' . $method->id . '">' . $method->name . '</label></p>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td><label for="tItemLimit"><?php echo _('Item Limit'); ?>:</label></td>
        <td><input type="text" class="tb" name="tItemLimit" id="tItemLimit" maxlength="10" value="<?php echo ( empty( $_POST['tItemLimit'] ) ) ? $coupon->item_limit : $_POST['tItemLimit']; ?>" /></td>
    </tr>
    <tr>
        <td>
            <label for="tStartDate"><?php echo _('Start &amp; End Date'); ?>:</label><br />
            <small>(<?php echo _('optional'); ?>)</small>
        </td>
        <td>
            <input type="text" name="tStartDate" id="tStartDate" class="tb date" maxlength="10" value="<?php echo ( empty( $_POST['tStartDate'] ) && '0000-00-00' != $coupon->date_start ) ? $coupon->date_start : $_POST['tStartDate']; ?>" style="width:75px" />
            <input type="text" name="tEndDate" id="tEndDate" class="tb date" maxlength="10" value="<?php echo ( empty( $_POST['tEndDate'] ) && '0000-00-00' != $coupon->date_end ) ? $coupon->date_end : $_POST['tEndDate']; ?>" style="width:75px" />
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="submit" class="button" value="<?php echo ( $coupon->id ) ? _('Update Coupon') : _('Add Coupon'); ?>" /></td>
    </tr>
</table>
<?php nonce::field('add_edit'); ?>
</form>
<?php
echo $js_validation;
echo $template->end();
?>