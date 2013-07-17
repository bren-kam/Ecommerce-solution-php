<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - Payment Gateways
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( _('Payment Gateways'), '../sidebar' );
echo $form;
echo $template->end();
?>