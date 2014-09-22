<?php
/**
 * @package Grey Suit Retail
 * @page All Products | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountProduct[] $products
 */

?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                All Products
            </header>

            <div class="panel-body">

                <table class="dt display table table-bordered table-striped" perPage="30,50,100">
                    <thead>
                        <th>Name</th>
                        <th>SKU</th>
                        <th sort="1">Category</th>
                        <th>Brand</th>
                    </thead>

                    <tbody>
                        <?php if ( is_array( $products ) ): ?>
                            <?php foreach ( $products as $product ): ?>
                                <tr>
                                    <td><?php echo $product->name; ?></td>
                                    <td><?php echo $product->sku; ?></td>
                                    <td><?php echo $product->category; ?></td>
                                    <td><?php echo $product->brand; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </section>
    </div>
</div>