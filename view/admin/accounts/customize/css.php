<?php
/**
 * @package Grey Suit Retail
 * @page CSS | Customize
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('CSS'), '../sidebar' );
echo $template->v('form');
echo $template->end();
?>