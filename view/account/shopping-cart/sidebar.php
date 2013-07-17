<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/shopping-cart/orders/" title="<?php echo _('Orders'); ?>" class="top<?php $template->select('orders'); ?>"><?php echo _('Orders'); ?></a>
    <a href="/shopping-cart/users/" title="<?php echo _('Users'); ?>" class="top first<?php $template->select('users'); ?>"><?php echo _('Users'); ?></a>
	<a href="/shopping-cart/shipping/" title="<?php echo _('Shipping Settings'); ?>" class="top<?php $template->select('shipping'); ?>"><?php echo _('Shipping'); ?></a>
	<?php if ( $template->v('shipping') ) { ?>
		<a href="/shopping-cart/shipping/add-edit-custom/" title="<?php echo _('Add Custom'); ?>" class="sub<?php $template->select('add-edit-custom'); ?>"><?php echo _('Add Custom'); ?></a>
		<a href="/shopping-cart/shipping/add-edit-ups/" title="<?php echo _('Add UPS'); ?>" class="sub<?php $template->select('add-edit-ups'); ?>"><?php echo _('Add UPS'); ?></a>
		<a href="/shopping-cart/shipping/add-edit-fedex/" title="<?php echo _('Add FedEx'); ?>" class="sub<?php $template->select('add-edit-fedex'); ?>"><?php echo _('Add FedEx'); ?></a>
		<a href="/shopping-cart/shipping/settings/" title="<?php echo _('Settings'); ?>" class="sub<?php $template->select('shipping-settings'); ?>"><?php echo _('Settings'); ?></a>
	<?php } ?>

    <a href="/shopping-cart/coupons/" title="<?php echo _('Coupons'); ?>" class="top<?php $template->select('coupons'); ?>"><?php echo _('Coupons'); ?></a>
    <?php if ( $template->v('coupons') ) { ?>
        <a href="/shopping-cart/coupons/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
        <a href="/shopping-cart/coupons/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
    <?php } ?>

    <a href="/shopping-cart/settings/" title="<?php echo _('Settings'); ?>" class="top<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
	<?php if ( $template->v('settings') ) { ?>
    	<a href="/shopping-cart/settings/" title="<?php echo _('General Settings'); ?>" class="sub<?php $template->select('general'); ?>"><?php echo _('General'); ?></a>
		<a href="/shopping-cart/settings/payment-gateway/" title="<?php echo _('Payment Gateway Settings'); ?>" class="sub<?php $template->select('payment-gateways'); ?>"><?php echo _('Payment Gateways'); ?></a>
		<a href="/shopping-cart/settings/taxes/" title="<?php echo _('Tax Settings'); ?>" class="sub<?php $template->select('tax-settings'); ?>"><?php echo _('Taxes'); ?></a>
	<?php } ?>
</div>