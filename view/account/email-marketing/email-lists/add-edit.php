<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Email Lists | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailList $email_list
 * @var string $form
 */

$title = ( $email_list->id ) ? _('Edit') : _('Add');
echo $template->start( $title . ' ' . _('Email List'), '../sidebar' );
echo $form;
echo $template->end();
?>