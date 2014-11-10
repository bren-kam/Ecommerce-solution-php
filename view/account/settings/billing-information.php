<?php nonce::field( 'update_billing_information', '_billing_information' ); ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <form method="post" role="form">
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
                        <?php nonce::field( 'logo_and_phone' ); ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>