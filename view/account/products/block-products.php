<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Hide Products
            </header>
            <div class="panel-body">
                <?php echo $form ?>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Hidden Products
            </header>
            <div class="panel-body">

                <form action="/products/unblock-products/" method="post" role="form">
                    <?php if ( $blocked_products ): ?>
                        <?php foreach ( $blocked_products as $product ): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="unblock-products[]" value="<?php echo $product->id; ?>" />
                                    <?php echo "{$product->sku} - {$product->name}" ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <p>
                        <?php nonce::field( 'unblock_products' ) ?>
                        <button type="submit" class="btn btn-primary">Unhide Products</button>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>