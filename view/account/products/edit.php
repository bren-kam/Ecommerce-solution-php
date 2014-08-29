<?php
/**
 * @var AccountProduct $product
 * @var array $product_options
 * @var WebsiteCoupon[] $coupons
 */
?>

<form id="edit-product" action="/products/update-product/" method="post" role="form">

    <?php nonce::field( 'update_product' ) ?>
    <input type="hidden" name="hProductID" value="<?php echo $product->product_id ?>" />

    <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel"><?php echo $product->name ?></h4>
            </div>
            <div class="modal-body">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#product" role="tab" data-toggle="tab">Produc &amp; Pricing</a></li>
                    <li><a href="#options" role="tab" data-toggle="tab">Product Options</a></li>
                    <li><a href="#shopping-cart" role="tab" data-toggle="tab">Shopping Cart</a></li>
                    <li><a target="_blank" href="http://<?php echo str_replace( 'account', 'admin', SUBDOMAIN ), '.', DOMAIN; ?>/products/add-edit/?pid=<?php echo $product->product_id ?>">Master Catalog</a></li>
                </ul>

                <br />

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="product">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="tPrice">Price:</label>
                                    <input type="text" class="form-control" id="tPrice" name="tPrice" value="<?php echo $product->price ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tPriceNote">Price Note:</label>
                                    <input type="text" class="form-control" id="tPriceNote" name="tPriceNote" value="<?php echo $product->price_note ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tAlternatePrice">MSRP:</label>
                                    <input type="text" class="form-control" id="tAlternatePrice" name="tAlternatePrice" value="<?php echo $product->alternate_price ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tAlternatePriceName">MSRP Name:</label>
                                    <input type="text" class="form-control" id="tAlternatePriceName" name="tAlternatePriceName" value="<?php echo $product->alternate_price_name ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tSalePrice">Sale Price:</label>
                                    <input type="text" class="form-control" id="tSalePrice" name="tSalePrice" value="<?php echo $product->sale_price ?>" />
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="cbOnSale" name="cbOnSale" value="true" <?php if ( $product->on_sale ) echo 'checked'; ?> />
                                        On Sale?
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="tSetupFee">Setup Fee:</label>
                                    <input type="text" class="form-control" id="tSetupFee" name="tSetupFee" value="<?php echo $product->setup_fee ?>" />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="taProductNote">Product Note:</label>
                                    <textarea class="form-control" rows="2" id="taProductNote" name="taProductNote"><?php echo $product->product_note ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="tWarrantyLength">Warranty Length:</label>
                                    <input type="text" class="form-control" id="tWarrantyLength" name="tWarrantyLength" value="<?php echo $product->warranty_length ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tInventory">Inventory:</label>
                                    <input type="text" class="form-control" id="tInventory" name="tInventory" value="<?php echo $product->inventory ?>" />
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="cbDisplayInventory" name="cbDisplayInventory" value="1" <?php if ( $product->display_inventory ) echo 'checked'; ?> />
                                        Display Inventory?
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="sStatus">Status:</label>
                                    <select class="form-control" id="sStatus" name="sStatus">
                                        <?php foreach ( array( 1 => 'In Stock', 0 => 'Out of Stock', 2 => 'On Display', 3 => 'Special Order' ) as $k => $v ): ?>
                                            <option value="<?php echo $k?>" <?php if ( $product->status == $k ) echo 'selected' ?> ><?php echo $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="tMetaTitle">Meta Title:</label>
                                    <input type="text" class="form-control" id="tMetaTitle" name="tMetaTitle" value="<?php echo $product->meta_title ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tMetaDescription">Meta Description:</label>
                                    <input type="text" class="form-control" id="tMetaDescription" name="tMetaDescription" value="<?php echo $product->meta_description ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="tMetaKeywords">Meta Keywords:</label>
                                    <input type="text" class="form-control" id="tMetaKeywords" name="tMetaKeywords" value="<?php echo $product->meta_keywords ?>" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="options">

                        <div class="row">
                            <div class="col-lg-6">
                                <select id="sProductOptions" class="form-control">
                                    <option></option>
                                    <?php foreach ( $product_options as $product_option_id => $product_option ): ?>
                                        <option value="<?php echo $product_option_id ?>" <?php if( isset($product->product_options[$product_option_id]) ) echo 'disabled'?>><?php echo $product_option['option_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <button type="button" class="btn btn-sm btn-default" id="add-product-option">Add</button>
                            </div>
                        </div>

                        <br />

                        <div id="product-option-list">

                        </div>

                    </div>
                    <div class="tab-pane" id="shopping-cart">
                        <div class="form-group">
                            <label for="tStoreSKU">Store SKU:</label>
                            <input type="text" class="form-control" id="tStoreSKU" name="tStoreSKU" value="<?php echo $product->store_sku ?>" />
                        </div>
                        <div class="form-group">
                            <label for="tShipsIn">Ships In:</label>
                            <input type="text" class="form-control" id="tShipsIn" name="tShipsIn" value="<?php echo $product->ships_in ?>" />
                        </div>
                        <div class="form-group">
                            <label for="tShipping">Additional Shipping:</label>
                            <input type="text" class="form-control" id="tPrice" name="tAdditionalShipping" value="<?php echo $product->additional_shipping_amount ?>" />
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="rShippingMethod" value="Flat Rate" <?php if ( $product->additional_shipping_type == 'Flat Rate' ) echo 'checked' ?>/> Additional Shipping as Flat Rate ($)
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="rShippingMethod" value="Percentage" <?php if ( $product->additional_shipping_type == 'Percentage' ) echo 'checked' ?>/> Additional Shipping as Percentage (%)
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="tWholesalePrice">Wholesale Price:</label>
                            <input type="text" class="form-control" id="tWholesalePrice" name="tWholesalePrice" value="<?php echo $product->wholesale_price ?>" />
                        </div>
                        <div class="form-group">
                            <label for="tWeight">Weight:</label>
                            <input type="text" class="form-control" id="tWeight" name="tWeight" value="<?php echo $product->weight ?>" />
                        </div>
                        <p>Coupons:</p>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <select class="form-control" id="sCoupons">
                                        <option></option>
                                        <?php foreach( $coupons as $coupon ): ?>
                                            <option value="<?php echo $coupon->website_coupon_id ?>" <?php if ( isset( $product->coupons[$coupon->website_coupon_id] ) ) echo 'disabled' ?>><?php echo $coupon->name?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default" id="add-coupon">Add</button>
                            </div>
                            <div class="col-lg-6">
                                <ul id="coupon-list">
                                    <?php foreach ( $product->coupons as $product_coupon_id => $product_coupon ): ?>
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

<!-- Product Option specific forms -->
<div class="hidden" id="product-option-templates">
    <?php foreach ( $product_options as $product_option_id => $product_option ): ?>
        <div class="row" data-product-option-id="<?php echo $product_option_id ?>">
            <input type="hidden" name="product_options[<?php echo $product_option_id ?>]" value="<?php echo $product_option_id ?>" />

            <div class="col-lg-4">
                <strong><?php echo $product_option['option_name'] ?></strong>
                <a href="javascript:;" class="remove-product-option"><i class="fa fa-trash-o"></i></a>
            </div>
            <div class="col-lg-8">
                <?php if ( $product_option['option_type'] == 'select' ): ?>
                    <?php foreach ( $product_option['list_items'] as $product_option_list_item_id => $item ): ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="product_list_items[<?php echo $product_option_id ?>][<?php echo $product_option_list_item_id ?>]" value="true" <?php if ( isset( $product->product_options[$product_option_id]['list_items'][$product_option_list_item_id] ) ) echo 'checked' ?> />
                                        <?php echo $item ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="tPrices[<?php echo $product_option_id ?>][<?php echo $product_option_list_item_id ?>]" value="<?php echo $product->product_options[$product_option_id]['list_items'][$product_option_list_item_id] ?>" placeholder="Price (optional)"/>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="cbRequired<?php echo $product_option_id ?>" value="true">
                            Required?
                        </label>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="tPrice<?php echo $product_option_id ?>" value="<?php echo $product->product_options[$product_option_id]['price'] ?>" placeholder="Price (Optional)"/>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<ul class="hidden">
    <li id="coupon-template">
        <input type="hidden" name="hCoupons[]" />
        <a href="javascript:;" class="remove-coupon"><i class="fa fa-trash-o"></i></a>
    </li>
</ul>