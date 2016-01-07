<?php

/**
 * @var Product $parent_product
 * @var array $child_prices
 */

?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Pricing Tools: <?php echo $parent_product->name ?>
            </header>

            <div class="panel-body">

                <form method="post" role="form">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <td>Wholesale Price</td>
                                <td>MAP Price</td>
                                <td>Everyday Price</td>
                                <td>Sale Price</td>
                                <td>MSRP</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($child_prices as $product): ?>
                                <?php if ($product)?>
                                <tr>
                                    <td>
                                        <?php echo $product['sku'] ?> <br>
                                        <small><?php echo $product['name'] ?></small>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-right" name="product[<?php echo $product['product_id'] ?>][wholesale_price]" value="<?php echo $product['wholesale_price'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-right" name="product[<?php echo $product['product_id'] ?>][map_price]" value="<?php echo $product['map_price'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-right" name="product[<?php echo $product['product_id'] ?>][price]" value="<?php echo $product['price'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-right" name="product[<?php echo $product['product_id'] ?>][sale_price]" value="<?php echo $product['sale_price'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-right" name="product[<?php echo $product['product_id'] ?>][alternate_price]" value="<?php echo $product['alternate_price'] ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php nonce::field('pricing_tool'); ?>

                    <p>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>
