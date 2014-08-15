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

nonce::field('set_product_prices', '_set_product_prices');
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
                <h3>Product Prices</h3>
            </header>

            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-4">
                        <select id="sBrand" class="form-control">
                            <option value="">-- Select Brand --</option>
                            <?php foreach ( $brands as $brand ) : ?>
                                <option value="<?php echo $brand->id; ?>"><?php echo $brand->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <select id="sCategory" class="form-control">
                            <option value="">-- Select Category --</option>
                            <?php foreach ( $categories as $category ): ?>
                                <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-4 text-right">
                        <button type="button" class="btn btn-primary save">Save</button>
                    </div>
                </div>
                <br />

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" id="product-prices">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>MSRP</th>
                                <th>Everyday Price</th>
                                <th>Sale Price</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <p class="text-right">
                    <button type="button" class="btn btn-primary save">Save</button>
                </p>

            </div>
        </section>
    </div>
</div>
