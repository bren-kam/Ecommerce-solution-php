<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Company
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( '', '../sidebar' );
echo $template->v('form');
echo $template->end();
?>