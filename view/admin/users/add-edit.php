<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a user
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( ( isset( $_GET['uid'] ) ? _('Edit User') : _('Add User') ) );
echo $form;
echo $template->end();
?>