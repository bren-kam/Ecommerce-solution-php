<?php
/**
 * @package Grey Suit Retail
 * @page Account > Customize > Settings
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var string $form
 */

echo $template->start( _('Settings') );
echo $template->v('form');
echo $template->end();
?>