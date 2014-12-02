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
                    <li><a href="/products/services/">Services</a></li>
                    <li><a href="/settings/billing-information/">Billing Information</a></li>
                </ul>

                <h3>Services</h3>
            </header>

            <div class="panel-body">
                <form method="post" role="form">
                    <header class="panel-heading">Billing Information</header>
                    <br />
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
                    <header class="panel-heading">Billing Address</header>
                    <br>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="first-name">First Name:</label>
                                <input type="text" class="form-control" name="first-name" id="first-name" maxlength="30">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="last-name">Last Name:</label>
                                <input type="text" class="form-control" name="last-name" id="last-name" maxlength="30">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-8">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" name="address" id="address" maxlength="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-8">
                                <label for="city">City:</label>
                                <input type="text" class="form-control" name="city" id="city" maxlength="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="state">State:</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">-- Select State</option>
                                    <?php data::states(); ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="zip">Zip:</label>
                                <input type="text" class="form-control" name="zip" id="zip" maxlength="10">
                            </div>
                        </div>
                    </div>
                    <br>

                    <header class="panel-heading">Credit Card</header>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group col-lg-8">
                                <label for="ccnum">Credit Card Number:</label>
                                <input type="text" class="form-control" name="ccnum" id="ccnum" maxlength="20">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="ccexpm">Expiration Month:</label>
                                <select name="ccpexpm" id="ccpexpm" class="form-control">
                                    <option value="">-- Select Month --</option>
                                    <?php data::months(); ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="ccexpm">Expiration Year:</label>
                                <select name="ccpexpy" id="ccpexpy" class="form-control">
                                    <option value="">-- Select Year --</option>
                                    <?php
                                    $year = date('Y');
                                    for( $i = $year; $i < $year + 10; $i++ ) { ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p>
                        <?php nonce::field( 'billing_information' ); ?>
                        <button type="submit" class="btn btn-primary">Update Credit Card Information</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>