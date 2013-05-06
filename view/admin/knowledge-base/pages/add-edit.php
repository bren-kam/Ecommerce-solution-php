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

echo $template->start( ( isset( $_GET['kbpid'] ) ? _('Edit Page') : _('Add Page') ), '../sidebar' );
echo $form;
nonce::field( 'get_categories', '_get_categories' );
echo $template->end();
?>