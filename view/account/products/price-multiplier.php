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

nonce::field( 'multiply_prices', '_multiply_prices' );
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
                <h3>Price Multiplier</h3>
            </header>

            <div class="panel-body">
                <p>Please make your spreadsheet layout match the example below. For example, include SKU, Price and Note in the top row. Also, the SKU needs to be in the far left column, Price directly to the right of SKU, and Note directly to the right of Price. And as a reminder, SKU and Price are mandatory fields, the Note is optional and this field will add a Price Note to your products.</p>

                <p>Example:</p>
                <table class="table">
                    <tr>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Note</th>
                    </tr>
                    <tr>
                        <td>A123</td>
                        <td>400</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>B456</td>
                        <td>359.99</td>
                        <td>Includes Chair</td>
                    </tr>
                    <tr class="last">
                        <td>...</td>
                        <td>...</td>
                        <td></td>
                    </tr>
                </table>

                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="text" class="form-control" id="price" value="1" />
                        </div>
                        <div class="form-group">
                            <label for="sale-price">Sale Price:</label>
                            <input type="text" class="form-control" id="sale-price" value="0" />
                        </div>
                        <div class="form-group">
                            <label for="alternate-price">MSRP:</label>
                            <input type="text" class="form-control" id="alternate-price" value="0" />
                        </div>
                    </div>
                </div>

                <p>
                    <button type="button" class="btn btn-primary" id="upload">Upload Spreadsheet &amp; Run</button>
                    <div class="progress progress-sm hidden" id="upload-loader">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <!-- Where the uploader lives -->
                    <div id="uploader"></div>
                </p>

            </div>
        </section>
    </div>
</div>
