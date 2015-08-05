<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a Product
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var bool|int $product_id
 * @var bool $show_warning
 * @var Product $product
 * @var Account $account
 * @var array $brands
 * @var array $industries
 * @var DateTime $date
 * @var Category[] $categories
 * @var array $product_attribute_items
 * @var array $tags
 * @var array $product_images
 * @var Account $accounts
 */

$statuses = array(
    'in-stock' => 'In Stock'
    , 'special-order' => 'Special Order'
    , 'out-of-stock' => 'Out of Stock'
    , 'discontinued' => 'Discontinued'
);

$visibilities = array(
    Product::PUBLISH_VISIBILITY_PUBLIC => 'Public'
    , Product::PUBLISH_VISIBILITY_PRIVATE => 'Private'
);
if ( $product->id )
    $visibilities['deleted'] = 'Deleted';

nonce::field( 'create', '_create_product' );
nonce::field( 'upload_image', '_upload_image' );
nonce::field( 'get_attribute_items', '_get_attribute_items' );

?>

<?php if($show_warning) { ?>
    <!-- Modal -->
    <div aria-hidden="false" aria-labelledby="warningModalLabel" role="dialog" tabindex="-1" id="warningModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Notification</h4>
                </div>
                <div class="modal-body">
                    <p>Exclusive/Proprietary Ashley products are not allowed to be added to your website.</p>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">I understand</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->
<?php } ?>

<form id="fAddEditProduct" role="form" method="post" <?php if ( $product->id ) echo 'action="?pid=' . $product->id . '"'; ?>>

    <input type="hidden" id="hProductId" value="<?php if ( $product->id ) echo $product->id; ?>" />
    <?php nonce::field('add_edit'); ?>

    <div class="row-fluid">

        <!-- Main Form -->
        <div class="col-lg-9">
            <section class="panel">
                <header class="panel-heading">
                    <?php echo ($product->id ? 'Edit' : 'Add') . ' ' . ($account->id ? 'Custom' : '') . ' Product' ?>
                </header>

                <div class="panel-body">

                    <div class="form-group">
                        <label for="tName">Name:</label>
                        <input type="text" class="form-control" name="tName" id="tName" placeholder="Product Name" value="<?php echo $product->name ?>"/>
                    </div>

                    <p><strong>Link:</strong> http://www.website.com/products/<input type="text" id="tProductSlug" name="tProductSlug" value="<?php echo $product->slug ?>" placeholder="Product slug" />/</p>

                    <div class="form-group">
                        <label for="taDescription">Description:</label>
                        <textarea class="form-control" name="taDescription" id="taDescription" rte="1"><?php echo $product->description?></textarea>
                    </div>

                </div>

            </section>

            <section class="panel">
                <header class="panel-heading">
                    Basic Product Info
                </header>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="sProductStatus">Status:</label>
                                <select class="form-control" name="sProductStatus" id="sProductStatus">
                                    <?php foreach ( $statuses as $value => $status ): ?>
                                        <option value="<?php echo $value; ?>"<?php if ( $value == $product->status ) echo 'selected'; ?>><?php echo $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tSKU">SKU:</label>
                                <input type="text" class="form-control" name="tSKU" id="tSKU" placeholder="SKU" maxlength="30" value="<?php echo $product->sku ?>" />
                            </div>

                            <div class="form-group">
                                <label for="tWeight">Weight:</label>
                                <input type="text" class="form-control" name="tWeight" id="tWeight" placeholder="Weight" value="<?php echo $product->weight ?>" />
                            </div>

                            <div class="form-group">
                                <label for="tPrice">Wholesale Price:</label>
                                <input type="text" class="form-control" name="tPrice" id="tPrice" placeholder="Wholesale Price" value="<?php echo $product->price ?>" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <?php if ( !$product->parent_product_id ) { ?>
                            <div class="form-group">
                                <label for="sBrand">Brand:</label>
                                <select class="form-control" name="sBrand" id="sBrand">
                                    <option value="">-- Select Brand --</option>
                                    <?php foreach ( $brands as $brand ): ?>
                                        <option value="<?php echo $brand->id; ?>"<?php if ( $product->brand_id == $brand->id ) echo 'selected'; ?>><?php echo $brand->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sIndustry">Industry:</label>
                                <select class="form-control" name="sIndustry" id="sIndustry">
                                    <option value="">-- <?php echo 'Select Industry'; ?> --</option>
                                    <?php foreach ( $industries as $industry ): ?>
                                        <option value="<?php echo $industry->id; ?>"<?php if ( $product->industry_id == $industry->id ) echo 'selected'; ?>><?php echo ucwords( $industry->name ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sCategory">Category:</label>
                                <select class="form-control" name="sCategory" id="sCategory">
                                    <option value="">-- <?php echo 'Select Category'; ?> --</option>
                                    <?php
                                        foreach ( $categories as $category ):
                                            $selected = ( $product_id && $category->id == $product->category_id ) ? ' selected="selected"' : '';
                                            $disabled = ( $category->has_children() ) ? ' disabled="disabled"' : '';
                                    ?>
                                        <option value="<?php echo $category->id; ?>"<?php echo $selected, $disabled; ?>><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php } else {
                                ?>
                                <input type="hidden" name="sBrand" id="sBrand" value="<?php echo $product->brand_id; ?>">
                                <input type="hidden" name="sIndustry" id="sIndustry" value="<?php echo $product->industry_id; ?>">
                                <input type="hidden" name="sCategory" id="sCategory" value="<?php echo $product->category_id; ?>">
                            <?php } ?>

                            <div class="form-group">
                                <label for="tPriceMin">MAP Price:</label>
                                <input type="text" class="form-control" name="tPriceMin" id="tPriceMin" placeholder="MAP Price" value="<?php echo $product->price_min ?>" />
                            </div>
                        </div>
                    </div>

                </div>

            </section>

            <section class="panel">
                <header class="panel-heading">
                    Upload Images
                </header>

                <div class="panel-body">
                    <p>You can upload up to 10 images per product. Please ensure images are at least 500px wide or tall as a minimum.</p>

                    <p>
                        <button type="button" id="aUpload" class="btn btn-primary">Upload</button>

                        <div class="progress progress-sm hidden" id="upload-loader">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </p>

                    <!-- Where the uploader lives -->
                    <div id="upload-image"></div>

                    <div id="images-list">
                        <?php
                            if ( is_array( $product_images ) )
                                foreach ( $product_images as $pi ):
                        ?>
                            <p class="image">
                                <a href="<?php echo $product->get_image_url( $pi, 'large', $industries[$product->industry_id]->name, $product_id ) ?>" title="<?php echo 'View'; ?>" target="_blank">
                                    <img src="<?php echo $product->get_image_url( $pi, 'small', $industries[$product->industry_id]->name, $product_id ) ?>" width="200" height="200" alt="" />
                                </a>
                                <a href="#" class="remove-image" title="Delete"><i class="fa fa-trash-o"></i></a>
                                <input type="hidden" name="images[]" value="<?php echo $pi; ?>" />
                            </p>
                        <?php endforeach; ?>
                    </div>

                </div>
            </section>

        </div>

        <!-- Side Pannels -->
        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Publish
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="sStatus">Visibility:</label>
                        <select class="form-control" name="sStatus" id="sStatus">
                            <?php foreach ( $visibilities as $value => $visibility ): ?>
                                <option value="<?php echo $value; ?>"<?php if ( $value == $product->publish_visibility ) echo 'selected'; ?>><?php echo $visibility; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tPublishDate">Publish Date:</label>
                        <input type="text" class="form-control" id="tPublishDate" value="<?php echo $date->getTimestamp() > 0 ? $date->format('m/d/Y') : date('m/d/Y') ?>" />
                        <input type="hidden" name="hPublishDate" id="hPublishDate" value="<?php echo $date->getTimestamp() > 0 ? $date->format('Y-m-d') : date('Y-m-d'); ?>" />
                    </div>

                    <p class="clearfix">
                        <button type="submit" class="btn btn-success pull-right">Publish</button>
                    </p>
                </div>
            </section>

            <section class="panel">
                <header class="panel-heading">
                    Product Specifications
                </header>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="tAddSpecName">Name</label>
                                <input type="text" class="form-control" id="tAddSpecName" placeholder="Ex.: Width" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="taAddSpecValue">Value</label>
                                <textarea class="form-control" id="taAddSpecValue" placeholder="Ex.: 50 in." rows="1" cols="1"></textarea>
                            </div>
                        </div>
                    </div>

                    <p class="clearfix">
                        <button type="button" id="add-product-spec" class="btn btn-sm btn-primary pull-right">Add</button>
                    </p>

                    <div id="product-specs-list">
                        <?php if ( is_array($product->specifications) )
                                foreach ( $product->specifications as $spec ):
                        ?>
                            <p class="clearfix product-spec">
                                <span class="specification-name"><?php echo $spec->key; ?></span>
                                <span class="specification-value"><?php echo $spec->value; ?></span>
                                <a href="#" class="remove-spec pull-right" title="Delete"><i class="fa fa-trash-o"></i></a>
                                <input type="hidden" name="product-specs[]" value="<?php echo str_replace( '"', '&quot;', $spec->key ) . '|' . str_replace( '"', '&quot;', $spec->value ); ?>" />
                            </p>
                        <?php endforeach; ?>
                    </div>

                </div>
            </section>

            <section class="panel">
                <header class="panel-heading">
                    Tags
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="tTag">Tag name:</label>
                        <input type="text" class="form-control" id="tTag" placeholder="Tag name, comma separated" />
                    </div>

                    <p class="clearfix">
                        <button type="button" id="add-product-tag" class="btn btn-sm btn-primary pull-right">Add</button>
                    </p>

                    <div id="product-tags-list">
                        <?php if ( is_array( $tags ) )
                            foreach ( $tags as $tag ):
                        ?>
                            <p class="clearfix product-tag">
                                <?php echo $tag ?>
                                <a href="#" class="remove-tag pull-right" title="Delete"><i class="fa fa-trash-o"></i></a>
                                <input type="hidden" name="tags[]" value="<?php echo $tag; ?>" />
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="panel">
                <header class="panel-heading">
                    Attributes
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <select class="form-control" id="sAttributes" multiple="multiple"></select>
                    </div>

                    <p class="clearfix">
                        <button type="button" id="add-attribute" class="btn btn-sm btn-primary pull-right">Add</button>
                    </p>

                    <div id="attribute-items-list" class="list">
                        <?php
                            if ( is_array( $product_attribute_items ) )
                                foreach ( $product_attribute_items as $pai ):
                        ?>
                            <p class="attribute clearfix">
                                <strong><?php echo $pai->title; ?> &ndash; </strong>
                                <?php echo $pai->name; ?>
                                <a href="#" class="remove-attribute pull-right" title="Delete"><i class="fa fa-trash-o"></i></a>
                                <input type="hidden" name="attributes[]" value="<?php echo $pai->id; ?>" />
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>

    </div>

</form>

<!-- Element Templates -->
<div class="hidden">
    <p class="clearfix product-tag" id="product-tag-template">
        <a href="#" class="remove-tag pull-right" title="Delete"><i class="fa fa-trash-o"></i></a>
        <input type="hidden" name="tags[]" value="" />
    </p>
    <p class="attribute clearfix" id="attribute-template">
        <strong> &ndash; </strong>
        <a href="#" class="remove-attribute pull-right" title="Delete"><i class="fa fa-trash-o"></i></a>
        <input type="hidden" name="attributes[]" value="" />
    </p>
    <p class="clearfix product-spec" id="product-spec-template">
        <span class="specification-name"></span>
        <span class="specification-value"></span>
        <a href="#" class="remove-spec pull-right" title="Delete"><i class="fa fa-trash-o"></i></a>
        <input type="hidden" name="product-specs[]" value="" />
    </p>
    <p class="clearfix image" id="image-template">
        <a href="" title="View" target="_blank"><img src="" width="200" height="200" alt="" /></a>
        <a href="#" class="remove-image" title="Delete"><i class="fa fa-trash-o"></i></a>
        <input type="hidden" name="images[]" value="" />
    </p>
</div>

