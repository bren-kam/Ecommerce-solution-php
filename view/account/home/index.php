<?php
/**
 * @package Grey Suit Retail
 * @page Dashboard | Home
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $date_start_visitors
 * @var string $date_end_visitors
 * @var string $date_start_signups
 * @var string $date_end_signups
 * @var array $visitors
 * @var array $signups
 */
?>

<?php //if ( $show_new_features ): ?>
    <div class="row-fluid">
        <div class="col-lg-12">
            <div id="new-features">
                <a href="/kb/article/?aid=243">
                    <span><i class="fa fa-info-circle"></i></span>
                    OUR LATEST UPDATES AND FEATURES. We are constantly adding more features and functionality into your Website and dashboard to help you improve your online business and productivity. Click here to see what’s new.
                </a>
                <a href="javascript:;" id="dismiss-new-features"><i class="fa fa-times"></i></a>
            </div>
        </div>
    </div>
<?php //endif; ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Welcome to your Dashboard
            </header>
            <div class="panel-body">

                <div class="row-fluid">
                    <div class="col-lg-9">
                        <h2>Getting Started</h2>
                        <table class="table table-hover table-striped">
                            <tbody>
                                <?php foreach ( $kbh_home_articles as $kbh_article ): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo url::add_query_arg( 'aid', $kbh_article->id, '/kb/article/' ); ?>" title="<?php echo $kbh_article->title; ?>" target="_blank">
                                                <i class="fa fa-book"></i>
                                                <?php echo $kbh_article->title; ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <p><a href="/kb/" class="btn btn-primary">See More</a></p>
                    </div>

                    <div class="col-lg-3">
                        <section class="panel twt-panel">
                            <div class="twt-feed purple-bg">
                                <h1><?php echo $online_specialist->contact_name ?></h1>
                                <a href="javascript:;">
                                  <img src="<?php echo $online_specialist->photo ? $online_specialist->photo : '/images/profile-avatar.jpg' ?>">
                                </a>
                            </div>
                            <div class="weather-category twt-category">
                                <p class="title"><i class="fa fa-phone"></i> Contact Support</p>

                                <?php if ($online_specialist->work_phone): ?>
                                    <p><span class="purple">P.</span> <?php echo $online_specialist->work_phone ?></p>
                                <?php endif; ?>
                                <p><span class="purple">E.</span> <a href="mailto:<?php echo $online_specialist->email ?>"><?php echo $online_specialist->email ?></a></p>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="row-fluid">
                    <?php if ( $website_orders ): ?>
                        <div class="col-lg-6">
                            <h2>New Orders</h2>

                            <table class="table table-hover table-striped">
                                <tbody>
                                    <?php foreach($website_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="/shopping-cart/orders/view/?woid=<?php echo $order->website_order_id ?>"><?php echo $order->name . ' - ' . reset($order->items)->name ?></a>
                                            </td>
                                            <td class="text-right"><?php echo (new DateTime($order->date_created))->format('m/d/Y') ?></td>
                                            <td class="text-right cell-no-wrap">$ <?php echo number_format($order->total_cost, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p><a href="/shopping-cart/orders/" class="btn btn-primary">See All</a></p>
                        </div>
                    <?php else: ?>
                        <div class="col-lg-6">
                            <h2>New Orders</h2>
                            <section class="panel twt-panel no-service-box">
                                <div class="twt-feed blue-bg">
                                    <h1>It looks like you haven't signed up for a shopping cart.</h1>
                                    <a href="javascript:;">
                                        <img src="/images/shopping-cart-round.png" />
                                    </a>
                                </div>
                                <div class="weather-category twt-category">
                                    <p>Add a shopping cart to your Website for a low monthly fee and.</p>
                                    <p><a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#support-modal">Let your website start making money!</a></p>
                                </div>
                            </section>
                        </div>
                    <?php endif; ?>

                    <?php if ( $website_reaches ): ?>
                        <div class="col-lg-6">
                            <h2>New Reaches</h2>

                            <table class="table table-hover table-striped">
                                <tbody>
                                    <?php foreach($website_reaches as $reach): ?>
                                        <tr>
                                            <td>
                                                <a href="/sales-desk/reach/?wrid=<?php echo $reach->website_reach_id ?>"><?php echo $reach->name . ( $reach->meta['product-name'] ? ' - ' . $reach->meta['product-name'] : '' ) ?></a>
                                            </td>
                                            <td class="text-right"><?php echo (new DateTime($reach->date_created))->format('m/d/Y') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p><a href="/sales-desk/" class="btn btn-primary">See All</a></p>
                        </div>
                    <?php else: ?>
                        <div class="col-lg-6">
                            <div class="white-box">
                                <p>New Reaches not Available</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( $user->account->live ): ?>
                <div class="row-fluid">
                    <div class="col-lg-12">
                        <h2>
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
                        </h2>

                        <div id="visitors-graph"></div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="row-fluid">
                        <div class="col-lg-12">
                            <div class="white-box">
                                <p>Number of Visitors not Available</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( $user->account->email_marketing == 1 ): ?>
                <br>
                <div class="row-fluid">
                    <div class="col-lg-12">
                        <h2>
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
                        </h2>

                        <div id="signups-graph"></div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="row-fluid">
                        <div class="col-lg-12">
                            <h2>Email Signups</h2>
                            <section class="panel twt-panel no-service-box">
                                <div class="twt-feed blue-bg">
                                    <h1>It looks like you haven't signed up for email marketing.</h1>
                                    <a href="javascript:;">
                                        <img src="/images/email-marketing-round.png" />
                                    </a>
                                </div>
                                <div class="weather-category twt-category">
                                    <p>Sign up for Email Marketing to get full access to users' information.</p>
                                    <p><a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#support-modal">Try it out Today!</a></p>
                                </div>
                            </section>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </section>
    </div>
</div>


<script>
    var AnalyticsSettings = <?php echo json_encode( array( 'visitors' => $visitors, 'signups' => $signups ) ); ?>;
</script>

