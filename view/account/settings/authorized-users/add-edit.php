<?php
/**
 * @package Grey Suit Retail
 * @page Authorized Users | Settings | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var AuthUserWebsite $auth_user_website
 */

$title = ( $auth_user_website->user_id ) ? _('Edit') : _('Add');
echo $template->start( $title . ' ' . _('Authorized User'), '../sidebar' );
echo $form;
echo $template->end(); ?>