<?php

$nonce = nonce::create('payment_settings');

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Payment Settings

                <p class="pull-right">
                    <?php if ( $settings['payment-gateway-status'] == 1 ): ?>
                        <a class="btn btn-primary" href="/shopping-cart/settings/payment-test-mode-disable/?_nonce=<?php echo nonce::create('payment_test_mode_disable') ?>">Shopping Cart is LIVE, put it in TEST MODE.</a>
                    <?php else: ?>
                        <a class="btn btn-default" href="/shopping-cart/settings/payment-test-mode-enable/?_nonce=<?php echo nonce::create('payment_test_mode_enable') ?>">Shopping cart is in TEST MODE, make it LIVE.</a>
                    <?php endif; ?>
                </p>
            </header>
            <div class="panel-body">

                <h4>Add Credit Card Payments</h4>
                <table class="table table-bordered">
                    <tr>
                        <td class="v-align text-center col-lg-3">
                            <p><img src="/images/payment-logos/auth-net.png"></p>
                            <p><small><a target="_blank" href="http://www.authorize.net/"><span class="glyphicon glyphicon-question-sign"></span> Get more Info</a></small></p>
                        </td>
                        <td class="v-align text-center col-lg-6">
                            <ul>
                                <li>Merchant account application required</li>
                                <li>Integrated checkout on your site</li>
                                <li>2.9% + .30c/transaction & setup fee</li>
                                <li>Authorize.net charges $24/mnth and a one time setup fee of $49</li>
                            </ul>
                        </td>
                        <td class="v-align text-center col-lg-3">
                            <?php if( security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ) ): ?>
                                <p class="connected">
                                    <span><img src="/images/payment-logos/check.png"></span>
                                    <span><strong>Connected as <br><?php echo security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ) ?></strong></span>
                                </p>
                                <p><a class="btn btn-default" href="javascript:;" data-toggle="modal" data-target="#modal-authorize-net">Settings</a></p>
                            <?php else: ?>
                                <p><a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#modal-authorize-net">Enable</a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="v-align text-center col-lg-3">
                            <p><img src="/images/payment-logos/stripe.png"></p>
                            <p><small><a target="_blank" href="https://stripe.com/us/pricing"><span class="glyphicon glyphicon-question-sign"></span> Get more Info</a></small></p>
                        </td>
                        <td class="v-align text-center col-lg-6">
                            <ul>
                                <li>Integrated checkout on your site to accept credit cards</li>
                                <li>No monthly fees, no refund costs, no hidden fees</li>
                                <li>2.9% + .30c per successful charge</li>
                                <li>Instantaneously accept credit cards on your site</li>
                                <li>Earnings are transferred to your bank account on a 2-day rolling basis</li>
                            </ul>
                        </td>
                        <td class="v-align text-center col-lg-3">
                            <?php if ( isset($stripe_account['stripe_user_id']) ): ?>
                                <p class="connected">
                                    <span><img src="/images/payment-logos/check.png"></span>
                                    <span><strong>Connected as <br><?php echo $stripe_account['stripe_user_id'] ?></strong></span>
                                </p>
                                <p><a class="btn btn-primary" href="/shopping-cart/settings/stripe-connect/?website-id=<?php echo $user->account->id; ?>&user-id=<?php echo $user->id; ?>">Switch Stripe Account</a></p>                            
                            <?php else: ?>
                            <p><a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#modal-stripe-create-account">Create Stripe Account</a></p>
                            
                                <p><a class="btn btn-primary" href="/shopping-cart/settings/stripe-connect/?website-id=<?php echo $user->account->id; ?>&user-id=<?php echo $user->id; ?>">I already have a Stripe Account</a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <h4>Alternative Payment Settings</h4>
                <table class="table table-bordered">
                    <tr>
                        <td class="v-align text-center col-lg-3">
                            <p><img src="/images/payment-logos/paypal.png"></p>
                            <p><small><a target="_blank" href="/kb/article/?aid=193"><span class="glyphicon glyphicon-question-sign"></span> Get more Info</a></small></p>
                        </td>
                        <td class="v-align text-center col-lg-6">
                            <ul>
                                <li>No setup fee or monthly cost</li>
                                <li>2.9% + 30c/transaction</li>
                                <li>No application fees</li>
                            </ul>
                        </td>
                        <td class="v-align text-center col-lg-3">
                            <?php if( security::decrypt( base64_decode( $settings['paypal-express-username'] ), PAYMENT_DECRYPTION_KEY ) ): ?>
                                <p class="connected">
                                    <span><img src="/images/payment-logos/check.png"></span>
                                    <span><strong>Connected as <br><?php echo security::decrypt( base64_decode( $settings['paypal-express-username'] ), PAYMENT_DECRYPTION_KEY ) ?></strong></span>
                                </p>
                                <p><a class="btn btn-default" href="javascript:;" data-toggle="modal" data-target="#modal-paypal">Settings</a></p>
                            <?php else: ?>
                                <p><a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#modal-paypal">Enable</a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="v-align text-center col-lg-3">
                            <p><img src="/images/payment-logos/crest-financial.png"></p>
                            <p><small><a target="_blank" href="/kb/article/?aid=207"><span class="glyphicon glyphicon-question-sign"></span> Get more Info</a></small></p>
                        </td>
                        <td class="v-align text-center col-lg-6">
                            <ul>
                                <li>Instantaneous financing approval for your customers through Crest Financial</li>
                                <li>Retailer account application required</li>
                                <li>Retailers may choose between 0% and up to 6% financing per lease</li>
                                <li>There are no monthly fees or recurring fees for retailers</li>
                            </ul>
                        </td>
                        <td class="v-align text-center col-lg-3">
                            <?php if( security::decrypt( base64_decode( $settings['crest-financial-dealer-id'] ), PAYMENT_DECRYPTION_KEY ) ): ?>
                                <p class="connected">
                                    <span><img src="/images/payment-logos/check.png"></span>
                                    <span><strong>Connected as <br><?php echo security::decrypt( base64_decode( $settings['crest-financial-dealer-id'] ), PAYMENT_DECRYPTION_KEY ) ?></strong></span>
                                </p>
                                <p><a class="btn btn-default" href="javascript:;" data-toggle="modal" data-target="#modal-crest-financial">Settings</a></p>
                            <?php else: ?>
                                <p><a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#modal-crest-financial">Enable</a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- FlexShopper  -->
                    <tr>
                        <td class="v-align text-center col-lg-3">
                            <p><img src="/images/payment-logos/flexshopper.png"></p>
                            <p ><small><a target="_blank" href="/kb/article/?aid=251"><span class="glyphicon glyphicon-question-sign"></span> Get more Info</a></small></p>
                        </td>
                        <td class="v-align text-center col-lg-6">
                            <ul>
                                <li>Instantaneous financing approval for your customers through FlexShopper</li>
                                <li>Retailer account application required</li>
                                <li>FlexShopper will pay you for the ticket amount within 24 - 48 hours</li>
                                <li>There are no monthly fees or recurring fees for retailers</li>
                            </ul>
                        </td>
                        <td class="v-align text-center col-lg-3">
                            <?php if( security::decrypt( base64_decode( $settings['flexshopper-retailer-id'] ), PAYMENT_DECRYPTION_KEY ) ): ?>
                                <p class="connected">
                                    <span><img src="/images/payment-logos/check.png"></span>
                                    <span><strong>Connected as <br><?php echo security::decrypt( base64_decode( $settings['flexshopper-retailer-id'] ), PAYMENT_DECRYPTION_KEY ) ?></strong></span>
                                </p>
                                <p><a class="btn btn-default" href="javascript:;" data-toggle="modal" data-target="#modal-flexshopper">Settings</a></p>
                            <?php else: ?>
                                <p><a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#modal-flexshopper">Enable</a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- FlexShopper  -->
                </table>
                
                <?php if(security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ) && isset($stripe_account['stripe_user_id']) ) {?>
                <form action="" id="fPaymentSettings" method="post" name="fPaymentSettings">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>All Payment Methods</h4>

                            <div class="form-group">
                                <label for="sSelectedGateway">Process Credit Card Payments With:</label>
                                <select class="form-control" id="sSelectedGateway" name="sSelectedGateway">
                                    <option value="aim" <?php if ( $settings['selected-gateway'] == 'aim' ) echo 'selected' ?>>Authorize.net/AIM</option>
                                    <option value="stripe" <?php if ( $settings['selected-gateway'] == 'stripe' ) echo 'selected' ?>>Stripe</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="hidden" name="_nonce" value="<?php echo $nonce ?>">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <?php } ?>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal-authorize-net" tabindex="-1" role="dialog" aria-hidden="true" >
    <form action="/shopping-cart/settings/payment-settings/" method="post" role="form">
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Authorize.NET / AIM</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="tAIMLogin">AIM Login:</label>
                        <input class="form-control" id="tAIMLogin" maxlength="30" name="tAIMLogin" type="text" value="<?php echo security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ); ?>">
                    </div>

                    <div class="form-group">
                        <label for="tAIMTransactionKey">AIM Transaction Key:</label>
                        <input class="form-control" id="tAIMTransactionKey" maxlength="30" name="tAIMTransactionKey" type="text" value="<?php echo security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>

                    <input type="hidden" name="_nonce" value="<?php echo $nonce ?>">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modal-stripe-create-account" tabindex="-1" role="dialog" aria-hidden="true" >
    <form action="/shopping-cart/settings/stripe-create-account/" method="post" role="form">
        <?php nonce::field( 'stripe_create_account' )?>
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Create Stripe Account</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="tEmail">Email Address:</label>
                        <input type="text" class="form-control" name="email" id="tEmail" placeholder="Email Address..." value="<?php echo $user->email ?>" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create You Stripe Account</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modal-stripe" tabindex="-1" role="dialog" aria-hidden="true" >
    <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Stripe</h4>
            </div>
            <div class="modal-body">

                <?php if (isset($stripe_account['email'])): ?>
                    <div class="form-group">
                        <label for="tStripeEmail">Stripe Email Address:</label>
                        <input type="text" class="form-control" disabled="disabled" value="<?php echo $stripe_account['email'] ?>">
                    </div>
                    <p>
                        Fill out your bank account details to collect the money from your sales.
                        <a target="_blank" href="https://dashboard.stripe.com/account/activate?client_id=<?php echo Config::key('stripe-client-id') ?>&user_id=<?php echo $stripe_account['stripe_user_id'] ?>">Start now.</a>
                    </p>
                    <p>
                        <a href="/shopping-cart/settings/stripe-unlink/?_nonce=<?php echo nonce::create("stripe_unlink") ?>" confirm="Are you sure you want to Unlink your shop from Stripe?" class="btn btn-default btn-sm">Unlink your Stripe Account</a>
                    </p>
                <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-paypal" tabindex="-1" role="dialog" aria-hidden="true" >
    <form action="/shopping-cart/settings/payment-settings/" method="post" role="form">
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">PayPal Express Checkout</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="tPaypalExpressUsername">Username:</label>
                        <input class="form-control" id="tPaypalExpressUsername" maxlength="100" name="tPaypalExpressUsername" type="text" value="<?php echo security::decrypt( base64_decode( $settings['paypal-express-username'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>

                    <div class="form-group">
                        <label for="tPaypalExpressPassword">Password:</label>
                        <input class="form-control" id="tPaypalExpressPassword" maxlength="100" name="tPaypalExpressPassword" type="text" value="<?php echo security::decrypt( base64_decode( $settings['paypal-express-password'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>

                    <div class="form-group">
                        <label for="tPaypalExpressSignature">API Signature:</label>
                        <input class="form-control" id="tPaypalExpressSignature" maxlength="100" name="tPaypalExpressSignature" type="text" value="<?php echo security::decrypt( base64_decode( $settings['paypal-express-signature'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>

                    <div class="checkbox">
                        <label>
                            <input id="cbBillMeLater14" name="cbBillMeLater" type="checkbox" value="1" <?php if( $settings['bill-me-later'] ) echo 'checked' ?>>
                            Bill Me Later
                        </label>
                    </div>

                    <p>
                        <a class="btn btn-primary" href="/shopping-cart/settings/test-paypal/?_nonce=<?php echo nonce::create('test_paypal') ?>" id="test-paypal" ajax="1">Test PayPal Credentials</a>
                    </p>

                    <input type="hidden" name="_nonce" value="<?php echo $nonce ?>">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modal-crest-financial" tabindex="-1" role="dialog" aria-hidden="true" >
    <form action="/shopping-cart/settings/payment-settings/" method="post" role="form">
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Crest Financial</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="tCrestFinancialDealerId">Dealer ID:</label>
                        <input class="form-control" id="tCrestFinancialDealerId" maxlength="10" name="tCrestFinancialDealerId" type="text" value="<?php echo security::decrypt( base64_decode( $settings['crest-financial-dealer-id'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>

                    <input type="hidden" name="_nonce" value="<?php echo $nonce ?>">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="modal fade" id="modal-flexshopper" tabindex="-1" role="dialog" aria-hidden="true" >
    <form action="/shopping-cart/settings/payment-settings/" method="post" role="form">
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">FlexShopper</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="tFlexShopperRetailerId">Retailer ID:</label>
                        <input class="form-control" id="tFlexShopperRetailerId"  name="tFlexShopperRetailerId" type="text" value="<?php echo security::decrypt( base64_decode( $settings['flexshopper-retailer-id'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>
                    <div class="form-group">
                        <label for="tFlexShopperRetailerToken">Retailer Token:</label>
                        <input class="form-control" id="tFlexShopperRetailerToken"  name="tFlexShopperRetailerToken" type="text" value="<?php echo security::decrypt( base64_decode( $settings['flexshopper-retailer-token'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                    </div>                    


                    <input type="hidden" name="_nonce" value="<?php echo $nonce ?>">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
