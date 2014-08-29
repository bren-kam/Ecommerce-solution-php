<?php
/**
 * @package Grey Suit Retail
 * @page Choose | Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var SocialMediaFacebookPage $page
 * @var bool $has_permission
 * @var string $form
 */

$title = ( $page->id ) ? _('Edit') : _('Add');
echo $template->start( $title . ' ' . _('Facebook Page'), 'sidebar' );

if ( $has_permission ) {
    echo $form;
} else {
    echo '<p>', _('You have reached your maximum amount of facebook pages, please see your online specialist about getting more.'), '</p>';
}

echo $template->end();
?>