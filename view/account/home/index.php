<?php
/**
 * @package Grey Suit Retail
 * @page Dashboard | Home
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $date_start
 * @var string $date_end
 */
?>

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
<?php if ( $user->account->live ): ?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Number of Visitors

                <div class="pull-right">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control" id="date-start-visitors" value="<?php echo $date_start_visitors; ?>" />
                        </div>
                        <div class="form-group"><span style="font-size: 16px; color:#777">to</span></div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="date-end-visitors" value="<?php echo $date_end_visitors; ?>" />
                        </div>
                    </form>
                </div>
            </header>

            <div class="panel-body">
                <canvas id="visitors-graph" width="1200" height="250" style="padding: 15px;margin-left: -15px;"></canvas>
            </div>
        </section>
    </div>
</div>
<?php endif; ?>

<?php if ( $user->account->email_marketing == 1 ): ?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Email Signups

                <div class="pull-right">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control" id="date-start-signups" value="<?php echo $date_start_signups; ?>" />
                        </div>
                        <div class="form-group"><span style="font-size: 16px; color:#777">to</span></div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="date-end-signups" value="<?php echo $date_end_signups; ?>" />
                        </div>
                    </form>
                </div>
            </header>

            <div class="panel-body">
                <canvas id="signups-graph" width="1200" height="250" style="padding: 15px;margin-left: -15px;"></canvas>
            </div>
        </section>
    </div>
</div>
<?php endif; ?>

<script>
    var AnalyticsSettings = <?php echo json_encode( array( 'plotting_label' => 'Page Views', 'visitors_keys' => array_keys($visitors), 'visitors_values' => array_values($visitors), 'signups_keys' => array_keys($signups), 'signups_values' => array_values($signups) ) ); ?>;
</script>

