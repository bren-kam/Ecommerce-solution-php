<?php
global $user;
$w = new Websites;

$settings = $w->get_settings('limited-products');
$high_impact = 'High Impact' == $user['website']['type'];
$brands_name = ( $high_impact ) ? _('Brands') : _('Top Brands');
?>
<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<?php if ( !$high_impact ) { ?>
		<a href="/products/" title="<?php echo _('Products'); ?>" class="top"><?php echo _('Products'); ?></a>
		<?php if ( isset( $products ) ) { ?>
			<a href="/products/" title="<?php echo _('View Products'); ?>" class="sub"><?php echo _('View'); ?></a>
			<?php if ( $user['role'] >= 6 || '1' != $settings['limited-products'] ) { ?>
				<a href="/products/add/" title="<?php echo _('Add Product'); ?>" class="sub"><?php echo _('Add'); ?></a>
			<?php } ?>
			
			<a href="/products/all-products/" title="<?php echo _('All Products'); ?>" class="sub"><?php echo _('All Products'); ?></a>
			
			<?php if ( $user['role'] >= 7 ) { ?>
				<a href="/products/catalog-dump/" title="<?php echo _('Catalog Dump'); ?>" class="sub"><?php echo _('Catalog Dump'); ?></a>
			<?php } ?>

            <a href="/products/add-bulk/" title="<?php echo _('Add Bulk'); ?>" class="sub"><?php echo _('Add Bulk'); ?></a>
            <a href="/products/block-products/" title="<?php echo _('Block Products'); ?>" class="sub"><?php echo _('Block Products'); ?></a>
            <a href="/products/product-prices/" title="<?php echo _('Product Prices'); ?>" class="sub"><?php echo _('Product Prices'); ?></a>
            <?php
		}
	}
	?>
	
	<a href="/reaches/" title="<?php echo _('Reaches'); ?>" class="top"><?php echo _('Reaches'); ?></a>
	<a href="/products/custom-products/" title="<?php echo _('Custom Products'); ?>" class="top"><?php echo _('Custom Products'); ?></a>
	<?php if ( isset( $custom_products ) ) { ?>
		<a href="/products/custom-products/" title="<?php echo _('View'); ?>" class="sub"><?php echo _('View'); ?></a>
		<a href="/products/custom-products/add-edit/" title="<?php echo _('Add'); ?>" class="sub"><?php echo _('Add'); ?></a>
	<?php } ?>
	
	<a href="/products/brands/" title="<?php echo $brands_name; ?>" class="top"><?php echo $brands_name; ?></a>
	
	<?php if ( $user['website']['shopping_cart'] ) { ?>
		<a href="/products/coupons/" title="<?php echo _('Coupons'); ?>" class="top"><?php echo _('Coupons'); ?></a>
		<?php if ( isset( $coupons ) ) { ?>
			<a href="/products/coupons/" title="<?php echo _('View Coupons'); ?>" class="sub"><?php echo _('View'); ?></a>
			<a href="/products/coupons/add-edit/" title="<?php echo _('Add Coupon'); ?>" class="sub"><?php echo _('Add'); ?></a>
		<?php 
		}
	}
	?>
	
	<a href="/products/groups/" title="<?php echo _('Product Groups'); ?>" class="top"><?php echo _('Products Groups'); ?></a>
	<?php if ( isset( $product_groups ) ) { ?>
		<a href="/products/groups/" title="<?php echo _('View Product Groups'); ?>" class="sub"><?php echo _('View'); ?></a>
		<a href="/products/groups/add-edit/" title="<?php echo _('Add Product Groups'); ?>" class="sub"><?php echo _('Add'); ?></a></li>
	<?php } ?>
	
	<a href="/products/settings/" title="<?php echo _('Settings'); ?>" class="top"><?php echo _('Settings'); ?></a>
</div>