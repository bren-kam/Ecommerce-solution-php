<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - View Order
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteOrder $order
 */
?>

<div class="row-fluid">
    <div class="<?php echo ( $order->billing_phone ) ? "col-lg-3" : "col-lg-6" ?>">
        <section class="panel">
            <header class="panel-heading">
                Order #<?php echo $order->id ?>
            </header>

            <div class="panel-body">
                <ul>
                    <li><strong>Email:</strong> <?php echo $order->email ?></li>
                    <li><strong>Phone:</strong> <?php echo $order->phone ?></li>

                    <li><strong>Shipping Method:</strong> <?php echo $order->shipping_method ?></li>
                    <?php if ( $order->website_ashley_express_shipping_method_id ): ?>
                        <li><strong>Shipping Method (Express Delivery):</strong> <?php echo $order->ashley_express_shipping_method ?></li>
                    <?php elseif ( $order->is_ashley_express() ): ?>
                        <li><strong>Express Delivery Order</strong></li>
                    <?php endif; ?>

                    <?php if( $order->authorize_only ): ?>
                        <li><strong>Authorize Only</strong></li>
                    <?php endif; ?>
                </ul>

                <?php nonce::field( 'update_status' ); ?>
                <div class="form-group">
                    <label for="status">Order Status:</label>
                    <select id="status" class="form-control" data-order-id="<?php echo $order->id ?>">
                        <option value="0"<?php if ( '0' == $order->status ) echo ' selected="selected"'; ?>><?php echo _('Purchased'); ?></option>
                        <option value="1"<?php if ( '1' == $order->status ) echo ' selected="selected"'; ?>><?php echo _('Pending'); ?></option>
                        <option value="2"<?php if ( '3' == $order->status ) echo ' selected="selected"'; ?>><?php echo _('Received'); ?></option>
                        <option value="2"<?php if ( '4' == $order->status ) echo ' selected="selected"'; ?>><?php echo _('Shipped'); ?></option>
                        <option value="2"<?php if ( '2' == $order->status ) echo ' selected="selected"'; ?>><?php echo _('Delivered'); ?></option>
                        <option value="-1"<?php if ( '-1' == $order->status ) echo ' selected="selected"'; ?>><?php echo _('Declined'); ?></option>
                    </select>
                </div>
            </div>
        </section>
    </div>

    <div class="col-lg-3">
        <section class="panel">
            <header class="panel-heading">
                Shipping Information
            </header>

            <div class="panel-body">
                <p>
                    <?php echo $order->shipping_name; ?><br />
                    <?php echo $order->shipping_address1; ?><br />
                    <?php echo $order->shipping_city, ', ', $order->shipping_state, ' ', $order->shipping_zip; ?><br />
                </p>

                <?php if ( $order->shipping_track_number ): ?>
                    <p>
                        <strong>Express Delivery Tracking Codes:</strong><br>
                        <?php echo str_replace( ',', "<br>\n", $order->shipping_track_number ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php if ( $order->billing_phone ): ?>
        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Contact Info
                </header>

                <div class="panel-body">
                    <p>
                        <?php if ( !empty( $order->phone ) ) echo 'Main Phone: ' . $order->phone . '<br>'; ?>
                        <?php if ( !empty( $order->billing_phone ) ) echo 'Billing Phone: ' . $order->billing_phone . '<br>'; ?>
                        <?php if ( isset( $order->billing_alt_phone ) ) echo 'Alternate Phone'. ': '. $order->billing_alt_phone; ?>
                    </p>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <div class="col-lg-3">
        <section class="panel">
            <header class="panel-heading">
                Totals
            </header>

            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">$<?php echo number_format( $order->total_cost + $order->coupon_discount - $order->shipping_price - $order->tax_price, 2 ); ?></td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td class="text-right">$<?php echo number_format( $order->tax_price, 2 ); ?></td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td class="text-right">$<?php echo number_format( $order->shipping_price, 2 ); ?></td>
                    </tr>
                    <?php if ( $order->coupon_discount != 0 ): ?>
                        <tr>
                            <td>Coupon Discount</td>
                            <td class="text-right">$<?php echo number_format( $order->coupon_discount, 2 ); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Total</td>
                        <td class="text-right">$<?php echo number_format( $order->total_cost, 2 ); ?></td>
                    </tr>
                </table>
            </div>
        </section>
    </div>

</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Order Details
            </header>

            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2"><strong>Image</strong></div>
                    <div class="col-lg-7"><strong>Description</strong></div>
                    <div class="col-lg-1 text-right"><strong>Price</strong></div>
                    <div class="col-lg-1 text-right"><strong>Quantity</strong></div>
                    <div class="col-lg-1 text-right"><strong>Total</strong></div>
                </div>
                <?php foreach ( $order->items as $item ): ?>
                    <div class="row">
                        <div class="col-lg-2">
                            <img src="<?php echo $item->image; ?>" width="80" />
                        </div>
                        <div class="col-lg-7">
                            <p>
                                <strong><?php echo $item->name; ?></strong> <br />
                                SKU: <?php echo $item->sku ? $item->sku : $item->product_sku; ?> <br />
                                <?php if ( !empty( $item->store_sku ) ) echo 'Store SKU: ' . $item->store_sku; ?>
                            </p>

                            <?php
                            $additional_price = 0;
                            // Make sure there are options
                            if ( ( !empty( $item->product_options ) ) || ( !empty( $item->extra ) ) ):
                                ?>
                                <p>
                                    <a href="#options<?php echo $item->id; ?>" class="toggle-options" title="Show Options"><strong><span>[ + ]</span> Options</strong></a> <br/>

                                <ul id="#options<?php echo $item->id; ?>" class="hidden">
                                    <?php
                                    if ( is_array( $item->product_options ) ):
                                        foreach ( $item->product_options as $product_option ):

                                            switch ( $product_option->option_type ):
                                                case 'checkbox':
                                                    echo '<li>', $product_option->option_name;

                                                    if ( $product_option->price != 0 )
                                                        echo ' ($', number_format( $product_option->price, 2 ), ')';

                                                    echo '</li>';
                                                    break;
                                                case 'select':
                                                    echo '<li>', $product_option->option_name, ' - ', $product_option->list_item_value;

                                                    if ( $product_option->price != 0 )
                                                        echo ' ($', number_format( $product_option->price, 2 ), ')';

                                                    echo '</li>';
                                                    break;
                                            endswitch;

                                            $additional_price += $product_option->price;
                                        endforeach;
                                    endif;

                                    if ( is_array( $item->extra ) && !empty( $item->extra ) ):
                                        foreach ( $item->extra as $name => $value ):
                                            echo '<li>' . ucwords( $name ) . ": " . $value . '</li>';
                                        endforeach;
                                    endif;
                                    ?>
                                </ul>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-1 text-right">
                            $<?php $item_price = $item->price + $additional_price; echo number_format( $item_price, 2 ); ?>
                        </div>
                        <div class="col-lg-1 text-right">
                            <?php echo number_format( $item->quantity ); ?>
                        </div>
                        <div class="col-lg-1 text-right">
                            $<?php echo number_format( $item_price * $item->quantity, 2 ); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </section>
    </div>
</div>