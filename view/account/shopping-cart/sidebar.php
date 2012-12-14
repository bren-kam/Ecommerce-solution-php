<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/shopping-cart/users/" title="<?php echo _('Users'); ?>" class="top first<?php $template->select('users'); ?>"><?php echo _('Users'); ?></a>
    <a href="/shopping-cart/orders/" title="<?php echo _('Orders'); ?>" class="top<?php $template->select('orders'); ?>"><?php echo _('Orders'); ?></a>
	<a href="/shopping-cart/shipping/" title="<?php echo _('Shipping Settings'); ?>" class="top<?php $template->select('shipping'); ?>"><?php echo _('Shipping'); ?></a>
	<?php if ( $template->v('shipping') ) { ?>
		<a href="/shopping-cart/shipping/add-edit-custom/" title="<?php echo _('Add Custom'); ?>" class="sub<?php $template->select('add-custom'); ?>"><?php echo _('Add Custom'); ?></a>
		<a href="/shopping-cart/shipping/add-edit-ups/" title="<?php echo _('Add UPS'); ?>" class="sub<?php $template->select('add-ups'); ?>"><?php echo _('Add UPS'); ?></a>
		<a href="/shopping-cart/shipping/add-edit-fedex/" title="<?php echo _('Add FedEx'); ?>" class="sub<?php $template->select('add-fedex'); ?>"><?php echo _('Add FedEx'); ?></a>
		<a href="/shopping-cart/shipping/settings/" title="<?php echo _('Settings'); ?>" class="sub<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
	<?php } ?>

    <a href="/shopping-cart/settings/" title="<?php echo _('Settings'); ?>" class="top"><?php echo _('Settings'); ?></a>
	<?php if ( $template->v('settings') ) { ?>
    	<a href="/shopping-cart/settings/" title="<?php echo _('General Settings'); ?>" class="sub<?php $template->select('general'); ?>"><?php echo _('General'); ?></a>
		<a href="/shopping-cart/settings/payment-gateway/" title="<?php echo _('Payment Gateway Settings'); ?>" class="sub<?php $template->select('payment-gateway'); ?>"><?php echo _('Payment Gateway'); ?></a>
		<a href="/shopping-cart/settings/taxes/" title="<?php echo _('Tax Settings'); ?>" class="sub<?php $template->select('taxes'); ?>"><?php echo _('Taxes'); ?></a>
	<?php } ?>
</div>