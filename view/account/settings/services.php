<?php
/**
 * @page Billing Information
 *
 * @var User $user
 * @var array $settings
 */
$settings['arb-subscription-amount'] = 399;
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
                        
                    <br>
                    <header class="panel-heading">Website Add-Ons</header>
                    <br>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="checkbox col-lg-8">
                                <label for="shopping-cart">
                                    <input type="checkbox" name="shopping-cart" id="shopping-cart" data-price="50" value="1" <?php if ( $user->account->shopping_cart ) echo ' checked="checked"'; ?>>
                                    Shopping Cart ($50)
                                </label>
                            </div>
                            <div class="checkbox col-lg-8">
                                <label for="blog">
                                    <input type="checkbox" name="blog" id="blog" value="1" data-price="100" <?php if ( $user->account->blog ) echo ' checked="checked"'; ?>>
                                    Blog ($100)
                                </label>
                            </div>
                            <div class="checkbox col-lg-8">
                                <label for="email-marketing">
                                    <input type="checkbox" name="email-marketing" id="email-marketing" data-price="100" value="1" <?php if ( $user->account->email_marketing ) echo ' checked="checked"'; ?>>
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
                                    <input type="checkbox" name="social-media" id="social-media" data-price="99" value="1" <?php if ( $user->account->social_media ) echo ' checked="checked"'; ?>>
                                    Social Media ($99)
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
                                <label for="gm-listings">
                                    <input type="checkbox" name="gm-listings" id="gm-listings" data-price="100" value="1">
                                    Listing Services ($100)
                                </label>
                            </div>
                            <div class="checkbox col-lg-8">
                                <label for="gm-reviews">
                                    <input type="checkbox" name="gm-reviews" id="gm-reviews" data-price="100" value="1">
                                    Consumer Review Services ($100)
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div id="price-change" style="display:none">
                        <header class="panel-heading panel-danger">Services Change</header>
                        <br>
                        <div class="container-fluid">
                            <div class="row">
                            <p>
                                <?php nonce::field( 'services' ); ?>
                                Old Price: $<span class="old-price"><?php echo $settings['arb-subscription-amount']; ?></span><br>
                                <strong>New Monthly Price: $<span class="new-price"><?php echo $settings['arb-subscription-amount']; ?></span> (<span class="price-difference"></span>)<br></strong>
                                <br>
                                <button type="submit" class="btn btn-primary">Review Order</button>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>