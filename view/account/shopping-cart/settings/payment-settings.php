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
                            <p class="form-group"><strong></strong>All Payment Methods</p>

                            <div class="form-group">
                                <label for="sStatus">Status:</label>
                                <select class="form-control" id="sStatus" name="sStatus">
                                    <option value="0" <?php if ( !$settings['payment-gateway-status'] ) echo 'selected'?>>Testing</option>
                                    <option value="1" <?php if ( $settings['payment-gateway-status'] ) echo 'selected'?>>Live</option>
                                </select>
                            </div><br>

                            <p class="form-group"><strong></strong>Authorize.net AIM</p>

                            <div class="form-group">
                                <label for="tAIMLogin">AIM Login:</label>
                                <input class="form-control" id="tAIMLogin" maxlength="30" name="tAIMLogin" type="text" value="<?php echo security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ); ?>">
                            </div>

                            <div class="form-group">
                                <label for="tAIMTransactionKey">AIM Transaction Key:</label>
                                <input class="form-control" id="tAIMTransactionKey" maxlength="30" name="tAIMTransactionKey" type="text" value="<?php echo security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ) ?>">
                            </div>

                            <p class="form-group"><strong></strong>Stripe</p>

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
                                    <a target="_blank" href="https://dashboard.stripe.com/account/activate?client_id=<?php echo Config::key('stripe-client-id') ?>&user_id=<?php echo $stripe_account['stripe_user_id'] ?>">Activate Your Stripe Account</a>
                                </p>
                            <?php endif; ?>

                            <?php if ( !isset($stripe_account) ): ?>
                                <p>
                                    <a class="btn btn-primary" href="http://account.development.greysuitretail.com/shopping-cart/settings/stripe-create-account/?_nonce=<?php echo nonce::create('stripe-create_account') ?>">Create Stripe Account</a>
                                    <a class="btn btn-primary" href="http://account.development.greysuitretail.com/shopping-cart/settings/stripe-connect/?website-id=1352&user-id=2696">I already have a Stripe Account</a>
                                </p>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="sSelectedGateway">Process Payments With:</label>
                                <select class="form-control" id="sSelectedGateway" name="sSelectedGateway">
                                    <option value="aim" <?php if ( $settings['selected-gateway'] == 'aim' ) echo 'selected' ?>>AIM</option>
                                    <option value="stripe" <?php if ( $settings['selected-gateway'] == 'stripe' ) echo 'selected' ?>>Stripe</option>
                                </select>
                            </div>

                            <br>

                            <p class="form-group"><strong></strong>PayPal Express Checkout</p>

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

                            <br>

                            <p class="form-group"><strong></strong>Crest Financial</p>

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