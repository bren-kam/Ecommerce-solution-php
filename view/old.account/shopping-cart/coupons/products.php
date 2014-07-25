<?php
/**
 * @package Grey Suit Retail
 * @page Products in Coupon | Shopping Cart
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteCoupon[] $coupons
 */

echo $template->start( _('Products in Coupon'), '../sidebar' );
?>

<div class="relative">
    <label id="lCoupon" for="sCoupon">Showing Products in Coupon:</label>
    <select id="sCoupon">
        <?php
        foreach ( $coupons as $coupon ) {
            $selected = '';
            ?>
            <option value="<?php echo $coupon->id; ?>"<?php echo $selected; ?>><?php echo $coupon->name; ?></option>
        <?php } ?>
    </select>
    <table ajax="/shopping-cart/coupons/list-products/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="40%"><?php echo _('Product'); ?></th>
                <th width="10%"><?php echo _('Sku'); ?></th>
                <th width="25%"><?php echo _('Brand'); ?></th>
                <th width="25%"><?php echo _('Category'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<?php nonce::field( 'store_session', '_store_session' ) ?>

<?php echo $template->end(); ?>