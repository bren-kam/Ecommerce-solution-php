<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - Settings
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( _('Shipping Settings') , '../sidebar' );
echo $form;
echo $template->end();
?>
