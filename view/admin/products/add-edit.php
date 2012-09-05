<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a Product
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Product $product
 * @var array $brands
 * @var array $industries
 * @var DateTime $date
 * @var array $categories
 * @var array $attribute_items
 * @var array $product_attribute_items
 * @var array $tags
 */

$title = ( $product->id ) ? _('Edit') : _('Add');
$title .= ' ' . _('Product');

echo $template->start( $title );
?>

<form name="fAddEditProduct" id="fAddEditProduct" action="" method="post">
    <div id="right">
        <div class="box">
            <h2><?php echo _('Publish'); ?></h2>
            <div class="content">
                <select name="sStatus">
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

                        $new_slugs = 0;

                        if ( is_array( $specifications ) && count( $specifications ) > 0 )
                        foreach ( $specifications as $ps ) {
                            $ps_slug = str_replace( ' ', '-', strtolower( $ps[0] ) );

                            if ( empty( $ps_slug ) ) {
                                $ps_slug = $new_slugs;
                                $new_slugs++;
                            }
                        ?>

                        <div class="specification item">
                            <div class="specification-name"><?php echo html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ); ?></div>
                            <div class="specification-value"><?php echo html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ); ?></div>
                            <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
                            <input type="hidden" name="product-specs[]" value="" />
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
                <?php
                $disabled_attributes = array();
                $attributes_html = '';

                if ( is_array( $product_attribute_items ) )
                foreach ( $product_attribute_items as $pai ) {
                    $disabled_attributes[] = $pai->id;

                    $attributes_html .= '<div class="attribute item">';
                    $attributes_html .= '<strong>' . $pai->title . ' &ndash; </strong>';
                    $attributes_html .= $pai->name;
                    $attributes_html .= '<a href="#" class="delete" title="' . _('Delete') . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete') . '" /></a>';
                    $attributes_html .= '<input type="hidden" name="attributes[]" value="' . $pai->id . '" />';
                    $attributes_html .= '</div>';
                }
                ?>
                <select id="sAttributes" multiple="multiple">
                    <?php
                        $attributes = array_keys( $attribute_items );

                        foreach ( $attributes as $attribute ) {
                            echo '<optgroup label="', $attribute, '">';

                            if ( is_array( $attribute_items[$attribute] ) )
                            foreach ( $attribute_items[$attribute] as $attribute_item ) {
                                $disabled = ( in_array( $attribute_item->id, $disabled_attributes ) ) ? ' disabled="disabled"' : '';
                                echo '<option value="', $attribute_item->id, '"', $disabled , '>', $attribute_item->name, '</option>';
                            }

                            echo '</optgroup>';
                        }
                        ?>
                </select>
                <br /><br />
                <p><a href="#" class="button" id="add-attribute" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></p>
                <div id="attribute-items-list" class="list"><?php echo $attributes_html; ?></div>
            </div>
        </div>
    </div>

    <div id="main-container">
        <div id="main">
            <div id="name-container"><input type="text" name="tName" id="tName" class="tb" value="<?php echo $product->name; ?>" tmpval="<?php echo _('Product Name'); ?>" maxlength="200" /></div>
            <div id="dProductSlug">
                <span><strong><?php echo _('Link:'); ?></strong> <?php echo _('http://www.website.com/'); ?><span id="category-slug"><?php echo _('products'); ?></span>/ <input type="text" name="tProductSlug" id="tProductSlug" maxlength="50" class="tb" value="<?php echo $product->slug; ?>" /> /
            </div>
            <input type="hidden" name="hCategorySlug" id="hCategorySlug" maxlength="50" />

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
                                    $selected = ( $product->industry_id == $industry->id ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $industry->id; ?>"<?php echo $selected; ?>><?php echo ucwords( $industry->name ); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><div class="container"><input type="text" class="tb" name="tWeight" id="tWeight" value="<?php echo $product->weight; ?>" tmpval="<?php echo _('Weight'); ?>" /></div></td>
                    <td>
                        <select name="sCategory">
                            <option value="">-- <?php echo _('Select Category'); ?> --</option>
                            <?php
                            foreach ( $categories as $category ) {
                                $selected = ( $category->id == $product->category_id ) ? ' selected="selected"' : '';
                                ?>
                                <option value="<?php echo $category->id; ?>"<?php echo $selected; ?>><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
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
            </table>
            <br />
            <?php } ?>

            <h2><?php echo _('Upload Images'); ?></h2>
            <p id="pUploadImagesMessage"><?php echo _('You can upload up to 10 images per product. Please ensure images are at least 500px wide or tall as a minimum.'); ?></p>
            <p><a href="#" class="button" id="aUpload"><?php echo _('Upload'); ?></a></p>
        </div>
    </div>
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
        <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
        <input type="hidden" name="attributes[]" value="" />
    </div>
    <div class="specification item" id="product-spec-template">
        <div class="specification-name"></div>
        <div class="specification-value"></div>
        <a href="#" class="delete" title="<?php echo _('Delete'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete'); ?>" /></a>
        <input type="hidden" name="product-specs[]" value="" />
    </div>
</div>

<?php echo $template->end(); ?>