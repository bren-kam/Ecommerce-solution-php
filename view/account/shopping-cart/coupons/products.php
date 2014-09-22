<?php nonce::field( 'store_session', '_store_session' ) ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Products in Coupon
            </header>

            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="coupon">Coupon:</label>
                            <select class="form-control" id="coupon">
                                <?php foreach ( $coupons as $coupon ): ?>
                                    <option value="<?php echo $coupon->id; ?>"><?php echo $coupon->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/shopping-cart/coupons/list-products/" perPage="30,50,100">
                        <thead>
                            <th sort="1">Product</th>
                            <th>SKU</th>
                            <th>Brand</th>
                            <th>Category</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>