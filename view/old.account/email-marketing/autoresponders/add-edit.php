<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Autoresponder | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailAutoresponder $email_autoresponder
 * @var string $form
 */

$title = ( $email_autoresponder->id ) ? _('Edit') : _('Add');
echo $template->start( $title . ' ' . _('Autoresponder'), '../sidebar' );
echo $form;
echo $template->end();
?>