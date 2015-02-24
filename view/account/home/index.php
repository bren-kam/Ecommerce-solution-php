<div class="row-fluid">
    <?php if ( $website_orders ): ?>
        <div class="col-lg-6">
            <section class="panel">

                <header class="panel-heading">
                    New Orders
                </header>
                <div class="panel-body">

                    <table class="table table-hover table-striped">
                        <tbody>
                            <?php foreach($website_orders as $order): ?>
                                <tr>
                                    <td>
                                        <a href="/shopping-cart/orders/view/?woid=<?php echo $order->website_order_id ?>"><strong><?php echo $order->name . ' - ' . reset($order->items)->name ?></strong></a>
                                    </td>
                                    <td class="text-right"><?php echo (new DateTime($order->date_created))->format('m/d/Y') ?></td>
                                    <td class="text-right">$ <?php echo number_format($order->total_cost, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p><a href="/shopping-cart/orders/" class="btn btn-primary">See All</a></p>

                </div>
            </section>
        </div>
    <?php endif; ?>

    <?php if ( $website_reaches ): ?>
        <div class="col-lg-6">
            <section class="panel">

                <header class="panel-heading">
                    New Reaches
                </header>
                <div class="panel-body">

                    <table class="table table-hover table-striped">
                        <tbody>
                            <?php foreach($website_reaches as $reach): ?>
                                <tr>
                                    <td>
                                        <a href="/products/reaches/reach/?wrid=<?php echo $reach->website_reach_id ?>"><strong><?php echo $reach->name . ( $reach->meta['product-name'] ? ' - ' . $reach->meta['product-name'] : '' ) ?></strong></a>
                                    </td>
                                    <td class="text-right"><?php echo (new DateTime($reach->date_created))->format('m/d/Y') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p><a href="/product/reaches/" class="btn btn-primary">See All</a></p>

                </div>
            </section>
        </div>
    <?php endif; ?>
</div>

