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
        <a href="/products/product-prices/" title="<?php echo _('Product Prices'); ?>" class="sub<?php $template->select('product-prices'); ?>"><?php echo _('Product Prices'); ?></a>
        <a href="/products/price-multiplier/" title="<?php echo _('Price Multiplier'); ?>" class="sub<?php $template->select('price-multiplier'); ?>"><?php echo _('Price Multiplier'); ?></a>
        <a href="/products/export/" title="<?php echo _('Export'); ?>" class="sub"><?php echo _('Export'); ?></a>
    <?php } ?>

    <a href="/products/reaches/" title="<?php echo _('Reaches'); ?>" class="top<?php $template->select('reaches'); ?>"><?php echo _('Reaches'); ?></a>
	<a href="/products/custom-products/" title="<?php echo _('Custom Products'); ?>" class="top<?php $template->select('custom-products'); ?>"><?php echo _('Custom Products'); ?></a>
	<?php if ( $template->v('custom-products') ) { ?>
		<a href="/products/custom-products/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
		<a href="/products/custom-products/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
	<?php } ?>

	<a href="/products/brands/" title="<?php echo _('Top Brands'); ?>" class="top<?php $template->select('brands'); ?>"><?php echo _('Top Brands'); ?></a>

	<?php if ( $user->account->shopping_cart ) { ?>
		<a href="/products/coupons/" title="<?php echo _('Coupons'); ?>" class="top<?php $template->select('coupons'); ?>"><?php echo _('Coupons'); ?></a>
		<?php if ( true === $template->v('coupons') ) { ?>
			<a href="/products/coupons/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
			<a href="/products/coupons/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
		<?php
		}
	}
	?>

	<a href="/products/groups/" title="<?php echo _('Product Groups'); ?>" class="top<?php $template->select('product-groups'); ?>"><?php echo _('Products Groups'); ?></a>
	<?php if ( $template->v('product-groups') ) { ?>
		<a href="/products/groups/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
		<a href="/products/groups/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a></li>
	<?php } ?>

	<a href="/products/settings/" title="<?php echo _('Settings'); ?>" class="top last<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
</div>