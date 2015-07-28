<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - View Order
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteCart $cart
 */

?>

<div class="row-fluid">
    <div class="<?php echo ( $cart->billing_phone ) ? "col-lg-3" : "col-lg-6" ?>">
        <section class="panel">
            <header class="panel-heading">
                Cart #<?php echo $cart->id; ?>
            </header>

            <div class="panel-body">
                <ul>
                    <li><strong>Name:</strong> <?php echo $cart->name; ?></li>
                    <li><strong>Email:</strong> <?php echo $cart->email; ?></li>
                    <li><strong>Phone:</strong> <?php echo $cart->phone; ?></li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-lg-3">
        <section class="panel">
            <header class="panel-heading">
                Shipping Information
            </header>

            <div class="panel-body">
                <?php if ( $cart->website_order_id ): ?>
                    <p>
                        <?php echo $cart->shipping_name; ?><br />
                        <?php echo $cart->shipping_address1; ?><br />
                        <?php echo $cart->shipping_city, ', ', $cart->shipping_state, ' ', $cart->shipping_zip; ?><br />
                    </p>

                    <?php if ( $cart->shipping_track_number ): ?>
                        <p>
                            <strong>Express Delivery Tracking Codes:</strong><br>
                            <?php echo str_replace( ',', "<br>\n", $cart->shipping_track_number ); ?>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Cart not converted</p>
                <?php endif ?>
            </div>
        </section>
    </div>

    <?php if ( $cart->billing_phone ): ?>
        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Contact Info
                </header>

                <div class="panel-body">
                    <p>
                        <?php if ( !empty( $cart->phone ) ) echo 'Main Phone: ' . $cart->phone . '<br>'; ?>
                        <?php if ( !empty( $cart->billing_phone ) ) echo 'Billing Phone: ' . $cart->billing_phone . '<br>'; ?>
                        <?php if ( isset( $cart->billing_alt_phone ) ) echo 'Alternate Phone'. ': '. $cart->billing_alt_phone; ?>
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
                        <td class="text-right">$<?php echo number_format( $cart->total_price + $cart->coupon_discount - $cart->shipping_price - $cart->tax_price, 2 ); ?></td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td class="text-right">$<?php echo number_format( $cart->tax_price, 2 ); ?></td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td class="text-right">$<?php echo number_format( $cart->shipping_price, 2 ); ?></td>
                    </tr>
                    <?php if ( $cart->coupon_discount != 0 ): ?>
                        <tr>
                            <td>Coupon Discount</td>
                            <td class="text-right">$<?php echo number_format( $cart->coupon_discount, 2 ); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Total Ticket Sale</td>
                        <td class="text-right">$<?php echo number_format( $cart->total_price, 2 ); ?></td>
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
                Cart Details
            </header>

            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2"><strong>Image</strong></div>
                    <div class="col-lg-7"><strong>Description</strong></div>
                    <div class="col-lg-1 text-right"><strong>Price</strong></div>
                    <div class="col-lg-1 text-right"><strong>Quantity</strong></div>
                    <div class="col-lg-1 text-right"><strong>Total</strong></div>
                </div>
                <div class="row"><div class="col-lg-12">&nbsp;</div></div>
                <?php foreach ( $cart->items as $item ): ?>
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
