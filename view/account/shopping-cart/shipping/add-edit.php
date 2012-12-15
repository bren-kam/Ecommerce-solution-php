<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - Add/Edit Custom/UPS/FedEx
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var WebsiteShippingMethod $shipping_method
 */

$title = ( $shipping_method->id ) ? _('Edit') . " $type " . _('Shipping Method') : _('Add') . " $type " . ('Shipping Method');
echo $template->start( $title, '../sidebar' );
echo $form;
echo $template->end();
?>
