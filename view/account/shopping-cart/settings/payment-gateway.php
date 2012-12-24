<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - Payment Gateway
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( _('Payment Gateway'), '../sidebar' );
echo $form;
echo $template->end();
?>