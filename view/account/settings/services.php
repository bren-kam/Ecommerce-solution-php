<?php
/**
 * @page Billing Information
 *
 * @var User $user
 * @var array $settings
 * @var bool $success
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/settings/billing-information/">Billing Information</a></li>
                    <li class="active"><a href="/settings/services/">Services</a></li>
                </ul>

                <h3>Services</h3>
            </header>

            <div class="panel-body">
                <?php if ( $success ) { ?>
                    <div class="alert alert-success">
                        <p><strong>Success!</strong></p>
                        <p>Your services have been successfully changed and your new monthly bill is now <strong><?php echo $settings['arb-subscription-amount']; ?></strong>.</p>
                    </div>
                <?php } else { ?>
                <input type="hidden" id="original-subscription" value="<?php echo $settings['arb-subscription-amount']; ?>">
                <form method="post" role="form" id="form-services">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4">
                                <table class="table-condensed">
                                    <tr>
                                        <td><label>Amount:</label></td>
                                        <td>$<?php echo $settings['arb-subscription-amount']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><label>Interval:</label></td>
                                        <td>Monthly</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="services">
                        <br>
                        <header class="panel-heading">Website Add-Ons</header>
                        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="checkbox col-lg-8">
                                    <label for="shopping-cart">
                                        <input type="checkbox" name="shopping-cart" id="shopping-cart" data-group="Website Add-On" data-price="50" value="1"<?php if ( $user->account->shopping_cart ) echo ' checked="checked" data-default="checked"'; ?>>
                                        Shopping Cart ($50)
                                    </label>
                                </div>
                                <div class="checkbox col-lg-8">
                                    <label for="blog">
                                        <input type="checkbox" name="blog" id="blog" value="1" data-group="Website Add-On" data-price="100"<?php if ( $user->account->blog ) echo ' checked="checked" data-default="checked"'; ?>>
                                        Blog ($100)
                                    </label>
                                </div>
                                <div class="checkbox col-lg-8">
                                    <label for="email-marketing">
                                        <input type="checkbox" name="email-marketing" id="email-marketing" data-group="Website Add-On" data-price="100" value="1"<?php if ( $user->account->email_marketing ) echo ' checked="checked" data-default="checked"'; ?>>
                                        Email Marketing ($100)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <br>
                        <header class="panel-heading">Social Media</header>
                        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="checkbox col-lg-8">
                                    <label for="social-media">
                                        <input type="checkbox" name="social-media" id="social-media" data-group="Social Media" data-price="99" value="1"<?php if ( $user->account->social_media ) echo ' checked="checked" data-default="checked"'; ?>>
                                        Social Media Services ($99)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <br>
                        <header class="panel-heading">GeoMarketing</header>
                        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="checkbox col-lg-8">
                                    <label for="geo-marketing">
                                        <input type="checkbox" name="geo-marketing" id="geo-marketing" data-group="GeoMarketing" data-price="100" value="1"<?php if ( $user->account->geo_marketing ) echo ' checked="checked" data-default="checked"'; ?>>
                                        Listing Services ($100)
                                    </label>
                                </div>
                                <div class="checkbox col-lg-8">
                                    <label for="gm-reviews">
                                        <input type="checkbox" name="gm-reviews" id="gm-reviews" data-group="GeoMarketing" data-price="100" value="1">
                                        Consumer Review Services ($100)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>

                    <div id="service-change" style="display:none">
                        <header class="panel-heading panel-danger">Review Changes</header>
                        <br>
                        <div class="container-fluid">
                            <div class="row">
                            <div id="new-services" class="alert alert-success"></div>
                            <div id="old-services" class="alert alert-danger"></div>
                            <div class="alert alert-warning">
                                <p><strong>New Monthly Price: $<span class="new-price"><?php echo $settings['arb-subscription-amount']; ?></span> <span class="price-difference"></span></strong></p>
                                <div class="checkbox">
                                    <label for="verify">
                                        <input type="checkbox" name="verify" id="verify">I, <?php echo $user->contact_name; ?>, agree to pay the new total monthly price, <strong>$<span class="new-price"></span></strong>.
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">Confirm Service Changes</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="new-price" id="new-price" value="">
                    <?php nonce::field( 'services' ); ?>
                </form>
                <?php } ?>
            </div>
        </section>
    </div>
</div>