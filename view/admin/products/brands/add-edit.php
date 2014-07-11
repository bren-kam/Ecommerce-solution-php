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

                <form name="fAddEditBrand" id="fAddEditBrand" method="post" <?php if ( $brand->id ) echo 'action="?aid='. $brand->id .'"' ?>>

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


                    <p>
                        <button type="submit" class="btn btn-lg btn-primary">Save</button>
                    </p>

                </form>

                <?php echo $validation ?>

            </div>
        </section>
    </div>
</div>

<div class="input-group attribute-item hidden" id="attribute-item-template">
    <input type="text" class="form-control" name="list-items[]" value="<?php echo $brand_item->name; ?>" />
    <span class="input-group-btn">
        <button type="button" id="add-item" class="btn btn-danger delete-attribute-item"><i class="fa fa-trash-o"></i></button>
    </span>
</div>
