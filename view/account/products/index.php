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

nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
nonce::field( 'remove', '_remove' );
nonce::field( 'edit', '_edit' );
nonce::field( 'block', '_block' );
nonce::field( 'set_category_image', '_set_category_image' );
nonce::field( 'update_sequence', '_update_sequence' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                My Products
                <span class="pull-right">Products Usage: <?php echo number_format($product_count, 0) . " / " . number_format($user->account->products, 0) ?></span>
            </header>

            <div class="panel-body">

                <form action="/products/search/" class="form-inline" id="product-search" role="form">
                    <fieldset>
                        <div class="form-group">
                            <select class="form-control" name="cid">
                                <option value="">-- Select Category --</option>
                                <?php foreach ( $categories as $category ): ?>
                                    <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <?php if ( !empty( $pricing_points ) ): ?>
                                <select class="form-control" name="pr" >
                                    <option value="">-- Select Price Range --</option>
                                    <option value="0|0">Unpriced</option>
                                    <option value="0|<?php echo $pricing_points[0]; ?>"><?php echo '$' . number_format( $pricing_points[0] ) . ' ' . _('or less'); ?></option>
                                    <option value="<?php echo $pricing_points[0] . '|' . $pricing_points[1]; ?>"><?php echo '$' . number_format( $pricing_points[0] ) . ' - $' . number_format( $pricing_points[1] ); ?></option>
                                    <option value="<?php echo $pricing_points[1] . '|' . $pricing_points[2]; ?>"><?php echo '$' . number_format( $pricing_points[1] ) . ' - $' . number_format( $pricing_points[2] ); ?></option>
                                    <option value="<?php echo $pricing_points[2]; ?>|"><?php echo '$' . number_format( $pricing_points[2] ) . ' ' . _('or more'); ?></option>
                                </select>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <select class="form-control" id="sAutoComplete" name="s">
                                <option value="sku">SKU</option>
                                <option value="product">Product Name</option>
                                <option value="brand">Brand</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="tAutoComplete" placeholder="Search..." name="v" />
                        </div>
                        <input type="hidden" name="p" value="1" />
                        <input type="hidden" name="n" value="20" />
                        <button type="submit" class="btn btn-primary" id="sSearch">Search</button>
                    </fieldset>
                    <fieldset>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="od" value="1" /> Search Only Discontinued Products
                            </label>
                            <br />
                            (<a href="<?php echo url::add_query_arg( '_nonce', nonce::create('remove_all_discontinued_products'), '/products/remove-all-discontinued-products/' ); ?>" ajax="1" confirm="Are you sure you want to remove all discontinued products? This cannot be undone." title="Remove All Discontinued Products">Remove All Discontinued Products</a>)
                        </div>
                    </fieldset>
                </form>

            </div>
        </section>
    </div>
</div>


<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Search Results
            </header>

            <div class="panel-body">
                <p>
                    <select class="form-control" id="pp">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="0">All</option>
                    </select>
                    products per page
                </p>

                <div id="product-list">
                    <div id="product-template" class="product hidden">
                        <h3></h3>
                        <img src="" />
                        <ul>
                            <li>SKU: <span class="sku"></span></li>
                            <li>Brand: <span class="brand"></span></li>
                            <li>Price: $<span class="price"></span></li>
                            <li><span class="alt-price-name"></span>: $<span class="alt-price"></span></li>
                        </ul>
                        <p>
                            <a href="javascript:;" target="_blank" class="view-product">View</a>
                            | <a href="javascript:;" class="remove">Remove</a>
                            | <a href="javascript:;" class="edit" data-modal>Edit</a>
                            | <a href="javascript:;" class="block" ajax="1">Block</a>
                            | <a href="javascript:;" class="set-category-image" ajax="1">Set as Category Picture</a>
                            | <a href="javascript:;" class="add-product-options">Add Product Options</a>
                        </p>
                    </div>
                </div>

                <div class="clearfix"></div>

                <p class="pull-right">
                    Products <span id="product-start"></span> - <span id="product-end"></span> of <span id="product-count"></span>
                    <a href="javascript:;" id="prev-page" class="btn btn-xs btn-default">&lt;</a>
                    <a href="javascript:;" id="next-page" class="btn btn-xs btn-default">&gt;</a>
                </p>
            </div>
        </section>
    </div>
</div>
