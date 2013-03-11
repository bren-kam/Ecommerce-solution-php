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
 * @var Product $product
 * @var Account $account
 * @var array $brands
 * @var array $industries
 * @var DateTime $date
 * @var Category[] $categories
 * @var array $product_attribute_items
 * @var array $tags
 * @var array $product_images
 * @var account $accounts
 */

$title = ( $product->id ) ? _('Edit') : _('Add');

if ( $account->id )
    $title .= ' ' . _('Custom');

$title .= ' ' . _('Product');

echo $template->start( $title );
nonce::field( 'create', '_create_product' );
nonce::field( 'upload_image', '_upload_image' );
nonce::field( 'get_attribute_items', '_get_attribute_items' );
?>
<form name="fAddEditProduct" id="fAddEditProduct" action="<?php if ( $product->id ) echo '?pid=' . $product->id; ?>" method="post" err="<?php echo _('Products require at least one image to publish'); ?>">
    <div id="right">
        <div class="box">
            <h2><?php echo _('Publish'); ?></h2>
            <div class="content">
                <select name="sStatus" id="sStatus">
                <?php
                $visibilities = array(
                    'public' => _('Public')
                    , 'private' => _('Private')
                );

                if ( $product->id )
                    $visibilities['deleted'] = _('Deleted');

                foreach ( $visibilities as $value => $visibility ) {
                    $selected = ( $visibility == $product->publish_visibility ) ? ' selected="selected"' : '';
                ?>
                    <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $visibility; ?></option>
                <?php } ?>
                </select>
                <br /><br />
                <div class="container">
                    <input type="text" class="tb" id="tPublishDate" value="<?php echo $date->format('F j, Y'); ?>" />
                    <input type="hidden" name="hPublishDate" id="hPublishDate" value="<?php echo $date->format('Y-m-d'); ?>" />
                </div>
                <br />

                <div class="divider"></div>

                <p class="text-right"><input type="submit" class="button" value="<?php echo _('Publish'); ?>" /></p>
            </div>
        </div>
        <div class="box">
            <h2><?php echo _('Product Specifications'); ?></h2>
            <div class="content">
                <table width="222" id="tProductSpecifications">
                    <tr>
                        <td width="106" class="top">
                            <input type="text" class="tb" id="tAddSpecName" maxlength="50" tmpval="<?php echo _('Name'); ?>" />
                            <br />
                            <small><?php echo _('(example: Width)'); ?></small>
                        </td>
                        <td width="10">&nbsp;</td>
                        <td width="106" class="top" id="tdValue">
                            <textarea id="taAddSpecValue" cols="1" rows="1" tmpval="<?php echo _('Value'); ?>"></textarea>
                            <br />
                            <small><?php echo _('(example: 22 in.)'); ?></small>
                        </td>
                    </tr>
                </table>
                <p><a href="#" class="button" id="add-product-spec" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
                <div id="product-specs-list">
                    <?php
                    if ( !empty( $product->product_specifications ) ) {
                        $specifications = @unserialize( $product->product_specifications );

                        if ( !$specifications )
                            $specifications = @unserialize( html_entity_decode( $product->product_specifications, ENT_QUOTES, 'UTF-8' ) );

                        if ( is_array( $specifications ) && count( $specifications ) > 0 )
                        foreach ( $specifications as $ps ) {
                            $specification_name = html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' );
                            $specification_value = html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' );
                        ?>

                        <div class="specification item">
                            <span class="specification-name"><?php echo $specification_name; ?></span>
                            <span class="specification-value"><?php echo $specification_value; ?></span>
                            <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
                            <input type="hidden" name="product-specs[]" value="<?php echo str_replace( '"', '&quot;', $specification_name ) . '|' . str_replace( '"', '&quot;', $specification_value ); ?>" />
                        </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="box">
            <h2><?php echo _('Tags'); ?></h2>
            <div class="content">
                <div class="container"><input type="text" class="tb" id="tTags" tmpval="<?php echo _('Tag Name'); ?>" /></div>
                <small><?php echo _('Separate by comma'); ?></small>
                <p><a href="#" class="button" id="add-tag" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
                <div id="tags-list" class="list">
                    <?php
                    if ( isset( $tags ) )
                    foreach ( $tags as $tag ) {
                    ?>
                        <div class="tag item">
                            <?php echo $tag; ?>
                            <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
                            <input type="hidden" name="tags[]" value="<?php echo $tag; ?>" />
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="box">
            <h2><?php echo _('Attributes'); ?></h2>
            <div class="content">
                <select id="sAttributes" multiple="multiple"></select>
                <br /><br />
                <p><a href="#" class="button" id="add-attribute" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
                <div id="attribute-items-list" class="list">
                <?php
                if ( is_array( $product_attribute_items ) )
                foreach ( $product_attribute_items as $pai ) {
                    ?>
                    <div class="attribute item">
                        <strong><?php echo $pai->title; ?> &ndash; </strong>
                        <?php echo $pai->name; ?>
                        <a href="#" class="delete-attribute-item" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
                        <input type="hidden" name="attributes[]" value="<?php echo $pai->id; ?>" />
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div id="main-container">
        <div id="main">
            <div id="name-container"><input type="text" name="tName" id="tName" class="tb" value="<?php echo $product->name; ?>" tmpval="<?php echo _('Product Name'); ?>" maxlength="200" /></div>
            <div id="dProductSlug">
                <span><strong><?php echo _('Link:'); ?></strong> <?php echo _('http://www.website.com/'); ?><span><?php echo _('products'); ?></span>/ <input type="text" name="tProductSlug" id="tProductSlug" maxlength="50" class="tb" value="<?php echo $product->slug; ?>" /> /
            </div>

            <textarea name="taDescription" id="taDescription" rows="12" cols="50" rte="1"><?php echo $product->description; ?></textarea>
            <br />

            <h3><?php echo _('Basic Product Info'); ?></h3>
            <br />

            <table id="basic-product-info">
                <tr>
                    <td width="50%">
                        <select name="sProductStatus" id="sProductStatus">
                            <?php
                            $statuses = array(
                                'in-stock' => _('In Stock')
                                , 'special-order' => _('Special Order')
                                , 'out-of-stock' => _('Out of Stock')
                                , 'discontinued' => _('Discontinued')
                            );

                            foreach ( $statuses as $value => $status ) {
                                $selected = ( $product->id && $value == $product->status ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $status; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td width="50%">
                        <select name="sBrand" id="sBrand">
                            <option value="">-- <?php echo _('Select Brand'); ?> --</option>
                            <?php
                                if ( is_array( $brands ) )
                                foreach ( $brands as $brand ) {
                                    $selected = ( $product->id && $product->brand_id == $brand->id ) ? ' selected="selected"' : '';
                                ?>
                                <option value="<?php echo $brand->id; ?>"<?php echo $selected; ?>><?php echo $brand->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><div class="container"><input type="text" class="tb" name="tSKU" id="tSKU" maxlength="20" value="<?php echo $product->sku; ?>" tmpval="<?php echo _('SKU'); ?>" /></div></td>
                    <td>
                        <select name="sIndustry" id="sIndustry">
                            <option value="">-- <?php echo _('Select Industry'); ?> --</option>
                            <?php
                                if ( is_array( $industries ) )
                                foreach ( $industries as $industry ) {
                                    $selected = ( $product_id && $product->industry_id == $industry->id ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $industry->id; ?>"<?php echo $selected; ?>><?php echo ucwords( $industry->name ); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><div class="container"><input type="text" class="tb" name="tWeight" id="tWeight" value="<?php echo $product->weight; ?>" tmpval="<?php echo _('Weight'); ?>" /></div></td>
                    <td>
                        <select name="sCategory" id="sCategory">
                            <option value="">-- <?php echo _('Select Category'); ?> --</option>
                            <?php
                            foreach ( $categories as $category ) {
                                $selected = ( $product_id && $category->id == $product->category_id ) ? ' selected="selected"' : '';
                                $disabled = ( $category->has_children() ) ? ' disabled="disabled"' : '';
                                ?>
                                <option value="<?php echo $category->id; ?>"<?php echo $selected, $disabled; ?>><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
            <br />

            <?php if ( $product->id ) { ?>
            <table class="col-2">
                <tr>
                    <td><strong><?php echo _('Created by:'); ?></strong></td>
                    <td><?php echo $product->created_user; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo _('Updated by:'); ?></strong></td>
                    <td><?php echo $product->updated_user; ?></td>
                </tr>
                <?php if ( $account->id ) { ?>
                <tr>
                    <td><strong><?php echo _('Website:'); ?></strong></td>
                    <td><?php echo $account->title; ?></td>
                </tr>
                <?php } ?>
            </table>
            <br />
            <?php } ?>

            <h2><?php echo _('Upload Images'); ?></h2>
            <p id="pUploadImagesMessage"><?php echo _('You can upload up to 10 images per product. Please ensure images are at least 500px wide or tall as a minimum.'); ?></p>
            <a href="#" id="aUpload" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
            <div class="hidden" id="upload-image"></div>
            <br /><br />
            <div id="images-list">
                <?php
                if ( $product_id && is_array( $product_images ) )
                foreach ( $product_images as $pi ) {
                    ?>
                    <div class="image">
                        <a href="http://<?php echo str_replace( ' ', '', $industries[$product->industry_id]->name ); ?>.retailcatalog.us/products/<?php echo $product_id; ?>/large/<?php echo $pi; ?>" title="<?php echo _('View'); ?>" target="_blank"><img src="http://<?php echo str_replace( ' ', '', $industries[$product->industry_id]->name ); ?>.retailcatalog.us/products/<?php echo $product_id; ?>/small/<?php echo $pi; ?>" width="200" height="200" alt="" /></a>
                        <p><a href="#" class="delete" title="<?php echo _('Delete'); ?>" confirm="<?php echo _('Are you sure you want to delete this image? It cannot be undone'); ?>"><?php echo _('Delete'); ?></a></p>
                        <input type="hidden" name="images[]" value="<?php echo $pi; ?>" />
                    </div>
                <?php } ?>
            </div>

            <?php if ( !empty( $accounts ) ) { ?>
                <br clear="left" /><br />
                <h2><?php echo _('Accounts With Product'); ?></h2>
                <ul>
                    <?php foreach( $accounts as $account ) { ?>
                        <li><?php echo $account->title; ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>
    <input type="hidden" id="hProductId" value="<?php if ( $product_id ) echo $product_id; ?>" />
    <?php nonce::field('add_edit'); ?>
</form>
<br clear="all" />
<br />

<div class="hidden">
    <div class="tag item" id="tag-template">
        <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
        <input type="hidden" name="tags[]" value="" />
    </div>
    <div class="attribute item" id="attribute-item-template">
        <strong> &ndash; </strong>
        <a href="#" class="delete-attribute-item" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
        <input type="hidden" name="attributes[]" value="" />
    </div>
    <div class="specification item" id="product-spec-template">
        <span class="specification-name"></span>
        <span class="specification-value"></span>
        <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
        <input type="hidden" name="product-specs[]" value="" />
    </div>
    <div class="image" id="image-template">
        <a href="" title="<?php echo _('View'); ?>" target="_blank"><img src="" width="200" height="200" alt="" /></a>
        <p><a href="#" class="delete" title="<?php echo _('Delete'); ?>" confirm="<?php echo _('Are you sure you want to delete this image? It cannot be undone'); ?>"><?php echo _('Delete'); ?></a></p>
        <input type="hidden" name="images[]" value="" />
    </div>
</div>

<?php echo $template->end(); ?>