<?php
/**
 * @package Grey Suit Retail
 * @page Auto Price | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category[] $categories
 * @var Brand[] $brands
 * @var WebsiteAutoPrice[] $auto_prices
 * @var array $auto_price_candidates
 * @var Product $product
 */

$remove_auto_price = nonce::create('remove_auto_price');
$delete_auto_price = nonce::create('delete_auto_price');
$run_auto_prices = nonce::create('run_auto_prices');
nonce::field('add_auto_price', '_add_auto_price');
?>


<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/products/auto-price/">Auto Price</a></li>
                    <li><a href="/products/price-multiplier/">Price Multiplier</a></li>
                    <li><a href="/products/product-prices/">Product Prices</a></li>
                </ul>
                <h3>Auto Price</h3>
            </header>

            <div class="panel-body">
                <p>On this page, you can price out your items based on the wholesale price provided to us via the manufacturer feed.</p>
                <p>Please enter your markup in the fields below, click "Save & Run". A "0" will be ignored.</p>

                <?php if ( empty( $auto_price_candidates ) ) : ?>
                    <p>Because your manufacturer has not provided us with the wholesale price, your markups will not affect any products in your catalog.</p>
                <?php else: ?>
                    <p>Your markups will affect the following products:</p>
                    <ul>
                        <?php foreach ( $auto_price_candidates as $candidate ): ?>
                            <li>* <?php echo $candidate['brand'] . ' - ' . $candidate['count'] . ' product(s)'; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p><a href="/products/download-non-autoprice-products/">Click here</a> to download a spreadsheet of all items that cannot be priced using the auto price.</p>

                <p class="text-center">
                    <img src="/images/auto-price-explanation.jpg" width="100%" />
                </p>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <div class="panel-body">

                <form method="post" role="form">
                    <table class="table" id="auto-price-list">
                        <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>MSRP</th>
                            <th>Everyday Price</th>
                            <th>Sale Price</th>
                            <th>Price Ending</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ( $auto_prices as $auto_price ): ?>
                            <tr>
                                <td>
                                    <?php
                                    if ( $auto_price->brand_id == 0 )
                                        echo 'All';
                                    else if ( $auto_price->brand_id == 1048576 )
                                        echo 'Ashley Express Program';
                                    else
                                        echo $brands[$auto_price->brand_id]->name;
                                    ?>
                                </td>
                                <td><?php echo  Category::$categories[$auto_price->category_id]->name ?></td>
                                <td><input type="text" class="form-control" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][alternate_price]" value="<?php echo (float) $auto_price->alternate_price; ?>"></td>
                                <td><input type="text" class="form-control" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][price]" value="<?php echo (float) $auto_price->price; ?>"></td>
                                <td><input type="text" class="form-control" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][sale_price]" value="<?php echo (float) $auto_price->sale_price; ?>"></td>
                                <td><input type="text" class="form-control" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][ending]" value="<?php echo (float) $auto_price->ending; ?>"></td>
                                <td>
                                    <a href="<?php echo url::add_query_arg( array( 'bid' => $auto_price->brand_id, 'cid' => $auto_price->category_id, '_nonce' => $remove_auto_price ), '/products/remove-auto-price/' ); ?>" ajax="1" confirm="Are you sure you want to remove these prices? This cannot be undone.">Remove Prices From Products</a> |
                                    <a href="<?php echo url::add_query_arg( array( 'bid' => $auto_price->brand_id, 'cid' => $auto_price->category_id, '_nonce' => $delete_auto_price ), '/products/delete-auto-price/' ); ?>" class="remove"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="hidden" id="auto-price-template">
                            <td>BRAND_NAME</td>
                            <td>CATEGORY_NAME</td>
                            <td><input type="text" class="form-control" name="auto-price[BRAND_ID][CATEGORY_ID][alternate_price]" /></td>
                            <td><input type="text" class="form-control" name="auto-price[BRAND_ID][CATEGORY_ID][price]" /></td>
                            <td><input type="text" class="form-control" name="auto-price[BRAND_ID][CATEGORY_ID][sale_price]" /></td>
                            <td><input type="text" class="form-control" name="auto-price[BRAND_ID][CATEGORY_ID][ending]" /></td>
                            <td>
                                <a href="/products/remove-auto-price/?bid=BRAND_ID&cid=CATEGORY_ID&_nonce=<?php echo $remove_auto_price?>" ajax="1" confirm="Are you sure you want to remove these prices? This cannot be undone.">Remove Prices From Products</a> |
                                <a href="/products/delete-auto-price/?bid=BRAND_ID&cid=CATEGORY_ID&_nonce=<?php echo $delete_auto_price?>" class="remove"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td>
                                <select id="brand-id" class="form-control">
                                    <option value="0">-- All --</option>
                                    <?php if ( $user->account->get_settings( 'ashley-express' ) ) : ?>
                                        <option value="1048576">-- Ashley Express Program --</option>
                                    <?php endif; ?>
                                    <?php foreach ( $brands as $brand ): ?>
                                        <option value="<?php echo $brand->id; ?>"><?php echo $brand->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select id="category-id" class="form-control">
                                    <?php foreach ( $categories as $category ): ?>
                                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" class="form-control" id="alternate_price"></td>
                            <td><input type="text" class="form-control" id="price"></td>
                            <td><input type="text" class="form-control" id="sale_price"></td>
                            <td><input type="text" class="form-control" id="ending"></td>
                            <td><a href="javascript:;" class="btn btn-default" id="add">Add</a></td>
                        </tr>
                        </tfoot>
                    </table>

                    <p>
                        <button type="submit" class="btn btn-primary">Save & Run</button>
                        <?php nonce::field('auto_price'); ?>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Demo - Try me!
            </header>

            <div class="panel-body">
                <p>Below you can test your markup to see how it will appear on your products (we have randomly selected an auto-pricing-eligible product from your catalog).</p>

                <p class="lead">Example wholesale price is: <strong>$<?php echo number_format( $product->price, 2 ); ?></strong>.</p>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>MSRP</th>
                        <th>Everyday Price</th>
                        <th>Sale Price</th>
                        <th>Price Ending</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $brands[$product->brand_id]->name; ?></td>
                        <td><?php echo Category::$categories[$product->category_id]->name; ?></td>
                        <td><input type="text" class="form-control" id="example_alternate_price" value="2"></td>
                        <td><input type="text" class="form-control" id="example_price" value="1"></td>
                        <td><input type="text" class="form-control" id="example_sale_price" value="0.5"></td>
                        <td><input type="text" class="form-control" id="example_ending"></td>
                        <td>
                            <a href="javascript:;" id="update" class="btn btn-default">Update</a>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-lg-6 text-right">
                        <img id="product-image" src="http://<?php echo $product->industry; ?>.retailcatalog.us/products/<?php echo $product->id; ?>/<?php echo current( $product->images ); ?>">
                    </div>
                    <div class="col-lg-6">
                        <h3><?php echo $product->name; ?></h3>
                        <p id="example-price">
                            <span id="example-sale-price" data-original-price="<?php echo $product->price; ?>">$<?php echo number_format( $product->price * 1.5, 2 ); ?></span>
                            <s id="example-regular-price">$<?php echo number_format( $product->price * 2, 2 ); ?></s>
                            <br>
                            <span class="msrp" id="example-msrp">(MSRP $<?php echo str_replace( '.00', '', number_format( $product->price * 3, 2 ) ); ?>)</span>
                        </p>
                        <p>
                            SKU: <?php echo $product->sku; ?><br>
                            Brand: <?php echo $product->brand; ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>