<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Payment Settings
            </header>
            <div class="panel-body">

                <form action="" id="fPaymentSettings" method="post" name="fPaymentSettings">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>All Payment Methods</h3>

                            <div class="form-group">
                                <label for="sStatus">Status:</label>
                                <select class="form-control" id="sStatus" name="sStatus">
                                    <option value="0" <?php if ( !$settings['payment-gateway-status'] ) echo 'selected'?>>Testing</option>
                                    <option value="1" <?php if ( $settings['payment-gateway-status'] ) echo 'selected'?>>Live</option>
                                </select>
                            </div><br>

                            <div class="form-group">
                                <label for="sSelectedGateway">Process Payments With:</label>
                                <select class="form-control" id="sSelectedGateway" name="sSelectedGateway">
                                    <option value="aim" <?php if ( $settings['selected-gateway'] == 'aim' ) echo 'selected' ?>>Authorize.net/AIM</option>
                                    <option value="stripe" <?php if ( $settings['selected-gateway'] == 'stripe' ) echo 'selected' ?>>Stripe</option>
                                </select>
                            </div>

                            <h3>Authorize.net AIM</h3>

                            <div class="form-group">
                                <label for="tAIMLogin">AIM Login:</label>
                                <input class="form-control" id="tAIMLogin" maxlength="30" name="tAIMLogin" type="text" value="<?php echo security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ); ?>">
                            </div>

                            <div class="form-group">
                                <label for="tAIMTransactionKey">AIM Transaction Key:</label>
                                <input class="form-control" id="tAIMTransactionKey" maxlength="30" name="tAIMTransactionKey" type="text" value="<?php echo security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                            </div>

                            <h3>Stripe</h3>

                            <div class="form-group">
                                <label for="tStripeId">Stripe ID:</label>
                                <input type="text" class="form-control" disabled="disabled" value="<?php echo $stripe_account['stripe_user_id'] ?>">
                            </div>

                            <div class="form-group">
                                <label for="tStripePublishableKey">Stripe Publishable Key:</label>
                                <input type="text" class="form-control" disabled="disabled" value="<?php echo $stripe_account['stripe_publishable_key'] ?>">
                            </div>

                            <div class="form-group">
                                <label for="tStripeId">Stripe Secret Key:</label>
                                <input type="text" class="form-control" disabled="disabled" value="<?php echo $stripe_account['access_token'] ?>">
                            </div>

                            <?php if (isset($stripe_account['email'])): ?>
                                <div class="form-group">
                                    <label for="tStripeEmail">Stripe Email Address:</label>
                                    <input type="text" class="form-control" disabled="disabled" value="<?php echo $stripe_account['email'] ?>">
                                </div>
                                <p>
                                    Fill out your bank account details to collect the money from your sales.
                                    <a target="_blank" href="https://dashboard.stripe.com/account/activate?client_id=<?php echo Config::key('stripe-client-id') ?>&user_id=<?php echo $stripe_account['stripe_user_id'] ?>">Start now.</a>
                                    |
                                    <a href="/shopping-cart/settings/stripe-unlink/?_nonce=<?php echo nonce::create("stripe_unlink") ?>" confirm="Are you sure you want to Unlink your shop from Stripe?">Unlink your Stripe Account</a>
                                </p>
                            <?php endif; ?>

                            <?php if ( !isset($stripe_account) ): ?>
                                <p>
                                    <a class="btn btn-primary" data-toggle="modal" data-target="#modal-create-stripe-account" href="javascript:;"">Create Stripe Account</a>
                                    <a class="btn btn-primary" href="http://account.development.greysuitretail.com/shopping-cart/settings/stripe-connect/?website-id=1352&user-id=2696">I already have a Stripe Account</a>
                                </p>
                            <?php endif; ?>

                            <h3>PayPal Express Checkout</h3>

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
                                <a class="btn btn-primary" href="/shopping-cart/settings/test-paypal/?_nonce=<?php echo nonce::create('test_paypal') ?>" id="test-paypal">Test PayPal Credentials</a>
                            </p>

                            <h3>Crest Financial</h3>

                            <div class="form-group">
                                <label for="tCrestFinancialDealerId">Dealer ID:</label>
                                <input class="form-control" id="tCrestFinancialDealerId" maxlength="10" name="tCrestFinancialDealerId" type="text" value="<?php echo security::decrypt( base64_decode( $settings['crest-financial-dealer-id'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>

                    <?php nonce::field('payment_settings') ?>

                </form>

            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal-create-stripe-account" tabindex="-1" role="dialog" aria-hidden="true" >
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

    <!-- Real Uploader -->
    <div id="ticket-uploader"></div>
    <?php nonce::field( 'upload_to_ticket', '_upload_to_ticket' ) ?>
</div>
