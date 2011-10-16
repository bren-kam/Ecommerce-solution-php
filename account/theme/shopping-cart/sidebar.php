<div id="sidebar">
	<h2><?php echo _('Actions'); ?></h2>
	<a href="/shopping-cart/users/" title="<?php echo _('Users'); ?>" class="top"><?php echo _('Users'); ?></a>
	<?php if( $shopping_cart ) { ?>
    	<a href="/shopping-cart/users/" title="<?php echo _('View Users'); ?>" class="sub"><?php echo _('View'); ?></a>
		<a href="/shopping-cart/users/add/" title="<?php echo _('Add User'); ?>" class="sub"><?php echo _('Add'); ?></a>
	<?php } ?>
    
    <a href="/shopping-cart/orders/" title="<?php echo _('Orders'); ?>" class="top"><?php echo _('Orders'); ?></a>
	<?php if( $orders && 1 == 2 ) { ?>
		<a href="/ajax/shopping-cart/export/" title="<?php echo _('Export Orders'); ?>" class="sub"><?php echo _('Export'); ?></a>
	<?php 
	} 
	
	if( $view_order ) {
	?>
		<a href="/shopping-cart/orders/" title="<?php echo _('View Order'); ?>" class="sub"><?php echo _('View'); ?></a>
	<?php } ?>

	<a href="/shopping-cart/shipping/" title="<?php echo _('Shipping Settings'); ?>" class="top"><?php echo _('Shipping'); ?></a>
	<?php if( $shipping ) { ?>
		<a href="/shopping-cart/shipping/add-edit-custom/" title="<?php echo _('Add Custom'); ?>" class="sub"><?php echo _('Add Custom'); ?></a>
		<a href="/shopping-cart/shipping/add-edit-ups/" title="<?php echo _('Add UPS'); ?>" class="sub"><?php echo _('Add UPS'); ?></a>
		<a href="/shopping-cart/shipping/add-edit-fedex/" title="<?php echo _('Add FedEx'); ?>" class="sub"><?php echo _('Add FedEx'); ?></a>
		<?php /* @fix: Need to figure it out later ?> <a href="/shopping-cart/shipping/add-edit-usps/" title="<?php echo _('Add USPS'); ?>" class="sub"><?php echo _('Add USPS'); ?></a> <?php */?>
		<a href="/shopping-cart/shipping/settings/" title="<?php echo _('Settings'); ?>" class="sub"><?php echo _('Settings'); ?></a>
	<?php } ?>
	
    <a href="/shopping-cart/settings/" title="<?php echo _('Settings'); ?>" class="top"><?php echo _('Settings'); ?></a>
	<?php if( $settings ) { ?>
    	<a href="/shopping-cart/settings/" title="<?php echo _('General Settings'); ?>" class="sub"><?php echo _('General'); ?></a>
		<a href="/shopping-cart/settings/payment-gateway/" title="<?php echo _('Payment Gateway Settings'); ?>" class="sub"><?php echo _('Payment Gateway'); ?></a>
		<a href="/shopping-cart/settings/taxes/" title="<?php echo _('Tax Settings'); ?>" class="sub"><?php echo _('Taxes'); ?></a>
	<?php } ?>
</div>