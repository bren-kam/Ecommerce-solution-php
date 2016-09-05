<?php
/**
 * @var Product $product
 * @var AccountProduct $product
 * @var array $product_options
 * @var WebsiteCoupon[] $coupons
 * @var Product[] $child_products
 */
?>

<form id="edit-product" action="/products/update-product/" method="post" role="form">

    <?php nonce::field( 'update_product' ) ?>
    <input type="hidden" name="hProductID" value="<?php echo $account_product->product_id ?>" />

    <!-- Modal -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel"><?php echo $account_product->name ?></h4>
            </div>
            <div class="modal-body">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="<?php if (!$_GET['tab'] || $_GET['tab'] == 'product' ) echo 'active' ?>"><a href="#product" role="tab" data-toggle="tab">Product &amp; Pricing</a></li>
                    <li class="<?php if ($_GET['tab'] == 'options' ) echo 'active' ?>"><a href="#options" role="tab" data-toggle="tab">Product Options</a></li>
                    <li class="<?php if ($_GET['tab'] == 'shopping-cart' ) echo 'active' ?>"><a href="#shopping-cart" role="tab" data-toggle="tab">Shopping Cart</a></li>
                    <li><a target="_blank" href="http://<?php echo str_replace( 'account', 'admin', SUBDOMAIN ), '.', DOMAIN; ?>/products/add-edit/?pid=<?php echo $account_product->product_id ?>">Master Catalog</a></li>
                </ul>

                <br />

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane <?php if (!$_GET['tab'] || $_GET['tab'] == 'product' ) echo 'active' ?>" id="product">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="tPrice">Price:</label>
                                    <input type="text" class="form-control" id="tPrice" name="tPrice" value="<?php echo $account_product->price ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tPriceNote">Price Note:</label>
                                    <input type="text" class="form-control" id="tPriceNote" name="tPriceNote" value="<?php echo $account_product->price_note ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tAlternatePrice">MSRP:</label>
                                    <input type="text" class="form-control" id="tAlternatePrice" name="tAlternatePrice" value="<?php echo $account_product->alternate_price ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tAlternatePriceName">MSRP Name:</label>
                                    <input type="text" class="form-control" id="tAlternatePriceName" name="tAlternatePriceName" value="<?php echo $account_product->alternate_price_name ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tSalePrice">Sale Price:</label>
                                    <input type="text" class="form-control" id="tSalePrice" name="tSalePrice" value="<?php echo $account_product->sale_price ?>" />
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="cbOnSale" name="cbOnSale" value="true" <?php if ( $account_product->on_sale ) echo 'checked'; ?> />
                                        On Sale?
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="tSetupFee">Setup Fee:</label>
                                    <input type="text" class="form-control" id="tSetupFee" name="tSetupFee" value="<?php echo $account_product->setup_fee ?>" />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="taProductNote">Product Note:</label>
                                    <textarea class="form-control" rows="2" id="taProductNote" name="taProductNote"><?php echo $account_product->product_note ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="tWarrantyLength">Warranty Length:</label>
                                    <input type="text" class="form-control" id="tWarrantyLength" name="tWarrantyLength" value="<?php echo $account_product->warranty_length ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tInventory">Inventory:</label>
                                    <input type="text" class="form-control" id="tInventory" name="tInventory" value="<?php echo $account_product->inventory ?>" />
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="cbDisplayInventory" name="cbDisplayInventory" value="1" <?php if ( $account_product->display_inventory ) echo 'checked'; ?> />
                                        Display Inventory?
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="cbInventoryTracking" name="cbInventoryTracking" value="1" <?php if ( $account_product->inventory_tracking ) echo 'checked'; ?> />
                                        Inventory Tracking?
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="sStatus">Status:</label>
                                    <select class="form-control" id="sStatus" name="sStatus">
                                        <?php foreach ( array( 1 => 'In Stock', 0 => 'Out of Stock', 2 => 'On Display', 3 => 'Special Order' ) as $k => $v ): ?>
                                            <option value="<?php echo $k?>" <?php if ( $account_product->status == $k ) echo 'selected' ?> ><?php echo $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="tMetaTitle">Meta Title:</label>
                                    <input type="text" class="form-control" id="tMetaTitle" name="tMetaTitle" value="<?php echo $account_product->meta_title ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tMetaDescription">Meta Description:</label>
                                    <input type="text" class="form-control" id="tMetaDescription" name="tMetaDescription" value="<?php echo $account_product->meta_description ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tMetaKeywords">Meta Keywords:</label>
                                    <input type="text" class="form-control" id="tMetaKeywords" name="tMetaKeywords" value="<?php echo $account_product->meta_keywords ?>" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane <?php if ($_GET['tab'] == 'options' ) echo 'active' ?>" id="options">
                        <?php if ( $account_product->product_options() ): ?>
                            <h3>Product Options</h3>
                            <?php foreach( $account_product->product_options() as $product_option )
                            { ?>
                                <h4><?php echo $product_option->name; ?></h4>
                                <?php if ($product_option->items()): ?>
                                <ul>
                                    <?php foreach ($product_option->items() as $item)
                                    { ?>
                                        <li><?php echo $item->name; ?></li>
                                    <?php } ?>
                                </ul>
                                <?php endif; ?>
                            <?php } ?>
                            <a href="/products/product-options/add-edit/?pid=<?php echo $account_product->product_id ?>" class="btn btn-primary">Edit product options</a>
                        <?php endif; ?>
                        <?php if ( $child_products ): ?>
                            <h3>Product Option Mutations</h3>

                            <table class="table">
                                <tbody>
                                    <?php foreach ( $child_products as $child_product ): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" data-product-id="<?php echo $child_product->product_id ?>" class="toggle-child-product" <?php if ( $child_product->publish_visibility == 'public' ) echo 'checked' ?>/>
                                            </td>
                                            <td>
                                                <?php echo $child_product->sku ?> <br>
                                                <a href="/products/product-builder/add-edit/?pid=<?php echo $child_product->product_id ?>">Edit</a>
                                            </td>
                                            <td><?php echo $child_product->name ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p>
                                <a href="/products/product-options/pricing-tool/?pid=<?php echo $account_product->product_id ?>" class="btn btn-primary">Product Options Pricing Tool</a>
                            </p>
                        <?php else: ?>
                            <p>
                                <a href="/products/product-options/add-edit/?pid=<?php echo $account_product->product_id ?>" class="btn btn-primary">Create product options</a>
                            </p>
                        <?php endif; ?>


                    </div>
                    <div class="tab-pane <?php if ($_GET['tab'] == 'shopping-cart' ) echo 'active' ?>" id="shopping-cart">
                        <div class="form-group">
                            <label for="tStoreSKU">Store SKU:</label>
                            <input type="text" class="form-control" id="tStoreSKU" name="tStoreSKU" value="<?php echo $account_product->store_sku ?>" />
                        </div>
                        <div class="form-group">
                            <label for="tShipsIn">Amazon FBA</label>
                            <select class="form-control" id="sAmazonFBA"  name="sAmazonFBA">
                                <option value="1" <?php echo $product->is_amazon_eligible() ? 'selected="selected"' : '' ?>>Yes</option>
                                <option value="0" <?php echo !$product->is_amazon_eligible() ? 'selected="selected"' : '' ?>>No</option>
                            </select>                            
                        </div>
                        <div class="form-group">
                            <label for="tShipsIn">Ships In:</label>
                            <input type="text" class="form-control" id="tShipsIn" name="tShipsIn" value="<?php echo $account_product->ships_in ?>" />
                        </div>
                        <div class="form-group">
                            <label for="tShipping">Additional Shipping:</label>
                            <input type="text" class="form-control" id="tPrice" name="tAdditionalShipping" value="<?php echo $account_product->additional_shipping_amount ?>" />
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="rShippingMethod" value="Flat Rate" <?php if ( $account_product->additional_shipping_type == 'Flat Rate' ) echo 'checked' ?>/> Additional Shipping as Flat Rate ($)
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="rShippingMethod" value="Percentage" <?php if ( $account_product->additional_shipping_type == 'Percentage' ) echo 'checked' ?>/> Additional Shipping as Percentage (%)
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="tWholesalePrice">Wholesale Price:</label>
                            <input type="text" class="form-control" id="tWholesalePrice" name="tWholesalePrice" value="<?php echo $account_product->wholesale_price ?>" />
                        </div>
                        <div class="form-group">
                            <label for="tWeight">Weight:</label>
                            <input type="text" class="form-control" id="tWeight" name="tWeight" value="<?php echo $account_product->weight ?>" />
                        </div>
                        <p>Coupons:</p>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <select class="form-control" id="sCoupons">
                                        <option></option>
                                        <?php foreach( $coupons as $coupon ): ?>
                                            <option value="<?php echo $coupon->website_coupon_id ?>" <?php if ( isset( $account_product->coupons[$coupon->website_coupon_id] ) ) echo 'disabled' ?>><?php echo $coupon->name?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default" id="add-coupon">Add</button>
                            </div>
                            <div class="col-lg-6">
                                <ul id="coupon-list">
                                    <?php foreach ( $account_product->coupons as $product_coupon_id => $product_coupon ): ?>
                                        <li>
                                            <?php echo $product_coupon ?>
                                            <input type="hidden" name="hCoupons[]" value="<?php echo $product_coupon_id ?>" />
                                            <a href="javascript:;" class="remove-coupon"><i class="fa fa-trash-o"></i></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

</form>

<ul class="hidden">
    <li id="coupon-template">
        <input type="hidden" name="hCoupons[]" />
        <a href="javascript:;" class="remove-coupon"><i class="fa fa-trash-o"></i></a>
    </li>
</ul>
