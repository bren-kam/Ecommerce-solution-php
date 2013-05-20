<?php
/**
 * @var User $user
 * @var $template Template
 */
?>
<div id="sidebar">
    <div id="actions">
        <a href="/products/" title="<?php echo _('Products'); ?>" class="top first<?php $template->select('sub-products'); ?>"><?php echo _('Products'); ?></a>
        <?php if ( isset( $products ) && true === $products ) { ?>
            <a href="/products/" title="<?php echo _('View Products'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <a href="/products/add-edit/" title="<?php echo _('Add Product'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php } ?>

        <a href="/products/categories/" title="<?php echo _('Categories'); ?>" class="top<?php $template->select('categories'); ?>"><?php echo _('Categories'); ?></a>
        <?php if ( isset( $categories ) && true === $categories ) { ?>
            <a href="/categories/" title="<?php echo _('View Categories'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
        <?php } ?>

        <a href="/products/attributes/" title="<?php echo _('Attributes'); ?>" class="top<?php $template->select('attributes'); ?>"><?php echo _('Attributes'); ?></a>
        <?php if ( isset( $attributes ) && true === $attributes ) { ?>
            <a href="/products/attributes/" title="<?php echo _('View Attributes'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <a href="/products/attributes/add-edit/" title="<?php echo _('Add Attribute'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php } ?>

        <a href="/products/brands/" title="<?php echo _('Brands'); ?>" class="top<?php $template->select('brands'); ?>"><?php echo _('Brands'); ?></a>
        <?php if ( isset( $brands ) && true === $brands ) { ?>
            <a href="/products/brands/" title="<?php echo _('View Brands'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <a href="/products/brands/add-edit/" title="<?php echo _('Add Brand'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php } ?>

        <a href="/products/product-options/" title="<?php echo _('Product Options'); ?>" class="top<?php $template->select('product_options'); ?>"><?php echo _('Product Options'); ?></a>
        <?php if ( isset( $product_options ) && true === $product_options ) { ?>
            <a href="/products/product-options/" title="<?php echo _('View Product Options'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <a href="/products/product-options/add-edit/" title="<?php echo _('Add Product Options'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php } ?>
    </div>
</div>