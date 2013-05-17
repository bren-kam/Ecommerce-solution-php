<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit an Article
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( ( isset( $_GET['kbaid'] ) ? _('Edit Article') : _('Add Article') ), '../sidebar' );
echo $form;
nonce::field( 'get_categories', '_get_categories' );
nonce::field( 'get_pages', '_get_pages' );
echo $template->end();
?>