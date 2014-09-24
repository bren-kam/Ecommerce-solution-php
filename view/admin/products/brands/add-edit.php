<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Brand
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Brand $brand
 * @var array $product_options_array
 * @var array $product_option_ids
 * @var bool|string $errs
 */

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $brand->id ? 'Edit' : 'Add' ?> Brand
            </header>

            <div class="panel-body">

                <form name="fAddEditBrand" id="fAddEditBrand" method="post" <?php if ( $brand->id ) echo 'action="?bid='. $brand->id .'"' ?> enctype="multipart/form-data">

                    <?php nonce::field( 'add_edit' ); ?>

                    <div class="form-group">
                        <label for="tName">Name</label>
                        <input type="text" class="form-control" name="tName" id="tName" value="<?php echo ( isset( $_POST['tName'] ) || !$brand->id ) ? $template->v('tName') : $brand->name; ?>" placeholder="Name" />
                    </div>

                    <div class="form-group">
                        <label for="tSlug">Slug</label>
                        <input type="text" class="form-control" name="tSlug" id="tSlug" value="<?php echo ( isset( $_POST['tSlug'] ) || !$brand->slug ) ? $template->v('tSlug') : $brand->slug; ?>" placeholder="Slug" />
                    </div>

                    <div class="form-group">
                        <label for="tWebsite">Website</label>
                        <input type="text" class="form-control" name="tWebsite" id="tWebsite" value="<?php echo ( isset( $_POST['tWebsite'] ) || !$brand->website ) ? $template->v('tWebsite') : $brand->website; ?>" placeholder="Website" />
                    </div>

                    <div class="form-group" id="product-option-list">
                        <label for="sProductOptions">Product Options</label>

                        <select class="form-control" name="sProductOptions" id="sProductOptions">
                            <option value="">Select Product Option</option>
                            <?php
                            $product_options = array();
                            foreach ( $product_options_array as $po ):
                                $product_options[$po->id] = $po;
                                $disabled = ( in_array( $po->id, $product_option_ids ) ) ? ' disabled="disabled"' : '';
                            ?>
                                <option value="<?php echo $po->id; ?>"<?php echo $disabled; ?>><?php echo $po->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php foreach ( $product_option_ids as $product_option_id ): ?>
                            <p class="product-option-item clearfix">
                                <span><?php echo $product_options[$product_option_id]->name; ?></span>
                                <a href="javascript:;" class="delete-product-option" title="Delete"><i class="fa fa-trash-o"></i></a>
                                <input type="hidden" name="product-options[]" value="<?php echo $product_option_id; ?>" />
                            <p>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label for="fImage">Image</label>

                        <input type="file" class="form-control" id="fImage" name="fImage" />

                        <?php if ( !empty( $brand->image ) ): ?>
                            <img src="<?php echo $brand->image ?>" />
                        <?php endif; ?>
                    </div>

                    <p>
                        <button type="submit" class="btn btn-lg btn-primary">Save</button>
                    </p>

                </form>

                <?php echo $validation ?>

            </div>
        </section>
    </div>
</div>

<p class="clearfix hidden" id="product-option-template">
    <span></span>
    <a href="javascript:;" class="delete-product-option" title="Delete"><i class="fa fa-trash-o"></i></a>
    <input type="hidden" name="product-options[]" value="" />
<p>
