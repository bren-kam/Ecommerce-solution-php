<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Coupons | Shopping Cart
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

$coupon_type = ( empty( $_POST['rType'] ) ) ? $coupon->type : $_POST['rType'];

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
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $coupon->id ? 'Edit' : 'Add'?> Coupon
            </header>

            <div class="panel-body">

                <form method="post" action="<?php if ( $coupon->id ) echo '?wcid=' . $coupon->id; ?>" role="form">

                    <div class="form-group">
                        <label for="tName">Name:</label>
                        <input type="text" class="form-control" id="tName" name="tName" value="<?php echo ( empty( $_POST['tName'] ) ) ? $coupon->name : $_POST['tName']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="tCode">Code:</label>
                        <input type="text" class="form-control" id="tCode" name="tCode" value="<?php echo ( empty( $_POST['tCode'] ) ) ? $coupon->code : $_POST['tCode']; ?>" />
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="rType" value="Flat Rate" <?php if ( $coupon_type == 'Flat Rate' ) echo 'checked' ?> />
                            Discount as Fixed Dollar Amount
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="rType" value="Percentage" <?php if ( $coupon_type == 'Percentage' ) echo 'checked' ?> />
                            Discount as Percentage on Cart Price
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="tAmount">Discount Amount:</label>
                        <input type="text" class="form-control" id="tAmount" name="tAmount" value="<?php echo ( empty( $_POST['tAmount'] ) ) ? $coupon->amount : $_POST['tAmount']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="tMinimumPurchaseAmount">Minimum Purchase Amount:</label>
                        <input type="text" class="form-control" id="tMinimumPurchaseAmount" name="tMinimumPurchaseAmount" value="<?php echo ( empty( $_POST['tMinimumPurchaseAmount'] ) ) ? $coupon->minimum_purchase_amount : $_POST['tMinimumPurchaseAmount']; ?>" />
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="cbStoreWide" value="1" <?php if ( !empty( $_POST ) && isset( $_POST['cbStoreWide'] ) || ( empty( $_POST ) && $coupon->store_wide ) ) echo 'checked="checked"'; ?> />
                            This is a Store Wide Coupon
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="cbBuyOneGetOneFree" value="1" <?php if ( !empty( $_POST ) && isset( $_POST['cbBuyOneGetOneFree'] ) || ( empty( $_POST ) && $coupon->buy_one_get_one_free ) ) echo 'checked="checked"'; ?> />
                            This is a "Buy One Get One Free" Coupon
                        </label>
                    </div>

                    <br />

                    <?php
                    foreach ( $shipping_methods as $method ) :
                        $checked = ( in_array( $method->id, $check_shipping_methods ) ) ? ' checked="checked"' : '';
                        echo '<div class="checkbox"><label><input type="checkbox" name="cbFreeShippingMethods[]" value="' . $method->id . '"' . $checked . '>Free Shipping Method Coupon: ' . $method->name . '</label></div>';
                    endforeach;
                    ?>

                    <div class="form-group">
                        <label for="tItemLimit">Item Limit:</label>
                        <input type="text" class="form-control" id="tItemLimit" name="tItemLimit" value="<?php echo ( empty( $_POST['tItemLimit'] ) ) ? $coupon->item_limit : $_POST['tItemLimit']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="tStartDate">Start &amp; End Date <small>(optional)</small>:</label>
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="input-sm form-control" name="tStartDate" value="<?php echo ( empty( $_POST['tStartDate'] ) && '0000-00-00' != $coupon->date_start ) ? $coupon->date_start : $_POST['tStartDate']; ?>"/>
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control" name="tEndDate" value="<?php echo ( empty( $_POST['tEndDate'] ) && '0000-00-00' != $coupon->date_end ) ? $coupon->date_end : $_POST['tEndDate']; ?>" />
                        </div>

<!--                        <input type="text" class="form-control" id="tStartDate" name="tStartDate" />
                        <input type="text" class="form-control" id="tEndDate" name="tEndDate" value="<?php echo $coupon->name ?>" /> -->
                    </div>

                    <p class="text-right">
                        <?php echo nonce::field('add_edit') ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>