<?php
/**
 * @page Billing Information
 *
 * @var array $settings
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
                <form method="post" role="form">
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
                        
                    <br />
                    <header class="panel-heading">Features</header>
                    <br>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="checkbox col-lg-4">
                                <label for="website">
                                    <input type="checkbox" name="website" id="website" value="1">
                                    Website ($100)
                                </label>
                            </div>
                            <div class="checkbox col-lg-4">
                                <label for="shopping-cart">
                                    <input type="checkbox" name="shopping-cart" id="shopping-cart" value="1">
                                    Shopping Cart ($100)
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="checkbox col-lg-4">
                                <label for="product-catalog">
                                    <input type="checkbox" name="product-catalog" id="product-catalog" value="1">
                                    Product Catalog ($100)
                                </label>
                            </div>
                            <div class="checkbox col-lg-4">
                                <label for="blog">
                                    <input type="checkbox" name="blog" id="blog" value="1">
                                    Blog ($100)
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="checkbox col-lg-4">
                                <label for="email-marketing">
                                    <input type="checkbox" name="email-marketing" id="email-marketing" value="1">
                                    Email Marketing ($100)
                                </label>
                            </div>
                            <div class="checkbox col-lg-4">
                                <label for="social-media">
                                    <input type="checkbox" name="social-media" id="social-media" value="1">
                                    Social Media ($100)
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="checkbox col-lg-4">
                                <label for="geo-marketing">
                                    <input type="checkbox" name="geo-marketing" id="geo-marketing" value="1">
                                    Geo-Marketing ($100)
                                </label>
                            </div>
                        </div>

                    </div>
                    <br>

                    <header class="panel-heading">Catalogs</header>

                    <p>
                        <?php nonce::field( 'billing_information' ); ?>
                        <button type="submit" class="btn btn-primary">Update Account</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>