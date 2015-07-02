<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Product Option
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Product $product
 * @var AccountProduct $account_product
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Create Product Options For: <?php echo $product->name ?>
            </header>

            <div class="panel-body">

                <form id="fAddEditDropDownList" method="post" role="form">

                    <div id="product-option-group-container">
                        <?php
                        if ( $account_product->product_options() ) {
                            foreach ( $account_product->product_options() as $product_option ) {
                                ?>
                                <div class="product-option-group" data-group-id="<?php echo $product_option->id; ?>">
                                    <h4>Product Option <a href="javascript:;" class="delete-product-option-group"><i class="fa fa-trash-o"></i></a></h4>

                                    <div class="form-group">
                                        <label>Option Name:*</label>
                                        <input type="text" class="form-control" maxlength="200" name="option-name[n<?php echo $product_option->id; ?>]" value="<?php echo $product_option->name; ?>" />
                                    </div>

                                    <div class="form-group product-option-item-list">
                                        <div class="input-group">
                                            <label for="tItem">Items:</label>
                                            <input type="text" class="form-control add-item-text" placeholder="Add new item" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-success add-item"><i class="fa fa-plus"></i></button>
                                            </span>
                                        </div>
                                        <?php
                                        if( $product_option->items() ) {
                                            foreach ( $product_option->items() as $item ) {
                                            ?>
                                            <div class="input-group product-option-item">
                                                <input type="text" class="form-control" name="list-items[<?php echo $product_option->id; ?>][<?php echo $item->id; ?>]" value="<?php echo $item->name; ?>" />
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-danger delete-product-option-item"><i class="fa fa-trash-o"></i></button>
                                                </span>
                                            </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <!-- Unique to previous product options to let them know to update it -->
                                    <input type="hidden" class="action" name="action[<?php echo $product_option->id; ?>]" value="update">
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <input type="hidden" name="hType" value="drop-down-list" />
                    <?php nonce::field('add_edit'); ?>

                    <p>
                        <button type="button" id="add-product-option-group" class="btn btn-primary">Add Product Option</button>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>

<div class="input-group product-option-item hidden" id="product-option-item-template">
    <input type="text" class="form-control" name="list-items[]" id="product-option-name" />
    <span class="input-group-btn">
        <button type="button" class="btn btn-danger delete-product-option-item"><i class="fa fa-trash-o"></i></button>
    </span>
</div>

<div class="product-option-group hidden" id="product-option-group-template">
    <h4>Product Option <a href="javascript:;" class="delete-product-option-group"><i class="fa fa-trash-o"></i></a></h4>

    <div class="form-group">
        <label>Option Name:*</label>
        <input type="text" class="form-control" maxlength="200"name="option-name[]" />
    </div>

    <div class="form-group product-option-item-list">
        <div class="input-group">
            <label for="tItem">Items:</label>
            <input type="text" class="form-control add-item-text" placeholder="Add new item" />
            <span class="input-group-btn">
                <button type="button" class="btn btn-success add-item"><i class="fa fa-plus"></i></button>
            </span>
        </div>
    </div>
</div>
