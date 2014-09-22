<?php
/**
 * @package Grey Suit Retail
 * @page Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category[] $categories
 * @var int $product_count
 * @var WebsiteCoupon[] $coupons
 * @var array $pricing_points
 */

$remove_nonce = nonce::create( 'manually_priced_remove' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Manually Priced Products

                <span class="pull-right">
                    <a class="btn btn-sm btn-default" href="/products/manually-priced-remove-all/?_nonce=<?php echo nonce::create( 'manually_priced_remove_all' ) ?>">Clear All</a>
                    <a class="btn btn-sm btn-default" href="/products/manually-priced-lock-all/?_nonce=<?php echo nonce::create( 'manually_priced_lock_all' ) ?>">Lock All Products</a>
                </span>
            </header>

            <div class="panel-body">
                <div class="adv-table">
                    <table class="display table table-bordered table-striped dt" perPage="30,50,100">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ( $products as $product ): ?>
                                <tr>
                                    <td>
                                        <?php echo $product->name; ?> <br>
                                        <div class="actions">
                                            <a href="/products/manually-priced-remove/?product-id=<?php echo $product->product_id ?>&_nonce=<?php echo $remove_nonce ?>" confirm="Are you sure you want to make this product Auto Priced?" ajax="1">Remove</a>
                                        </div>
                                    </td>
                                    <td><?php echo $product->sku; ?></td>
                                    <td><?php echo $product->category; ?></td>
                                    <td><?php echo $product->brand; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
