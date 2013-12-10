<?php
/**
 * @package Grey Suit Retail
 * @page Discontinue Ashley Products By SKUs
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( _('Discontinue Ashley Products'), '../sidebar' );
echo $form;
echo $template->end();
?>