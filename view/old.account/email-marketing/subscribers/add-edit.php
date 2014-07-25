<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Subscribers | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Email $email
 * @var string $form
 */

$title = ( $email->id ) ? _('Edit') : _('Add');
echo $template->start( $title . ' ' . _('Subscriber'), '../sidebar' );
echo $form;
echo $template->end();
?>