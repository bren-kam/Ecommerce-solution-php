<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/products/" title="<?php echo _('Products'); ?>" class="top first<?php $template->select('sub-products'); ?>"><?php echo _('Products'); ?></a>
    <?php if ( $template->v('sub-products') ) { ?>
        <a href="/products/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
        <?php if ( $user->has_permission( User::ROLE_MARKETING_SPECIALIST ) || '1' != $user->account->get_settings('limited-products') ) { ?>
            <a href="/products/add/" title="<?php echo _('Add Product'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php } ?>

        <a href="/products/all/" title="<?php echo _('All Products'); ?>" class="sub view<?php $template->select('all'); ?>"><?php echo _('All Products'); ?></a>

        <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
            <a href="/products/catalog-dump/" title="<?php echo _('Catalog Dump'); ?>" class="sub add<?php $template->select('catalog-dump'); ?>"><?php echo _('Catalog Dump'); ?></a>
        <?php } ?>

        <a href="/products/add-bulk/" title="<?php echo _('Add Bulk'); ?>" class="sub add<?php $template->select('add-bulk'); ?>"><?php echo _('Add Bulk'); ?></a>
        <a href="/products/block-products/" title="<?php echo _('Block Products'); ?>" class="sub<?php $template->select('block-products'); ?>"><?php echo _('Block Products'); ?></a>
        <a href="/products/hide-categories/" title="<?php echo _('Hide Categories'); ?>" class="sub<?php $template->select('hide-categories'); ?>"><?php echo _('Hide Categories'); ?></a>
        <a href="/products/auto-price/" title="<?php echo _('Pricing Tools'); ?>" class="sub<?php $template->select('pricing-tools'); ?>"><?php echo _('Pricing Tools'); ?></a>
        <a href="/products/import/" title="<?php echo _('Import'); ?>" class="sub<?php $template->select('import'); ?>""><?php echo _('Import'); ?></a>
        <a href="/products/export/" title="<?php echo _('Export'); ?>" class="sub"><?php echo _('Export'); ?></a>
    <?php } ?>

    <a href="/products/reaches/" title="<?php echo _('Reaches'); ?>" class="top<?php $template->select('reaches'); ?>"><?php echo _('Reaches'); ?></a>
	<a href="/products/product-builder/" title="<?php echo _('Product Builder'); ?>" class="top<?php $template->select('product-builder'); ?>"><?php echo _('Product Builder'); ?></a>
	<?php if ( $template->v('product-builder') ) { ?>
		<a href="/products/product-builder/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
		<a href="/products/product-builder/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
	<?php } ?>

	<a href="/products/brands/" title="<?php echo _('Top Brands'); ?>" class="top<?php $template->select('brands'); ?>"><?php echo _('Top Brands'); ?></a>
	<a href="/products/top-categories/" title="<?php echo _('Top Categories'); ?>" class="top<?php $template->select('top-categories'); ?>"><?php echo _('Top Categories'); ?></a>

	<a href="/products/related-products/" title="<?php echo _('Related Products'); ?>" class="top<?php $template->select('related-products'); ?>"><?php echo _('Related Products'); ?></a>
	<?php if ( $template->v('related-products') ) { ?>
		<a href="/products/related-products/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
		<a href="/products/related-products/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
	<?php } ?>

	<a href="/products/settings/" title="<?php echo _('Settings'); ?>" class="top last<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
</div>